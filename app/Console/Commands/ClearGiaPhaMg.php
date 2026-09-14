<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GiaPhaMg;

class ClearGiaPhaMg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:giaphamg {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all data from GiaPhaMg MongoDB collection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->warn('🗑️  CLEAR GiaPhaMg Collection');
        $this->warn('============================');
        
        try {
            $count = GiaPhaMg::count();
            $this->info("📊 Current records: {$count}");
            
            if ($count === 0) {
                $this->info('✅ Collection is already empty');
                return Command::SUCCESS;
            }

            if (!$this->option('force')) {
                if (!$this->confirm("⚠️  Are you sure you want to delete all {$count} records?")) {
                    $this->info('❌ Operation cancelled');
                    return Command::SUCCESS;
                }
            }

            $this->info('🗑️  Clearing collection...');
            GiaPhaMg::truncate();
            
            $newCount = GiaPhaMg::count();
            $this->info("✅ Collection cleared! Records: {$newCount}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Clear failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 