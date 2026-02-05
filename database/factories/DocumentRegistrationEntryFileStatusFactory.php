<?php

namespace Database\Factories;

use App\Models\DocumentRegistrationEntryFileStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentRegistrationEntryFileStatusFactory extends Factory
{
    protected $model = DocumentRegistrationEntryFileStatus::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Pending', 'Implemented', 'Returned']),
            'is_active' => true,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Pending',
            'is_active' => true,
        ]);
    }

    public function implemented(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Implemented',
            'is_active' => true,
        ]);
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Returned',
            'is_active' => false,
        ]);
    }
}
