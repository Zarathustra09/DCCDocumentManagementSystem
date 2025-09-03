<?php

namespace Database\Factories;

use App\Models\DocumentRegistrationEntry;
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
            'customer'         => $this->faker->company,
            'remarks'          => $this->faker->sentence,
            'status'           => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'submitted_by'     => User::factory(),
            'implemented_by'   => null,
            'submitted_at'     => now(),
            'implemented_at'   => null,
        ];
    }
}
