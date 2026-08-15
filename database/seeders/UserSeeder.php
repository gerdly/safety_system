<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create the superadmin user with full access
        User::create([
            'name' => 'Admin SMS',
            'email' => 'adminSMS@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'superadmin',
        ]);

        // Create a standard user with controlled access
        User::create([
            'name' => 'Standard Employee',
            'email' => 'employee@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
        ]);
    }
}