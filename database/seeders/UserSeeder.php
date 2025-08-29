<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'mmedynskyi@worldbankgroup.org'], // search condition
            [
                'name' => 'M Medynskyi',
                'password' => Hash::make('mmedynskyi@worldbankgroup.org'), // change to a strong password
                'email_verified_at' => now(),
            ]
        );
    }
}
