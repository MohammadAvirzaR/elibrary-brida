<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateTestToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:test-token {email=admin@brida.com}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create test token for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->with('role')->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found");
            return 1;
        }

        // Delete existing tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->line('');
        $this->info('✅ Test Token Created Successfully');
        $this->line('');
        $this->line("Email: {$user->email}");
        $this->line("Name: {$user->full_name}");
        $this->line("Role: {$user->role->name}");
        $this->line('');
        $this->line('📋 Token:');
        $this->line($token);
        $this->line('');
        $this->warn('⚠️  Keep this token safe! Copy it to your frontend localStorage under key: "auth_token"');
        $this->line('');

        return 0;
    }
}
