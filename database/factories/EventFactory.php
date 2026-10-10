<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Buchung, wie sie nach dem öffentlichen Buchungsformular in der DB steht.
 *
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 week', '+6 months');
        $days = fake()->numberBetween(1, 5);
        $telephone = '079 '.fake()->numerify('### ## ##');

        return [
            'name' => fake()->lastName(),
            'firstname' => fake()->firstName(),
            'group_name' => fake()->optional()->company(),
            'email' => fake()->unique()->safeEmail(),
            'street' => fake()->streetName(),
            'house_number' => (string) fake()->buildingNumber(),
            'plz' => fake()->numberBetween(1000, 9999),
            'city' => fake()->city(),
            'telephone' => $telephone,
            'phonenumber_query' => str_replace([' ', '-'], '', $telephone),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => (clone $start)->modify("+$days days")->format('Y-m-d'),
            'total_days' => $days,
            'total_people' => fake()->numberBetween(1, 19),
            'event_status_id' => config('status.event_neu'),
            'contract_status_id' => config('status.contract_offen'),
            'terms' => true,
            'uuid' => (string) Str::uuid(),
        ];
    }

    public function confirmed(): static
    {
        return $this->state([
            'event_status_id' => config('status.event_bestaetigt'),
            'contract_status_id' => config('status.contract_rechnung_erstellt'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state([
            'event_status_id' => config('status.event_storniert'),
            'contract_status_id' => config('status.contract_storniert'),
        ]);
    }

    public function past(): static
    {
        return $this->state(fn () => [
            'start_date' => now()->subDays(10)->format('Y-m-d'),
            'end_date' => now()->subDays(8)->format('Y-m-d'),
            'total_days' => 2,
        ]);
    }
}
