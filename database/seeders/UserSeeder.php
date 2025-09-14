<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@uhamka.id'], // cek kalau sudah ada user ini
            [
                'name' => 'Admin UHAMKA',
                'password' => Hash::make('A123456789'),
            ]
        );
    }
}
