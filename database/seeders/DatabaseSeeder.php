<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $agentRole = Role::firstOrCreate(['name' => 'agent']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Διαχειριστής', 'password' => Hash::make('password')],
        );
        $admin->assignRole($adminRole);

        $secondAdmin = User::firstOrCreate(
            ['email' => 'admin2@example.com'],
            ['name' => 'Δεύτερος Διαχειριστής', 'password' => Hash::make('password')],
        );
        $secondAdmin->assignRole($adminRole);

        $agent = User::firstOrCreate(
            ['email' => 'agent@example.com'],
            ['name' => 'Υπεύθυνος Υποστήριξης', 'password' => Hash::make('password')],
        );
        $agent->assignRole($agentRole);

        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            ['name' => 'Πελάτης', 'password' => Hash::make('password')],
        );
        $customer->assignRole($customerRole);
    }
}
