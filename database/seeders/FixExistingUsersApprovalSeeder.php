<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixExistingUsersApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set all existing users to 'approved' status so they can continue logging in
        $updatedCount = DB::table('users')->update([
            'approval_status' => 'approved',
            'approved_at' => now()
        ]);
        
        $this->command->info("Updated {$updatedCount} existing users to 'approved' status.");
        $this->command->info('All existing users can now log in normally.');
        $this->command->info('Only new registrations will require approval.');
    }
}