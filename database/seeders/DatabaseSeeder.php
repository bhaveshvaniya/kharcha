<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user
        $user = User::create([
            'name'     => 'Demo User',
            'email'    => 'demo@kharcha.com',
            'password' => Hash::make('password'),
            'currency' => 'INR',
        ]);

        // Sample transactions for last 3 months
        $categories = [
            'Food & Dining', 'Transport', 'Shopping', 'Entertainment',
            'Healthcare', 'Bills & Utilities', 'Education', 'Other'
        ];

        $descriptions = [
            'Food & Dining'    => ['Grocery Shopping', 'Restaurant Dinner', 'Zomato Order', 'Swiggy Breakfast', 'BigBasket'],
            'Transport'        => ['Uber Ride', 'Metro Card Recharge', 'Ola Cab', 'Petrol Fill', 'Auto Rickshaw'],
            'Shopping'         => ['Amazon Purchase', 'Flipkart Order', 'Clothes Shopping', 'Electronics', 'Books'],
            'Entertainment'    => ['Netflix Subscription', 'Movie Tickets', 'Spotify Premium', 'Gaming', 'Concert'],
            'Healthcare'       => ['Doctor Visit', 'Medicine', 'Lab Tests', 'Gym Membership', 'Pharmacy'],
            'Bills & Utilities'=> ['Electricity Bill', 'Internet Bill', 'Mobile Recharge', 'Water Bill', 'House Rent'],
            'Education'        => ['Online Course', 'Books', 'Tuition Fee', 'Udemy Course', 'Certification'],
            'Other'            => ['Miscellaneous', 'Gift', 'Donation', 'Household Items', 'Repair'],
        ];

        $now = now();

        for ($m = 2; $m >= 0; $m--) {
            $monthDate = $now->copy()->subMonths($m);

            // Salary income
            Transaction::create([
                'user_id'     => $user->id,
                'type'        => 'income',
                'amount'      => rand(45000, 65000),
                'description' => 'Monthly Salary',
                'category'    => 'Salary',
                'date'        => $monthDate->copy()->startOfMonth()->format('Y-m-d'),
                'note'        => 'Regular monthly salary',
            ]);

            // Freelance income (sometimes)
            if (rand(0, 1)) {
                Transaction::create([
                    'user_id'     => $user->id,
                    'type'        => 'income',
                    'amount'      => rand(5000, 15000),
                    'description' => 'Freelance Project',
                    'category'    => 'Freelance',
                    'date'        => $monthDate->copy()->addDays(rand(5, 20))->format('Y-m-d'),
                    'note'        => 'Side project payment',
                ]);
            }

            // Expenses (10-15 per month)
            $numExpenses = rand(10, 15);
            for ($i = 0; $i < $numExpenses; $i++) {
                $cat  = $categories[array_rand($categories)];
                $desc = $descriptions[$cat][array_rand($descriptions[$cat])];
                Transaction::create([
                    'user_id'     => $user->id,
                    'type'        => 'expense',
                    'amount'      => rand(200, 8000),
                    'description' => $desc,
                    'category'    => $cat,
                    'date'        => $monthDate->copy()->addDays(rand(0, 27))->format('Y-m-d'),
                    'note'        => null,
                ]);
            }
        }

        // Sample goals
        Goal::insert([
            [
                'user_id'       => $user->id,
                'name'          => 'Emergency Fund',
                'target_amount' => 200000,
                'saved_amount'  => 75000,
                'emoji'         => '🛡️',
                'deadline'      => now()->addYear()->format('Y-m-d'),
                'description'   => '6 months of expenses',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'user_id'       => $user->id,
                'name'          => 'Goa Vacation',
                'target_amount' => 50000,
                'saved_amount'  => 22000,
                'emoji'         => '✈️',
                'deadline'      => now()->addMonths(6)->format('Y-m-d'),
                'description'   => 'Annual family trip to Goa',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'user_id'       => $user->id,
                'name'          => 'New MacBook',
                'target_amount' => 120000,
                'saved_amount'  => 45000,
                'emoji'         => '💻',
                'deadline'      => now()->addMonths(8)->format('Y-m-d'),
                'description'   => 'MacBook Pro M3',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
