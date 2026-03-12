<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code'              => $this->faker->unique()->currencyCode(),
            'name'              => $this->faker->word() . ' Currency',
            'symbol'            => $this->faker->randomElement(['$', '€', '£', '¥', 'ر.س', 'د.إ']),
            'fraction_name'     => $this->faker->randomElement(['Cent', 'Fils', 'Halala', 'Pence']),
            'exchange_rate'     => $this->faker->randomFloat(6, 0.1, 100),
            'equivalent'        => $this->faker->randomFloat(6, 0.1, 100),
            'max_exchange_rate' => $this->faker->randomFloat(6, 0.1, 150),
            'min_exchange_rate' => $this->faker->randomFloat(6, 0, 90),
            'is_local'          => $this->faker->boolean(15),
            'is_inventory'      => $this->faker->boolean(25),
            'status'            => $this->faker->randomElement([ActiveStatus::ACTIVE->value, ActiveStatus::INACTIVE->value]),
            'notes'             => $this->faker->optional()->sentence(),
            'created_by'        => 1,
        ];
    }
}
