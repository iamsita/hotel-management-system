<?php

namespace App\Console\Commands;

use App\Models\Food;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class InitialSeedCommand extends Command
{
    protected $signature = 'initial:seed';

    protected $description = 'Seed initial data for Hotel Management System with diverse guest behaviors for algorithm testing';

    public function handle(): void
    {
        $this->info('Starting Hotel Management System initial seeding...');

        $this->seedUsers();
        $this->seedRooms();
        $this->seedFoods();
        $this->seedReservations();
        $this->seedPayments();

        $this->info('✓ Seeding completed successfully!');
        $this->info('You can now run: php artisan guests:segment');
    }

    private function seedUsers(): void
    {
        $this->info('Seeding users...');

        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@hms.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'phone' => '9800000000',
                'role' => 'admin',
            ]
        );

        // VIP Guests - High spending, frequent visitors (5+ visits, ₹10,000+)
        $vipGuests = [
            ['name' => 'Rajesh Kumar', 'email' => 'rajesh@gmail.com', 'phone' => '9841111111'],
            ['name' => 'Priya Sharma', 'email' => 'priya@gmail.com', 'phone' => '9841111112'],
        ];

        foreach ($vipGuests as $guest) {
            User::firstOrCreate(
                ['email' => $guest['email']],
                [
                    'name' => $guest['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $guest['phone'],
                    'role' => 'guest',
                ]
            );
        }

        // Loyal Guests - Repeat visitors with low cancellations (3+ visits, <20% cancellation)
        $loyalGuests = [
            ['name' => 'Amit Singh', 'email' => 'amit@gmail.com', 'phone' => '9842222222'],
            ['name' => 'Neha Verma', 'email' => 'neha@gmail.com', 'phone' => '9842222223'],
            ['name' => 'Vikram Patel', 'email' => 'vikram@gmail.com', 'phone' => '9842222224'],
        ];

        foreach ($loyalGuests as $guest) {
            User::firstOrCreate(
                ['email' => $guest['email']],
                [
                    'name' => $guest['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $guest['phone'],
                    'role' => 'guest',
                ]
            );
        }

        // At Risk Guests - Previously active but inactive (90+ days, 2+ visits)
        $atRiskGuests = [
            ['name' => 'Suresh Kumar', 'email' => 'suresh@gmail.com', 'phone' => '9843333333'],
            ['name' => 'Anjali Reddy', 'email' => 'anjali@gmail.com', 'phone' => '9843333334'],
        ];

        foreach ($atRiskGuests as $guest) {
            User::firstOrCreate(
                ['email' => $guest['email']],
                [
                    'name' => $guest['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $guest['phone'],
                    'role' => 'guest',
                ]
            );
        }

        // High Value New - First time, high spend (1 visit, ₹5,000+)
        $highValueNewGuests = [
            ['name' => 'Deepak Nair', 'email' => 'deepak@gmail.com', 'phone' => '9844444444'],
            ['name' => 'Pooja Iyer', 'email' => 'pooja@gmail.com', 'phone' => '9844444445'],
        ];

        foreach ($highValueNewGuests as $guest) {
            User::firstOrCreate(
                ['email' => $guest['email']],
                [
                    'name' => $guest['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $guest['phone'],
                    'role' => 'guest',
                ]
            );
        }

        // Unreliable Guests - High cancellation rate (50%+)
        $unreliableGuests = [
            ['name' => 'Arjun Das', 'email' => 'arjun@gmail.com', 'phone' => '9845555555'],
            ['name' => 'Meera Gupta', 'email' => 'meera@gmail.com', 'phone' => '9845555556'],
        ];

        foreach ($unreliableGuests as $guest) {
            User::firstOrCreate(
                ['email' => $guest['email']],
                [
                    'name' => $guest['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $guest['phone'],
                    'role' => 'guest',
                ]
            );
        }

        // Regular Guests - Others
        $regularGuests = [
            ['name' => 'Ravi Sharma', 'email' => 'ravi@gmail.com', 'phone' => '9846666666'],
            ['name' => 'Divya Nandini', 'email' => 'divya@gmail.com', 'phone' => '9846666667'],
        ];

        foreach ($regularGuests as $guest) {
            User::firstOrCreate(
                ['email' => $guest['email']],
                [
                    'name' => $guest['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $guest['phone'],
                    'role' => 'guest',
                ]
            );
        }

        $this->line('  ✓ 11 guests seeded');
    }

    private function seedRooms(): void
    {
        $this->info('Seeding rooms...');

        $rooms = [
            ['room_number' => '101', 'type' => 'single', 'capacity' => 1, 'price_per_night' => 2000, 'floor' => 1],
            ['room_number' => '102', 'type' => 'single', 'capacity' => 1, 'price_per_night' => 2000, 'floor' => 1],
            ['room_number' => '201', 'type' => 'double', 'capacity' => 2, 'price_per_night' => 3500, 'floor' => 2],
            ['room_number' => '202', 'type' => 'double', 'capacity' => 2, 'price_per_night' => 3500, 'floor' => 2],
            ['room_number' => '203', 'type' => 'double', 'capacity' => 2, 'price_per_night' => 3500, 'floor' => 2],
            ['room_number' => '301', 'type' => 'suite', 'capacity' => 3, 'price_per_night' => 5500, 'floor' => 3],
            ['room_number' => '302', 'type' => 'suite', 'capacity' => 3, 'price_per_night' => 5500, 'floor' => 3],
            ['room_number' => '401', 'type' => 'deluxe', 'capacity' => 4, 'price_per_night' => 8000, 'floor' => 4],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(
                ['room_number' => $room['room_number']],
                array_merge($room, ['status' => 'available'])
            );
        }

        $this->line('  ✓ 8 rooms seeded');
    }

    private function seedFoods(): void
    {
        $this->info('Seeding foods...');

        $foods = [
            ['name' => 'Aloo Paratha', 'category' => 'breakfast', 'price' => 250],
            ['name' => 'Paneer Tikka', 'category' => 'lunch', 'price' => 350],
            ['name' => 'Butter Chicken', 'category' => 'lunch', 'price' => 450],
            ['name' => 'Tandoori Chicken', 'category' => 'dinner', 'price' => 500],
            ['name' => 'Fish Curry', 'category' => 'dinner', 'price' => 550],
            ['name' => 'Samosa', 'category' => 'snacks', 'price' => 50],
            ['name' => 'Momos', 'category' => 'snacks', 'price' => 100],
            ['name' => 'Tea', 'category' => 'beverages', 'price' => 50],
            ['name' => 'Coffee', 'category' => 'beverages', 'price' => 80],
            ['name' => 'Fresh Orange Juice', 'category' => 'beverages', 'price' => 120],
        ];

        foreach ($foods as $food) {
            Food::updateOrCreate(
                ['name' => $food['name']],
                array_merge($food, ['available' => true])
            );
        }

        $this->line('  ✓ 10 foods seeded');
    }

    private function seedReservations(): void
    {
        $this->info('Seeding reservations and related data...');

        $users = User::where('role', 'guest')->get();
        $rooms = Room::all();
        $foods = Food::all();

        $reservationCount = 0;
        $foodOrderCount = 0;
        $paymentCount = 0;

        // VIP Guests: 5+ completed stays, high spending
        $vipUsers = $users->whereIn('email', ['rajesh@gmail.com', 'priya@gmail.com']);
        foreach ($vipUsers as $user) {
            for ($i = 0; $i < 6; $i++) {
                $checkIn = Carbon::now()->subMonths(6 - $i)->startOfDay();
                $checkOut = $checkIn->copy()->addDays(2);
                $room = $rooms->random();
                $nights = $checkOut->diffInDays($checkIn);
                $totalAmount = $room->price_per_night * $nights;

                $reservation = Reservation::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'room_id' => $room->id,
                        'check_in' => $checkIn->toDateString(),
                    ],
                    [
                        'check_out' => $checkOut->toDateString(),
                        'guests' => rand(1, 3),
                        'status' => 'checked_out',
                        'total_amount' => $totalAmount,
                    ]
                );
                $reservationCount++;

                // Add food orders
                for ($j = 0; $j < rand(2, 4); $j++) {
                    $food = $foods->random();
                    FoodOrder::updateOrCreate(
                        [
                            'reservation_id' => $reservation->id,
                            'food_id' => $food->id,
                        ],
                        [
                            'quantity' => rand(1, 3),
                            'total_price' => $food->price * rand(1, 3),
                            'status' => 'delivered',
                        ]
                    );
                    $foodOrderCount++;
                }

                // Add payment
                Payment::updateOrCreate(
                    ['reservation_id' => $reservation->id],
                    [
                        'amount' => $totalAmount + rand(500, 2000),
                        'method' => collect(['cash', 'card', 'upi'])->random(),
                        'status' => 'completed',
                    ]
                );
                $paymentCount++;
            }
        }

        // Loyal Guests: 3+ visits, low cancellation (<20%)
        $loyalUsers = $users->whereIn('email', ['amit@gmail.com', 'neha@gmail.com', 'vikram@gmail.com']);
        foreach ($loyalUsers as $user) {
            for ($i = 0; $i < 4; $i++) {
                $checkIn = Carbon::now()->subMonths(8 - (2 * $i))->startOfDay();
                $checkOut = $checkIn->copy()->addDay();
                $room = $rooms->random();
                $totalAmount = $room->price_per_night;

                $reservation = Reservation::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'room_id' => $room->id,
                        'check_in' => $checkIn->toDateString(),
                    ],
                    [
                        'check_out' => $checkOut->toDateString(),
                        'guests' => 1,
                        'status' => 'checked_out',
                        'total_amount' => $totalAmount,
                    ]
                );
                $reservationCount++;

                // Add food orders
                for ($j = 0; $j < rand(1, 3); $j++) {
                    $food = $foods->random();
                    FoodOrder::updateOrCreate(
                        [
                            'reservation_id' => $reservation->id,
                            'food_id' => $food->id,
                        ],
                        [
                            'quantity' => rand(1, 2),
                            'total_price' => $food->price * rand(1, 2),
                            'status' => 'delivered',
                        ]
                    );
                    $foodOrderCount++;
                }

                // Add payment
                Payment::updateOrCreate(
                    ['reservation_id' => $reservation->id],
                    [
                        'amount' => $totalAmount + rand(200, 800),
                        'method' => collect(['cash', 'card'])->random(),
                        'status' => 'completed',
                    ]
                );
                $paymentCount++;
            }
        }

        // At Risk: 2+ visits, 90+ days inactive
        $atRiskUsers = $users->whereIn('email', ['suresh@gmail.com', 'anjali@gmail.com']);
        foreach ($atRiskUsers as $user) {
            for ($i = 0; $i < 3; $i++) {
                $checkIn = Carbon::now()->subMonths(10 + (3 * $i))->startOfDay();
                $checkOut = $checkIn->copy()->addDay();
                $room = $rooms->random();
                $totalAmount = $room->price_per_night;

                $reservation = Reservation::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'room_id' => $room->id,
                        'check_in' => $checkIn->toDateString(),
                    ],
                    [
                        'check_out' => $checkOut->toDateString(),
                        'guests' => 1,
                        'status' => 'checked_out',
                        'total_amount' => $totalAmount,
                    ]
                );
                $reservationCount++;

                // Add food orders
                for ($j = 0; $j < rand(1, 2); $j++) {
                    $food = $foods->random();
                    FoodOrder::updateOrCreate(
                        [
                            'reservation_id' => $reservation->id,
                            'food_id' => $food->id,
                        ],
                        [
                            'quantity' => rand(1, 2),
                            'total_price' => $food->price * rand(1, 2),
                            'status' => 'delivered',
                        ]
                    );
                    $foodOrderCount++;
                }

                // Add payment
                Payment::updateOrCreate(
                    ['reservation_id' => $reservation->id],
                    [
                        'amount' => $totalAmount + rand(100, 500),
                        'method' => 'cash',
                        'status' => 'completed',
                    ]
                );
                $paymentCount++;
            }
        }

        // High Value New: First time, high spend
        $highValueUsers = $users->whereIn('email', ['deepak@gmail.com', 'pooja@gmail.com']);
        foreach ($highValueUsers as $user) {
            $checkIn = Carbon::now()->subDays(15)->startOfDay();
            $checkOut = $checkIn->copy()->addDays(3);
            $room = $rooms->whereIn('type', ['suite', 'deluxe'])->random();
            $totalAmount = $room->price_per_night * 3;

            $reservation = Reservation::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'room_id' => $room->id,
                    'check_in' => $checkIn->toDateString(),
                ],
                [
                    'check_out' => $checkOut->toDateString(),
                    'guests' => 2,
                    'status' => 'checked_out',
                    'total_amount' => $totalAmount,
                ]
            );
            $reservationCount++;

            // Add multiple food orders
            for ($j = 0; $j < 5; $j++) {
                $food = $foods->random();
                FoodOrder::updateOrCreate(
                    [
                        'reservation_id' => $reservation->id,
                        'food_id' => $food->id,
                    ],
                    [
                        'quantity' => rand(1, 2),
                        'total_price' => $food->price * rand(1, 2),
                        'status' => 'delivered',
                    ]
                );
                $foodOrderCount++;
            }

            // Add payment
            Payment::updateOrCreate(
                ['reservation_id' => $reservation->id],
                [
                    'amount' => $totalAmount + rand(1000, 2000),
                    'method' => 'card',
                    'status' => 'completed',
                ]
            );
            $paymentCount++;
        }

        // Unreliable: High cancellation (50%+)
        $unreliableUsers = $users->whereIn('email', ['arjun@gmail.com', 'meera@gmail.com']);
        foreach ($unreliableUsers as $user) {
            for ($i = 0; $i < 4; $i++) {
                $checkIn = Carbon::now()->subMonths(7 - $i)->startOfDay();
                $checkOut = $checkIn->copy()->addDay();
                $room = $rooms->random();
                $totalAmount = $room->price_per_night;

                // Half cancelled, half completed
                $status = $i % 2 == 0 ? 'cancelled' : 'checked_out';

                $reservation = Reservation::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'room_id' => $room->id,
                        'check_in' => $checkIn->toDateString(),
                    ],
                    [
                        'check_out' => $checkOut->toDateString(),
                        'guests' => 1,
                        'status' => $status,
                        'total_amount' => $totalAmount,
                    ]
                );
                $reservationCount++;

                // Only add food orders if not cancelled
                if ($status !== 'cancelled') {
                    for ($j = 0; $j < rand(1, 2); $j++) {
                        $food = $foods->random();
                        FoodOrder::updateOrCreate(
                            [
                                'reservation_id' => $reservation->id,
                                'food_id' => $food->id,
                            ],
                            [
                                'quantity' => 1,
                                'total_price' => $food->price,
                                'status' => 'delivered',
                            ]
                        );
                        $foodOrderCount++;
                    }

                    // Add payment only for completed
                    Payment::updateOrCreate(
                        ['reservation_id' => $reservation->id],
                        [
                            'amount' => $totalAmount + rand(100, 300),
                            'method' => 'cash',
                            'status' => 'completed',
                        ]
                    );
                    $paymentCount++;
                }
            }
        }

        // Regular Guests: Others
        $regularUsers = $users->whereIn('email', ['ravi@gmail.com', 'divya@gmail.com']);
        foreach ($regularUsers as $user) {
            for ($i = 0; $i < 2; $i++) {
                $checkIn = Carbon::now()->subMonths(5 + $i)->startOfDay();
                $checkOut = $checkIn->copy()->addDay();
                $room = $rooms->random();
                $totalAmount = $room->price_per_night;

                $reservation = Reservation::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'room_id' => $room->id,
                        'check_in' => $checkIn->toDateString(),
                    ],
                    [
                        'check_out' => $checkOut->toDateString(),
                        'guests' => 1,
                        'status' => 'checked_out',
                        'total_amount' => $totalAmount,
                    ]
                );
                $reservationCount++;

                // Add food orders
                for ($j = 0; $j < rand(1, 2); $j++) {
                    $food = $foods->random();
                    FoodOrder::updateOrCreate(
                        [
                            'reservation_id' => $reservation->id,
                            'food_id' => $food->id,
                        ],
                        [
                            'quantity' => 1,
                            'total_price' => $food->price,
                            'status' => 'delivered',
                        ]
                    );
                    $foodOrderCount++;
                }

                // Add payment
                Payment::updateOrCreate(
                    ['reservation_id' => $reservation->id],
                    [
                        'amount' => $totalAmount + rand(100, 500),
                        'method' => 'cash',
                        'status' => 'completed',
                    ]
                );
                $paymentCount++;
            }
        }

        $this->line("  ✓ $reservationCount reservations seeded");
        $this->line("  ✓ $foodOrderCount food orders seeded");
        $this->line("  ✓ $paymentCount payments seeded");
    }

    private function seedPayments(): void
    {
        // Payments are already created in seedReservations()
        // This method is a placeholder for any additional payment logic
    }
}
