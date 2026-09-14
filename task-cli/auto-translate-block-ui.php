<?php
/**
 * Auto Translate BlockUi Model Fields
 * 
 * Tự động dịch các trường JSON đa ngôn ngữ trong database cho model BlockUi
 * Sử dụng Google Translate API (free endpoint)
 * 
 * Usage:
 *   php task-cli/auto-translate-block-ui.php
 *   php task-cli/auto-translate-block-ui.php --dry-run
 *   php task-cli/auto-translate-block-ui.php --id=123
 *   php task-cli/auto-translate-block-ui.php --lang=ja,ko
 *   php task-cli/auto-translate-block-ui.php --force
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

require_once __DIR__ . '/../app/common.php';

class AutoTranslateBlockUi
{
    private $dryRun = false;
    private $targetId = null;
    private $targetLangs = null;
    private $force = false;
    private $stats = [
        'records_processed' => 0,
        'fields_translated' => 0,
        'fields_skipped' => 0,
        'fields_forced' => 0,
        'errors' => 0,
    ];

    public function __construct($options = [])
    {
        $this->dryRun = $options['dry-run'] ?? false;
        $this->targetId = $options['id'] ?? null;
        $this->targetLangs = $options['lang'] ?? null;
        $this->force = $options['force'] ?? false;
    }

    public function run()
    {
        echo "=================================================\n";
        echo "   Auto Translate BlockUi Model\n";
        echo "=================================================\n\n";

        if ($this->dryRun) {
            echo "⚠️  DRY RUN MODE - No database will be modified\n\n";
        }

        if ($this->force) {
            echo "🔥 FORCE MODE - Will re-translate existing translations\n\n";
        }

        // Lấy danh sách ngôn ngữ từ clang1
        $allLanguages = clang1::getLanguageListKey();
        echo "📋 Supported languages: " . implode(', ', $allLanguages) . "\n\n";

        // Lọc ngôn ngữ nếu có --lang
        if ($this->targetLangs) {
            $targetLangList = array_map('trim', explode(',', $this->targetLangs));
            $allLanguages = array_intersect($allLanguages, $targetLangList);
            echo "🎯 Filtered languages: " . implode(', ', $allLanguages) . "\n\n";
        }

        // Tạo instance BlockUi để lấy translatable fields
        $blockUi = new \App\Models\BlockUi();
        $translatableFields = $blockUi->getTranslatableAttributes();
        
        if (empty($translatableFields)) {
            echo "⚠️  Warning: No translatable fields found. Make sure multi-language is enabled.\n";
            return;
        }

        echo "📝 Translatable fields: " . implode(', ', $translatableFields) . "\n\n";

        // Lấy danh sách records
        $query = \App\Models\BlockUi::query();
        
        if ($this->targetId) {
            $query->where('id', $this->targetId);
            echo "🎯 Filtering to ID: {$this->targetId}\n\n";
        }

        $records = $query->get();
        
        if ($records->isEmpty()) {
            echo "⚠️  No records found.\n";
            return;
        }

        echo "📊 Found " . $records->count() . " record(s) to process\n\n";

        // Xử lý từng record
        foreach ($records as $record) {
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "📄 Processing ID: {$record->id} - {$record->sname}\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

            $this->stats['records_processed']++;
            $updated = false;

            // Dịch từng field
            foreach ($translatableFields as $field) {
                $currentValue = $record->$field;
                
                // Nếu không phải JSON hoặc null, skip
                if (empty($currentValue)) {
                    echo "  ⏭️  Field '$field': Empty, skipped\n";
                    continue;
                }

                // Decode JSON
                $jsonData = is_string($currentValue) ? json_decode($currentValue, true) : $currentValue;
                
                if (!is_array($jsonData)) {
                    echo "  ⏭️  Field '$field': Not JSON format, skipped\n";
                    continue;
                }

                // Kiểm tra có 'en' không
                if (!isset($jsonData['en']) || empty($jsonData['en'])) {
                    echo "  ⏭️  Field '$field': No 'en' key found, skipped\n";
                    continue;
                }

                $sourceText = $jsonData['en'];
                echo "  📝 Field '$field': Source (EN) = " . mb_substr($sourceText, 0, 50) . "...\n";

                // Dịch sang các ngôn ngữ
                $needsUpdate = false;
                
                foreach ($allLanguages as $lang) {
                    if ($lang === 'en') {
                        continue; // Skip EN
                    }

                    // Kiểm tra đã có translation chưa
                    if (isset($jsonData[$lang]) && !empty($jsonData[$lang]) && !$this->force) {
                        echo "     ⏭️  $lang: Already exists, skipped\n";
                        $this->stats['fields_skipped']++;
                        continue;
                    }

                    // Dịch
                    echo "     🌍 $lang: Translating... ";
                    $translated = $this->translateText($sourceText, $lang);
                    
                    if ($translated !== false) {
                        $jsonData[$lang] = $translated;
                        $needsUpdate = true;
                        
                        if ($this->force && isset($jsonData[$lang])) {
                            $this->stats['fields_forced']++;
                        } else {
                            $this->stats['fields_translated']++;
                        }
                        
                        echo "✅\n";
                    } else {
                        echo "❌ Error\n";
                        $this->stats['errors']++;
                    }

                    // Delay để tránh rate limit
                    usleep(200000); // 200ms
                }

                // Cập nhật record nếu có thay đổi
                if ($needsUpdate) {
                    if (!$this->dryRun) {
                        $record->$field = $jsonData;
                        $updated = true;
                    }
                    echo "     ✅ Field '$field' updated\n";
                }

                echo "\n";
            }

            // Save record
            if ($updated && !$this->dryRun) {
                $record->save();
                echo "  💾 Record saved\n";
            } elseif ($updated && $this->dryRun) {
                echo "  ⚠️  Record NOT saved (dry-run mode)\n";
            } else {
                echo "  ⏭️  No changes needed\n";
            }

            echo "\n";
        }

        // Print statistics
        $this->printStats();
    }

    /**
     * Dịch text bằng Google Translate API
     * Tái sử dụng logic từ auto-translate-multi-language.php
     */
    private function translateText($text, $targetLang)
    {
        if (empty(trim($text))) {
            return $text;
        }

        // Nếu có newline, chia và dịch từng phần
        if (strpos($text, "\n") !== false) {
            $parts = explode("\n", $text);
            $translatedParts = [];
            
            foreach ($parts as $part) {
                if (empty(trim($part))) {
                    $translatedParts[] = $part;
                } else {
                    $translated = $this->translateSingleText($part, $targetLang);
                    $translatedParts[] = $translated !== false ? $translated : $part;
                    usleep(150000);
                }
            }
            
            return implode("\n", $translatedParts);
        }
        
        return $this->translateSingleText($text, $targetLang);
    }

    /**
     * Dịch một đoạn text đơn
     */
    private function translateSingleText($text, $targetLang)
    {
        if (empty(trim($text))) {
            return $text;
        }

        $placeholders = [];
        $placeholderIndex = 0;
        
        // Bảo vệ [:word] format
        $textWithPlaceholders = preg_replace_callback(
            '/\[:([a-zA-Z_][a-zA-Z0-9_]*)\]/u',
            function($matches) use (&$placeholders, &$placeholderIndex) {
                $placeholder = $matches[0];
                $token = "___PLACEHOLDER_{$placeholderIndex}___";
                $placeholders[$token] = $placeholder;
                $placeholderIndex++;
                return $token;
            },
            $text
        );
        
        // Bảo vệ :word format
        $textWithPlaceholders = preg_replace_callback(
            '/(?<!\S):([a-zA-Z_][a-zA-Z0-9_]*)/u',
            function($matches) use (&$placeholders, &$placeholderIndex) {
                $placeholder = $matches[0];
                $token = "___PLACEHOLDER_{$placeholderIndex}___";
                $placeholders[$token] = $placeholder;
                $placeholderIndex++;
                return $token;
            },
            $textWithPlaceholders
        );

        // Bảo vệ HTML tags
        $textWithPlaceholders = preg_replace_callback(
            '/<[^>]+>/u',
            function($matches) use (&$placeholders, &$placeholderIndex) {
                $tag = $matches[0];
                $token = "___HTMLTAG_{$placeholderIndex}___";
                $placeholders[$token] = $tag;
                $placeholderIndex++;
                return $token;
            },
            $textWithPlaceholders
        );

        $langMap = [
            'vi' => 'vi',
            'en' => 'en',
            'ja' => 'ja',
            'ko' => 'ko',
            'fr' => 'fr',
            'de' => 'de',
            'es' => 'es',
            'km' => 'km',
            'ru' => 'ru',
            'zh' => 'zh-CN',
        ];

        $googleLang = $langMap[$targetLang] ?? $targetLang;

        $url = "https://translate.googleapis.com/translate_a/single?" . http_build_query([
            'client' => 'gtx',
            'sl' => 'en',
            'tl' => $googleLang,
            'dt' => 't',
            'q' => $textWithPlaceholders,
        ]);

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$response) {
                return false;
            }

            $data = json_decode($response, true);
            
            if (isset($data[0]) && is_array($data[0])) {
                $translatedParts = [];
                
                foreach ($data[0] as $part) {
                    if (isset($part[0])) {
                        $translatedParts[] = $part[0];
                    }
                }
                
                $translated = implode('', $translatedParts);
                
                // Khôi phục placeholders
                foreach ($placeholders as $token => $placeholder) {
                    $translated = str_replace($token, $placeholder, $translated);
                }
                
                return $translated;
            }

            return false;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * In thống kê
     */
    private function printStats()
    {
        echo "=================================================\n";
        echo "   📊 TRANSLATION STATISTICS\n";
        echo "=================================================\n\n";
        
        echo "✅ Records processed:  {$this->stats['records_processed']}\n";
        echo "🌍 Fields translated:  {$this->stats['fields_translated']}\n";
        echo "⏭️  Fields skipped:     {$this->stats['fields_skipped']}\n";
        
        if ($this->force && $this->stats['fields_forced'] > 0) {
            echo "🔥 Fields forced:      {$this->stats['fields_forced']}\n";
        }
        
        echo "❌ Errors:             {$this->stats['errors']}\n\n";
        
        if ($this->dryRun) {
            echo "⚠️  DRY RUN - No database was actually modified\n\n";
        } else {
            echo "✅ Translation completed!\n\n";
        }
    }
}

// Parse command line arguments
$options = [];
for ($i = 1; $i < $argc; $i++) {
    if ($argv[$i] === '--dry-run') {
        $options['dry-run'] = true;
    } elseif ($argv[$i] === '--force') {
        $options['force'] = true;
    } elseif (strpos($argv[$i], '--id=') === 0) {
        $options['id'] = substr($argv[$i], 5);
    } elseif (strpos($argv[$i], '--lang=') === 0) {
        $options['lang'] = substr($argv[$i], 7);
    } elseif ($argv[$i] === '--help' || $argv[$i] === '-h') {
        echo "Usage: php auto-translate-block-ui.php [options]\n\n";
        echo "Options:\n";
        echo "  --dry-run         Show what would be translated without making changes\n";
        echo "  --force           Re-translate existing translations (overwrite)\n";
        echo "  --id=ID           Only translate specific record by ID\n";
        echo "  --lang=LANGS      Only translate to specific languages (comma-separated)\n";
        echo "  --help, -h        Show this help message\n\n";
        echo "Examples:\n";
        echo "  php auto-translate-block-ui.php\n";
        echo "  php auto-translate-block-ui.php --dry-run\n";
        echo "  php auto-translate-block-ui.php --force\n";
        echo "  php auto-translate-block-ui.php --id=123\n";
        echo "  php auto-translate-block-ui.php --lang=ja,ko\n";
        echo "  php auto-translate-block-ui.php --id=123 --lang=ja --force\n\n";
        exit(0);
    }
}

// Run
$translator = new AutoTranslateBlockUi($options);
$translator->run();
