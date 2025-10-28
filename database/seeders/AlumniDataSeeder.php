<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Alumni;
use Illuminate\Support\Facades\Hash;

class AlumniDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // This seeder is currently empty
        // Add your alumni data here when needed
        
        $this->command->info('AlumniDataSeeder completed - no data to seed.');
    }
}