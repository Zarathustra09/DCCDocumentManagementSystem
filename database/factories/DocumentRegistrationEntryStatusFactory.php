<?php

namespace Database\Factories;

use App\Models\DocumentRegistrationEntryStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentRegistrationEntryStatusFactory extends Factory
{
    protected $model = DocumentRegistrationEntryStatus::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Pending', 'Approved', 'Rejected', 'Revision Required']),
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

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Approved',
            'is_active' => true,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Rejected',
            'is_active' => false,
        ]);
    }

    public function revisionRequired(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Revision Required',
            'is_active' => false,
        ]);
    }
}
