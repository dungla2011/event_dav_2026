<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GiaPha;
use App\Models\GiaPhaMg;

class CheckGiaPhaMgData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:giaphamg {--sample=5 : Number of sample records to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check GiaPhaMg data after import';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Checking GiaPhaMg Data');
        $this->info('========================');

        try {
            // Count records
            $mysqlCount = GiaPha::count();
            $mongoCount = GiaPhaMg::count();
            
            $this->info("📊 MySQL GiaPha records: {$mysqlCount}");
            $this->info("📊 MongoDB GiaPhaMg records: {$mongoCount}");
            
            if ($mongoCount > 0) {
                $percentage = round(($mongoCount / $mysqlCount) * 100, 2);
                $this->info("📈 Import progress: {$percentage}%");
            }

            // Check for idsql field
            $this->info("\n🔍 Checking idsql field...");
            $withIdsql = GiaPhaMg::whereNotNull('idsql')->count();
            $this->info("📋 Records with idsql: {$withIdsql}");

            if ($withIdsql > 0) {
                // Show sample records
                $sampleCount = (int) $this->option('sample');
                $this->info("\n📝 Sample records:");
                
                $samples = GiaPhaMg::take($sampleCount)->get();
                
                $headers = ['MongoDB _id', 'idsql (MySQL ID)', 'Other Fields'];
                $rows = [];
                
                foreach ($samples as $record) {
                    $otherFields = collect($record->toArray())
                        ->except(['_id', 'idsql', 'created_at', 'updated_at'])
                        ->take(3)
                        ->map(function($value, $key) {
                            return "{$key}: " . (is_string($value) ? substr($value, 0, 20) : $value);
                        })
                        ->implode(', ');
                    
                    $rows[] = [
                        substr($record->_id, 0, 12) . '...',
                        $record->idsql ?? 'NULL',
                        $otherFields
                    ];
                }
                
                $this->table($headers, $rows);

                // Check for duplicates
                $this->info("\n🔍 Checking for duplicates...");
                $duplicates = GiaPhaMg::selectRaw('idsql, count(*) as count')
                    ->whereNotNull('idsql')
                    ->groupBy('idsql')
                    ->having('count', '>', 1)
                    ->count();
                
                if ($duplicates > 0) {
                    $this->warn("⚠️  Found {$duplicates} duplicate idsql values");
                } else {
                    $this->info("✅ No duplicates found");
                }

                // Show idsql range
                $minIdsql = GiaPhaMg::whereNotNull('idsql')->min('idsql');
                $maxIdsql = GiaPhaMg::whereNotNull('idsql')->max('idsql');
                $this->info("📊 idsql range: {$minIdsql} - {$maxIdsql}");

            } else {
                $this->warn("⚠️  No records found with idsql field");
            }

            $this->newLine();
            $this->info('🎯 Usage examples:');
            $this->info('- Find by SQL ID: GiaPhaMg::where("idsql", 123)->first()');
            $this->info('- Using scope: GiaPhaMg::bySqlId(123)->first()');
            $this->info('- Count with idsql: GiaPhaMg::whereNotNull("idsql")->count()');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Check failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 