<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AllowanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Allowance::truncate();

        $allowances = [
            ['allowance_id' => 1, 'allowance_type' => 'THR'],
            ['allowance_id' => 2, 'allowance_type' => 'target_bonus'],
            ['allowance_id' => 3, 'allowance_type' => 'overtime'],
            ['allowance_id' => 4, 'allowance_type' => 'BPJS_kesehatan'],
        ];

        foreach ($allowances as $allowance) {
            \App\Models\Allowance::create($allowance);
        }
    }
}
