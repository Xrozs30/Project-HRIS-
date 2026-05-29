<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = [
            ['tax_id' => 1, 'tax_status' => 'T/K0'],
            ['tax_id' => 2, 'tax_status' => 'T/K1'],
            ['tax_id' => 3, 'tax_status' => 'T/K2'],
            ['tax_id' => 4, 'tax_status' => 'T/K3'],
            ['tax_id' => 5, 'tax_status' => 'K/0'],
            ['tax_id' => 6, 'tax_status' => 'K/1'],
            ['tax_id' => 7, 'tax_status' => 'K/2'],
            ['tax_id' => 8, 'tax_status' => 'K/3'],
        ];

        foreach ($taxes as $tax) {
            \App\Models\Tax::updateOrCreate(
                ['tax_id' => $tax['tax_id']],
                ['tax_status' => $tax['tax_status']]
            );
        }
    }
}
