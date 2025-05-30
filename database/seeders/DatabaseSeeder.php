<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'email' => 'admin@gmail.com',
            'name' => 'Admin',
            'password' => Hash::make('admin1234'),
        ]);

        // Insert default settings
        DB::table('settings')->insert([
            [
                'name' => 'SmartAdmin',
                'mini_name' => 'SA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
