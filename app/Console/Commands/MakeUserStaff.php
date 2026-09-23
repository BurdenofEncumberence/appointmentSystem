<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserStaff extends Command
{
    protected $signature = 'user:make-staff {email : The account email to promote}';

    protected $description = 'Promote an existing user to the staff role';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No user exists with that email address.');

            return self::FAILURE;
        }

        $user->update(['role' => 'staff']);
        $this->info("{$user->email} is now a staff member.");

        return self::SUCCESS;
    }
}
