<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin first
        $superAdmin = User::updateOrCreate(
            ['email' => 'chedymaed@gmail.com'],
            [
                'name' => 'Super Admin',
                'email' => 'chedymaed@gmail.com',
                'password' => Hash::make('SuperAdmin2025!'),
                'role' => 'SuperAdmin',
                'email_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now()
            ]
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'jeannpierrepolnareff@yahoo.com'],
            [
                'name' => 'Admin User',
                'email' => 'jeannpierrepolnareff@yahoo.com',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'email_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $superAdmin->id
            ]
        );

        // Staff
        User::updateOrCreate(
            ['email' => 'frownymc2knives@gmail.com'],
            [
                'name' => 'Staff User',
                'email' => 'frownymc2knives@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'Staff',
                'email_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $superAdmin->id
            ]
        );

        // HR
        User::updateOrCreate(
            ['email' => 'daffyjillypie@gmail.com'],
            [
                'name' => 'HR Representative',
                'email' => 'daffyjillypie@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'HR',
                'email_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $superAdmin->id
            ]
        );

        // Alumni User
        User::updateOrCreate(
            ['email' => 'jilltagudin262@gmail.com'],
            [
                'name' => 'Alumni User',
                'email' => 'jilltagudin262@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'Alumni',
                'email_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $superAdmin->id
            ]
        );

        $this->command->info('Test accounts created:');
        $this->command->info('Super Admin - Email: chedymaed@gmail.com | Password: SuperAdmin2025!');
        $this->command->info('Admin - Email: jeannpierrepolnareff@yahoo.com | Password: password');
        $this->command->info('Staff - Email: frownymc2knives@gmail.com | Password: password');
        $this->command->info('HR - Email: daffyjillypie@gmail.com | Password: password');
        $this->command->info('Alumni - Email: jilltagudin262@gmail.com | Password: password');
    }
}