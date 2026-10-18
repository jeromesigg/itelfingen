<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Standard: aktiver Benutzer ohne Rolle (wie nach der Selbst-Registrierung, aber freigeschaltet).
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'fullname' => fake()->name(),
            'password' => Hash::make('password'),
            'role_id' => null,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(['role_id' => UserRole::Administrator]);
    }

    public function verwalter(): static
    {
        return $this->state(['role_id' => UserRole::Verwalter]);
    }

    public function team(): static
    {
        return $this->state(['role_id' => UserRole::Team]);
    }

    public function gast(): static
    {
        return $this->state(['role_id' => UserRole::Gast]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
