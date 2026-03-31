<?php

namespace App\Console\Commands;

use App\Services\GuestSegmentationEngine;
use Illuminate\Console\Command;

class SegmentAllGuests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guests:segment {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Segment all guests based on their booking and payment history';

    /**
     * Execute the console command.
     */
    public function handle(GuestSegmentationEngine $engine): int
    {
        if (!$this->option('force') && !$this->confirm('This will segment all guests. Continue?')) {
            $this->info('Command cancelled.');
            return 1;
        }

        $this->info('🔄 Starting guest segmentation...');
        $startTime = now();

        $results = $engine->segmentAllGuests();

        $successful = collect($results)->where('status', 'success')->count();
        $failed = collect($results)->where('status', 'failed')->count();
        $totalTime = now()->diffInSeconds($startTime);

        $this->newLine();
        $this->info('✓ Segmentation completed!');
        $this->newLine();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Guests', count($results)],
                ['Successful', $successful],
                ['Failed', $failed],
                ['Time Taken', "{$totalTime}s"],
            ]
        );

        // Show summary
        $summary = $engine->getSegmentationSummary();
        $this->newLine();
        $this->info('📊 Segmentation Summary:');

        $this->table(
            ['Segment', 'Count'],
            [
                ['VIP', $summary['by_segment']['vip']],
                ['LOYAL', $summary['by_segment']['loyal']],
                ['BUSINESS', $summary['by_segment']['business']],
                ['LEISURE', $summary['by_segment']['leisure']],
                ['BUDGET', $summary['by_segment']['budget']],
                ['RISK', $summary['by_segment']['risk']],
                ['REGULAR', $summary['by_segment']['regular']],
            ]
        );

        return 0;
    }
}
