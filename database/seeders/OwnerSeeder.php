<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'owner@arci.com'],
            [
                'name' => 'Owner AJNN',
                'password' => Hash::make('123456'),
                'role' => 'owner',
                'nik' => 'OWN-001',
                'pph21_status' => 'TK/0',
                'basic_salary' => 0,
            ]
        );
    }
}
