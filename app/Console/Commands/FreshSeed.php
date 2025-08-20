<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FreshSeed extends Command
{
    protected $signature = 'db:fresh-seed';
    
    protected $description = 'Fresh migrate and seed database';

    public function handle()
    {
        $this->info('🔄 Resetting database...');
        $this->call('migrate:fresh');
        
        $this->info('🌱 Seeding database...');
        $this->call('db:seed');
        
        $this->info('✅ Database reset and seeded successfully!');
        $this->info('🔐 Don\'t forget to create admin user: php artisan make:filament-user');
        
        return 0;
    }
}