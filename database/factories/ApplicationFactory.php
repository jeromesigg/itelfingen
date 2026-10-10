<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'salutation_id' => 1,
            'name' => fake()->lastName(),
            'firstname' => fake()->firstName(),
            'email' => fake()->safeEmail(),
            'street' => fake()->streetName(),
            'house_number' => (string) fake()->buildingNumber(),
            'plz' => fake()->numberBetween(1000, 9999),
            'city' => fake()->city(),
            'telephone' => '079 '.fake()->numerify('### ## ##'),
            'why' => fake()->sentence(),
        ];
    }
}
