<?php

namespace Database\Factories;

use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
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
            'status'             => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'rejection_reason'   => null,
            'implemented_at'     => null,
            'implemented_by'     => null,
        ];
    }
}
