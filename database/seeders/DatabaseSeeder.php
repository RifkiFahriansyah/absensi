<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default boss account
        User::firstOrCreate(
        ['email' => 'boss@coffeeshop.com'],
        [
            'name' => 'Owner Coffee Shop',
            'password' => 'password',
            'role' => 'boss',
            'phone' => '081234567890',
        ]
        );

        // Create sample employees
        User::firstOrCreate(
        ['email' => 'andi@coffeeshop.com'],
        [
            'name' => 'Andi Pratama',
            'password' => 'password',
            'role' => 'employee',
            'phone' => '081234567891',
        ]
        );

        User::firstOrCreate(
        ['email' => 'sari@coffeeshop.com'],
        [
            'name' => 'Sari Wulandari',
            'password' => 'password',
            'role' => 'employee',
            'phone' => '081234567892',
        ]
        );

        User::firstOrCreate(
        ['email' => 'budi@coffeeshop.com'],
        [
            'name' => 'Budi Santoso',
            'password' => 'password',
            'role' => 'employee',
            'phone' => '081234567893',
        ]
        );

        // Create default settings
        Setting::instance();
    }
}
