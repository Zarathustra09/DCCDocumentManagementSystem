<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentRegistrationEntryFactory extends Factory
{
    protected $model = DocumentRegistrationEntry::class;

    public function definition()
    {
        return [
            'document_title'   => $this->faker->sentence(3),
            'document_no'      => $this->faker->unique()->bothify('DOC-####'),
            'revision_no'      => $this->faker->randomElement(['A', 'B', 'C']),
            'device_name'      => $this->faker->word, // Always set a value
            'originator_name'  => $this->faker->name,
            'customer_id'      => Customer::factory(),
            'remarks'          => $this->faker->sentence,
            'status_id'        => $this->faker->numberBetween(1, 3),
            'submitted_by'     => User::factory(),
            'implemented_by'   => $this->faker->boolean(50) ? User::factory() : null,
            'submitted_at'     => $this->faker->dateTimeBetween('-1 year', 'now'),
            'implemented_at'   => $this->faker->boolean(50) ? $this->faker->dateTimeBetween('-6 months', 'now') : null,
            'control_no'       => now()->format('y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
        ];
    }

    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status_id' => DocumentRegistrationEntryStatus::factory()->pending(),
                'implemented_by' => null,
                'implemented_at' => null,
            ];
        });
    }

    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status_id' => DocumentRegistrationEntryStatus::factory()->approved(),
                'implemented_by' => User::factory(),
                'implemented_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            ];
        });
    }

    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status_id' => DocumentRegistrationEntryStatus::factory()->rejected(),
                'rejection_reason' => $this->faker->sentence,
                'implemented_by' => null,
                'implemented_at' => null,
            ];
        });
    }
}
