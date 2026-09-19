<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    protected $signature = 'user:make-admin {email : The account email to promote}';

    protected $description = 'Promote an existing user to the admin role';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No user exists with that email address.');

            return self::FAILURE;
        }

        $user->update(['role' => 'admin']);
        $this->info("{$user->email} is now an admin.");

        return self::SUCCESS;
    }
}
