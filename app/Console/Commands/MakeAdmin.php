<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {email : The email of the user to promote to admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote a user to admin role by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return Command::FAILURE;
        }
        
        // Line 38 - Update the handle method
        if ($user->role === 'Admin') {
            $this->info("User '{$email}' is already an admin.");
            return Command::SUCCESS;
        }
        
        $user->role = 'Admin';
        $user->save();
        
        $this->info("User '{$email}' has been promoted to admin successfully!");
        return Command::SUCCESS;
    }
}