<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GiaPha;
use App\Models\GiaPhaMg;
use Illuminate\Support\Facades\DB;

class FastImportGiaPhaMg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fast-import:giaphamg {--batch=5000 : Number of records per batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Super fast import from MySQL GiaPha to MongoDB (no duplicate checking)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 FAST IMPORT: MySQL GiaPha → MongoDB GiaPhaMg');
        $this->info('===============================================');
        $this->warn('⚠️  This will NOT check for duplicates!');
        
        if (!$this->confirm('Continue with fast import?')) {
            return Command::SUCCESS;
        }

        try {
            $totalRecords = GiaPha::count();
            $batchSize = (int) $this->option('batch');
            
            $this->info("📊 Total records: {$totalRecords}");
            $this->info("📦 Batch size: {$batchSize}");
            $this->info("⏱️  Estimated time: " . ceil($totalRecords / $batchSize / 10) . " minutes");

            $bar = $this->output->createProgressBar($totalRecords);
            $bar->start();

            $imported = 0;
            $startTime = microtime(true);

            GiaPha::select('*')->chunk($batchSize, function ($records) use (&$imported, $bar) {
                $batchData = [];
                
                foreach ($records as $record) {
                    $data = $record->toArray();
                    
                    // Keep MySQL ID as idsql
                    if (isset($data['id'])) {
                        $data['idsql'] = $data['id'];
                        unset($data['id']);
                    }
                    
                    // Add timestamps
                    if (!isset($data['created_at'])) {
                        $data['created_at'] = now();
                    }
                    if (!isset($data['updated_at'])) {
                        $data['updated_at'] = now();
                    }
                    
                    $batchData[] = $data;
                    $bar->advance();
                }

                // Bulk insert using Laravel MongoDB
                if (!empty($batchData)) {
                    try {
                        // Use model's insert method for bulk insert
                        GiaPhaMg::insert($batchData);
                        $imported += count($batchData);
                    } catch (\Exception $e) {
                        // Fallback to individual inserts
                        foreach ($batchData as $data) {
                            try {
                                GiaPhaMg::create($data);
                                $imported++;
                            } catch (\Exception $e2) {
                                // Skip failed records
                            }
                        }
                    }
                }
            });

            $bar->finish();
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            $this->newLine(2);
            $this->info('✅ Fast import completed!');
            $this->info("📈 Imported: {$imported} records");
            $this->info("⏱️  Duration: " . round($duration, 2) . " seconds");
            $this->info("🚀 Speed: " . round($imported / $duration, 2) . " records/second");
            
            $mongoCount = GiaPhaMg::count();
            $this->info("📊 MongoDB total: {$mongoCount} records");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Fast import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 