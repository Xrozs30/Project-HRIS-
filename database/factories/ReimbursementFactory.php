<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reimbursement>
 */
class ReimbursementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::where('role', 'employee')->inRandomOrder()->first()->id ?? \App\Models\User::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'amount' => $this->faker->randomElement([50000, 100000, 150000, 200000, 300000, 500000, 1000000]),
            'description' => $this->faker->sentence(6),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'hr_approved']),
            'hr_notes' => null,
        ];
    }
}
