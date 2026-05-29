<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReimbursementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Employee IDs: 2=Budi Santoso, 3=Siti Aminah, 5=Robin Viery, 6=budi arto
        $employees = \App\Models\User::where('role', 'employee')->pluck('id')->toArray();

        if (empty($employees)) {
            $this->command->info('No employees found, skipping reimbursement seed.');
            return;
        }

        $descriptions = [
            'Biaya transportasi dinas luar kota ke Surabaya',
            'Pembelian alat tulis kantor untuk kebutuhan proyek',
            'Biaya makan siang rapat tim dengan klien',
            'Biaya bensin perjalanan dinas ke cabang',
            'Pembelian tinta printer untuk dokumen bulanan',
            'Biaya parkir selama kunjungan klien',
            'Biaya fotokopi dokumen kontrak kerja',
            'Biaya telepon dan internet luar kantor',
            'Biaya konsumsi pelatihan karyawan baru',
            'Biaya perjalanan dinas ke Jakarta',
            'Pembelian kertas A4 untuk laporan bulanan',
            'Biaya representasi pertemuan dengan vendor',
        ];

        $periods = [
            ['month' => 1, 'year' => 2026],
            ['month' => 2, 'year' => 2026],
            ['month' => 3, 'year' => 2026],
            ['month' => 4, 'year' => 2026],
        ];

        $amounts = [50000, 75000, 100000, 150000, 200000, 250000, 300000, 500000, 750000, 1000000];

        $now = now();
        $data = [];

        foreach ($employees as $userId) {
            foreach ($periods as $period) {
                // Insert 1-3 reimbursements per employee per month
                $count = rand(1, 3);
                for ($i = 0; $i < $count; $i++) {
                    $day = rand(1, 28);
                    $dateStr = sprintf('%04d-%02d-%02d', $period['year'], $period['month'], $day);
                    $data[] = [
                        'user_id'     => $userId,
                        'date'        => $dateStr,
                        'amount'      => $amounts[array_rand($amounts)],
                        'description' => $descriptions[array_rand($descriptions)],
                        'status'      => 'pending',
                        'hr_notes'    => null,
                        'file_path'   => null,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ];
                }
            }
        }

        // Chunk insert to avoid memory issues
        foreach (array_chunk($data, 50) as $chunk) {
            DB::table('reimbursements')->insert($chunk);
        }

        $this->command->info('Reimbursement dummy data inserted: ' . count($data) . ' records.');
    }
}

