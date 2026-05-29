<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveSeeder extends Seeder
{
    public function run(): void
    {
        // Create HR User
        \App\Models\User::firstOrCreate(
        ['email' => 'hr@arci.com'],
        [
            'name' => 'HR Manager',
            'password' => bcrypt('123456'),
            'role' => 'hr',
        ]
        );

        // Create Employee 1
        $emp1 = \App\Models\User::firstOrCreate(
        ['email' => 'budi@example.com'],
        [
            'name' => 'Budi Santoso',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]
        );

        // Create Employee 2
        $emp2 = \App\Models\User::firstOrCreate(
        ['email' => 'siti@example.com'],
        [
            'name' => 'Siti Aminah',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]
        );

        // Create Leave Requests
        \App\Models\LeaveRequest::updateOrCreate(
        [
            'user_id' => $emp1->id,
            'type' => 'cuti',
            'status' => 'pending',
            'reason' => 'Liburan keluarga ke Bali',
        ],
        [
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(7),
            'duration' => 3,
        ]
        );

        \App\Models\LeaveRequest::updateOrCreate(
        [
            'user_id' => $emp2->id,
            'type' => 'ijin',
            'status' => 'pending',
            'reason' => 'Mengurus SIM',
        ],
        [
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(1),
            'duration' => 1,
        ]
        );

        \App\Models\LeaveRequest::updateOrCreate(
        [
            'user_id' => $emp1->id,
            'type' => 'sakit',
            'reason' => 'Demam tinggi',
        ],
        [
            'start_date' => now()->subDays(5),
            'end_date' => now()->subDays(4),
            'duration' => 2,
            'status' => 'approved',
            'approved_by' => clone \App\Models\User::where('role', 'hr')->first()->id ?? 1, // HR ID
        ]
        );
    }
}
