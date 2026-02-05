<?php

namespace Database\Factories;

use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
use App\Models\DocumentRegistrationEntryFileStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentRegistrationEntryFileFactory extends Factory
{
    protected $model = DocumentRegistrationEntryFile::class;

    public function definition()
    {
        return [
            'entry_id'           => DocumentRegistrationEntry::factory(),
            'file_path'          => 'document_registrations/' . $this->faker->uuid . '.pdf',
            'original_filename'  => $this->faker->lexify('file_????.pdf'),
            'mime_type'          => 'application/pdf',
            'file_size'          => $this->faker->numberBetween(10000, 500000),
            'status_id'          => DocumentRegistrationEntryFileStatus::factory()->pending(),
            'rejection_reason'   => null,
            'implemented_at'     => null,
            'implemented_by'     => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => DocumentRegistrationEntryFileStatus::factory()->pending(),
            'implemented_by' => null,
            'implemented_at' => null,
            'rejection_reason' => null,
        ]);
    }

    public function implemented(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => DocumentRegistrationEntryFileStatus::factory()->implemented(),
            'implemented_by' => User::factory(),
            'implemented_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'rejection_reason' => null,
        ]);
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => DocumentRegistrationEntryFileStatus::factory()->returned(),
            'rejection_reason' => $this->faker->sentence,
            'implemented_by' => null,
            'implemented_at' => null,
        ]);
    }
}
