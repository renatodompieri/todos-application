<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         \App\Models\User::create([
             'name' => 'Administrator',
             'email' => 'admin@admin.com',
             'password' => bcrypt('admin'),
             'email_verified_at' => now(),
             'remember_token' => md5('remember_token'),
         ]);
    }
}
