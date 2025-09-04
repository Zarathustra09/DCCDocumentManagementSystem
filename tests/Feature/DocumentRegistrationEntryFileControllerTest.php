<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentRegistrationEntryFileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'submit document for approval']);
        Permission::create(['name' => 'approve document registration']);
        Permission::create(['name' => 'reject document registration']);
        Permission::create(['name' => 'view all document registrations']);
        Permission::create(['name' => 'view own document registrations']);
    }

    public function test_upload_file_to_entry()
    {
        Notification::fake();
        $user = User::factory()->create();
        $user->givePermissionTo('submit document for approval');
        $entry = DocumentRegistrationEntry::factory()->create(['status' => 'pending']);
        $this->actingAs($user);

        Storage::fake('local');
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $response = $this->post(route('document-registry.upload-file', $entry), [
            'document_file' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('document_registration_entry_files', [
            'entry_id' => $entry->id,
            'original_filename' => 'test.pdf',
            'status' => 'pending',
        ]);
    }

    public function test_cannot_upload_file_to_non_pending_entry()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('submit document for approval');
        $entry = DocumentRegistrationEntry::factory()->create(['status' => 'approved']);
        $this->actingAs($user);

        Storage::fake('local');
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $response = $this->post(route('document-registry.upload-file', $entry), [
            'document_file' => $file,
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_validates_file_type()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('submit document for approval');
        $entry = DocumentRegistrationEntry::factory()->create(['status' => 'pending']);
        $this->actingAs($user);

        Storage::fake('local');
        $file = UploadedFile::fake()->create('test.exe', 100, 'application/exe');

        $response = $this->post(route('document-registry.upload-file', $entry), [
            'document_file' => $file,
        ]);

        $response->assertSessionHasErrors('document_file');
    }

    public function test_upload_validates_file_size()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('submit document for approval');
        $entry = DocumentRegistrationEntry::factory()->create(['status' => 'pending']);
        $this->actingAs($user);

        Storage::fake('local');
        $file = UploadedFile::fake()->create('test.pdf', 20480, 'application/pdf'); // 20MB

        $response = $this->post(route('document-registry.upload-file', $entry), [
            'document_file' => $file,
        ]);

        $response->assertSessionHasErrors('document_file');
    }

    public function test_approve_file()
    {
        Notification::fake();
        $user = User::factory()->create();
        $user->givePermissionTo('approve document registration');
        $entry = DocumentRegistrationEntry::factory()->create();
        $file = DocumentRegistrationEntryFile::factory()->create([
            'entry_id' => $entry->id,
            'status' => 'pending',
        ]);
        $this->actingAs($user);

        $response = $this->post(route('document-registry.files.approve', $file->id));
        $response->assertRedirect();
        $this->assertDatabaseHas('document_registration_entry_files', [
            'id' => $file->id,
            'status' => 'approved',
            'implemented_by' => $user->id,
        ]);
        $this->assertDatabaseHas('document_registration_entries', [
            'id' => $entry->id,
            'status' => 'approved',
        ]);
    }

    public function test_cannot_approve_non_pending_file()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('approve document registration');
        $entry = DocumentRegistrationEntry::factory()->create();
        $file = DocumentRegistrationEntryFile::factory()->create([
            'entry_id' => $entry->id,
            'status' => 'approved',
        ]);
        $this->actingAs($user);

        $response = $this->post(route('document-registry.files.approve', $file->id));
        $response->assertStatus(403);
    }

    public function test_reject_file()
    {
        Notification::fake();
        $user = User::factory()->create();
        $user->givePermissionTo('reject document registration');
        $entry = DocumentRegistrationEntry::factory()->create();
        $file = DocumentRegistrationEntryFile::factory()->create([
            'entry_id' => $entry->id,
            'status' => 'pending',
        ]);
        $this->actingAs($user);

        $response = $this->post(route('document-registry.files.reject', $file->id), [
            'rejection_reason' => 'Invalid file format'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('document_registration_entry_files', [
            'id' => $file->id,
            'status' => 'rejected',
            'rejection_reason' => 'Invalid file format',
            'implemented_by' => $user->id,
        ]);
    }

    public function test_reject_file_requires_reason()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('reject document registration');
        $entry = DocumentRegistrationEntry::factory()->create();
        $file = DocumentRegistrationEntryFile::factory()->create([
            'entry_id' => $entry->id,
            'status' => 'pending',
        ]);
        $this->actingAs($user);

        $response = $this->post(route('document-registry.files.reject', $file->id), []);
        $response->assertSessionHasErrors('rejection_reason');
    }

    public function test_preview_file()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $user->givePermissionTo('view own document registrations');
        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $user->id]);
        $file = DocumentRegistrationEntryFile::factory()->create([
            'entry_id' => $entry->id,
            'file_path' => 'test/file.pdf',
            'mime_type' => 'application/pdf'
        ]);
        Storage::disk('local')->put('test/file.pdf', 'test pdf content');
        $this->actingAs($user);

        $response = $this->get(route('document-registry.files.preview', $file->id));
        $response->assertOk();
    }

    public function test_preview_unsupported_file_type()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $user->givePermissionTo('view own document registrations');
        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $user->id]);
        $file = DocumentRegistrationEntryFile::factory()->create([
            'entry_id' => $entry->id,
            'file_path' => 'test/file.txt',
            'mime_type' => 'text/plain'
        ]);
        Storage::disk('local')->put('test/file.txt', 'test content');
        $this->actingAs($user);

        $response = $this->get(route('document-registry.files.preview', $file->id));
        $response->assertStatus(400);
    }

    public function test_download_file()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $user->givePermissionTo('view own document registrations');
        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $user->id]);
        $file = DocumentRegistrationEntryFile::factory()->create([
            'entry_id' => $entry->id,
            'file_path' => 'test/file.pdf',
            'original_filename' => 'document.pdf'
        ]);
        Storage::disk('local')->put('test/file.pdf', 'test content');
        $this->actingAs($user);

        $response = $this->get(route('document-registry.files.download', $file->id));
        $response->assertOk();
    }

    public function test_cannot_download_nonexistent_file()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view own document registrations');
        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $user->id]);
        $file = DocumentRegistrationEntryFile::factory()->create([
            'entry_id' => $entry->id,
            'file_path' => 'nonexistent/file.pdf'
        ]);
        $this->actingAs($user);

        $response = $this->get(route('document-registry.files.download', $file->id));
        $response->assertStatus(404);
    }

    public function test_cannot_access_others_files()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $otherUser->id]);
        $file = DocumentRegistrationEntryFile::factory()->create([
            'entry_id' => $entry->id,
            'file_path' => 'test/file.pdf'
        ]);
        Storage::disk('local')->put('test/file.pdf', 'test content');
        $this->actingAs($user);

        $response = $this->get(route('document-registry.files.download', $file->id));
        $response->assertStatus(403);
    }
}
