<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => fake()->numberBetween(1,3),
            'status' => fake()->randomElement(['inprogress', 'paid', 'expired', 'rejected', 'new', 'pending', 'completed']),
            'amount' => mt_rand(50, 100),
            'gateway_id' => fake()->numberBetween(1,2),
        ];
    }
}
