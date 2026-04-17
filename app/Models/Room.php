<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_number', 'type', 'capacity', 'price_per_night', 'status', 'floor'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * ALGORITHM: Interval Overlap Detection
     *
     * Checks if a room is available for the given date range by detecting
     * overlapping reservations using the interval overlap formula:
     *
     *   Two intervals [A_start, A_end] and [B_start, B_end] overlap if:
     *       A_start < B_end  AND  B_start < A_end
     *
     * This runs in O(n) time where n = number of active reservations for this room.
     * We exclude cancelled and checked_out reservations from the check.
     */
    public function isAvailable($checkIn, $checkOut, $excludeReservationId = null)
    {
        $query = $this->reservations()
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->where('check_in', '<', $checkOut)   // existing start < new end
            ->where('check_out', '>', $checkIn);   // existing end > new start

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query->count() === 0;
    }

    /**
     * ALGORITHM: Search, Filter & Sort
     *
     * Multi-criteria filtering using query builder pattern:
     *   1. Filter by room type (exact match)
     *   2. Filter by price range (min/max bounds)
     *   3. Filter by minimum capacity (greater than or equal)
     *   4. Sort by chosen field (price, capacity, room_number)
     *
     * Time complexity: O(n log n) due to sorting, where n = number of rooms.
     */
    public static function searchAndFilter($filters)
    {
        $query = static::where('status', 'available');

        // Filter by type
        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Filter by price range
        if (! empty($filters['min_price'])) {
            $query->where('price_per_night', '>=', $filters['min_price']);
        }
        if (! empty($filters['max_price'])) {
            $query->where('price_per_night', '<=', $filters['max_price']);
        }

        // Filter by minimum capacity
        if (! empty($filters['capacity'])) {
            $query->where('capacity', '>=', $filters['capacity']);
        }

        // Sort results
        $sortBy = $filters['sort_by'] ?? 'price_per_night';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $allowedSorts = ['price_per_night', 'capacity', 'room_number', 'floor'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        return $query->get();
    }
}
