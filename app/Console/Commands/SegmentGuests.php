<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SegmentGuests extends Command
{
    protected $signature = 'guests:segment';

    protected $description = 'Classify guests into segments using Rule-Based Decision Tree algorithm';

    public function handle(): void
    {
        $this->info('Running guest segmentation...');

        $guests = DB::select("
            SELECT
                u.id AS user_id,
                COUNT(r.id)                                                        AS visit_count,
                COALESCE(SUM(p.amount), 0)                                        AS total_spend,
                COALESCE(DATEDIFF(CURDATE(), MAX(r.check_out)), 9999)             AS days_since_last_visit,
                COALESCE(
                    SUM(CASE WHEN r.status = 'cancelled' THEN 1 ELSE 0 END)
                    / NULLIF(COUNT(r.id), 0), 0
                )                                                                  AS cancellation_rate,
                COUNT(fo.id)                                                       AS food_order_count
            FROM users u
            LEFT JOIN reservations r  ON r.user_id = u.id
            LEFT JOIN payments p      ON p.reservation_id = r.id AND p.status = 'completed'
            LEFT JOIN food_orders fo  ON fo.reservation_id = r.id AND fo.status = 'delivered'
            WHERE u.role = 'guest'
            GROUP BY u.id
        ");

        $counts = ['vip' => 0, 'loyal' => 0, 'at_risk' => 0, 'high_value_new' => 0, 'unreliable' => 0, 'regular' => 0];

        foreach ($guests as $guest) {
            $segment = $this->classify(
                (float) $guest->total_spend,
                (int) $guest->visit_count,
                (int) $guest->days_since_last_visit,
                (float) $guest->cancellation_rate
            );

            DB::table('users')->where('id', $guest->user_id)->update(['segment' => $segment]);
            $counts[$segment]++;
        }

        $this->table(
            ['Segment', 'Guests'],
            collect($counts)->map(fn ($n, $s) => [$s, $n])->values()->toArray()
        );

        $this->info('Segmentation complete. '.array_sum($counts).' guests classified.');
    }

    private function classify(float $spend, int $visits, int $daysSince, float $cancellationRate): string
    {
        // Rule 1: VIP — high spend + frequent visitor
        if ($spend >= 10000 && $visits >= 5) {
            return 'vip';
        }

        // Rule 2: Loyal — repeat visitor with low cancellation rate
        if ($visits >= 3 && $cancellationRate < 0.2) {
            return 'loyal';
        }

        // Rule 3: At Risk — used to visit but now inactive
        if ($daysSince > 90 && $visits >= 2) {
            return 'at_risk';
        }

        // Rule 4: High Value New — first-time but high spend
        if ($visits == 1 && $spend >= 5000) {
            return 'high_value_new';
        }

        // Rule 5: Unreliable — cancels often
        if ($cancellationRate >= 0.5) {
            return 'unreliable';
        }

        // Default
        return 'regular';
    }
}
