<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AlgorithmDemoController extends Controller
{
    public function index()
    {
        $segmentedGuests = DB::select("
            SELECT
                u.id,
                u.name,
                u.email,
                u.segment,
                COUNT(r.id)                                                         AS visit_count,
                COALESCE(SUM(p.amount), 0)                                          AS total_spend,
                COALESCE(DATEDIFF(CURDATE(), MAX(r.check_out)), 9999)               AS days_since_last_visit,
                COALESCE(
                    SUM(CASE WHEN r.status = 'cancelled' THEN 1 ELSE 0 END)
                    / NULLIF(COUNT(r.id), 0), 0
                )                                                                    AS cancellation_rate
            FROM users u
            LEFT JOIN reservations r  ON r.user_id = u.id
            LEFT JOIN payments p      ON p.reservation_id = r.id AND p.status = 'completed'
            WHERE u.role = 'guest'
            GROUP BY u.id, u.name, u.email, u.segment
            ORDER BY FIELD(u.segment, 'vip','loyal','high_value_new','at_risk','unreliable','regular')
        ");

        return view('admin.algorithms.index', compact('segmentedGuests'));
    }
}
