<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            ['position_id' => 1, 'position_type' => 'owner'],
            ['position_id' => 2, 'position_type' => 'hr'],
            ['position_id' => 3, 'position_type' => 'it'],
            ['position_id' => 4, 'position_type' => 'marketing'],
            ['position_id' => 5, 'position_type' => 'staff'],
        ];

        foreach ($positions as $position) {
            \App\Models\Position::updateOrCreate(
                ['position_id' => $position['position_id']],
                ['position_type' => $position['position_type']]
            );
        }
    }
}
