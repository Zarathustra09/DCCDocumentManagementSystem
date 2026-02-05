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
//    use RefreshDatabase;
//
//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        Permission::create(['name' => 'submit document for approval']);
//        Permission::create(['name' => 'approve document registration']);
//        Permission::create(['name' => 'reject document registration']);
//        Permission::create(['name' => 'view all document registrations']);
//        Permission::create(['name' => 'view own document registrations']);
//    }
//
//    public function test_upload_file_to_entry()
//    {
//        Notification::fake();
//        Storage::fake('local');
//
//        $user = User::factory()->create();
//        $user->givePermissionTo('submit document for approval');
//        $this->actingAs($user);
//
//        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
//
//        $response = $this->post(route('document-registry.store'), [
//            'document_no' => 'DOC-TEST',
//            'document_title' => 'Test Document',
//            'revision_no' => 'A',
//            'originator_name' => 'Test Originator',
//            'document_file' => $file,
//        ]);
//
//        $response->assertRedirect();
//        $entry = DocumentRegistrationEntry::where('document_no', 'DOC-TEST')->first();
//        $this->assertNotNull($entry);
//
//        $uploadedFile = DocumentRegistrationEntryFile::where('entry_id', $entry->id)->first();
//        $this->assertNotNull($uploadedFile);
//        $this->assertEquals('test.pdf', $uploadedFile->original_filename);
//        $this->assertEquals('Pending', $uploadedFile->status->name);
//    }
//
//    public function test_cannot_upload_file_to_non_pending_entry()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('submit document for approval');
//        $entry = DocumentRegistrationEntry::factory()->approved()->create();
//        $this->actingAs($user);
//
//        Storage::fake('local');
//        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
//
//        // Approved entries cannot be edited, so update should fail
//        $response = $this->put(route('document-registry.update', $entry), [
//            'document_title' => 'Updated Title',
//            'revision_no' => 'B',
//            'originator_name' => 'Originator',
//        ]);
//
//        $response->assertStatus(403);
//    }
//
//    public function test_upload_validates_file_type()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('submit document for approval');
//        $this->actingAs($user);
//
//        Storage::fake('local');
//        $file = UploadedFile::fake()->create('test.exe', 100, 'application/exe');
//
//        $response = $this->post(route('document-registry.store'), [
//            'document_no' => 'DOC-TEST',
//            'document_title' => 'Test Document',
//            'revision_no' => 'A',
//            'originator_name' => 'Test Originator',
//            'document_file' => $file,
//        ]);
//
//        $response->assertSessionHasErrors('document_file');
//    }
//
//    public function test_upload_validates_file_size()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('submit document for approval');
//        $this->actingAs($user);
//
//        Storage::fake('local');
//        // Create a file larger than 1MB (1024KB) - use 1025KB to exceed limit
//        $file = UploadedFile::fake()->create('test.pdf', 1025, 'application/pdf');
//
//        $response = $this->post(route('document-registry.store'), [
//            'document_no' => 'DOC-TEST',
//            'document_title' => 'Test Document',
//            'revision_no' => 'A',
//            'originator_name' => 'Test Originator',
//            'document_file' => $file,
//        ]);
//
//        $response->assertSessionHasErrors('document_file');
//    }
//
//    public function test_approve_file()
//    {
//        Notification::fake();
//        $user = User::factory()->create();
//        $user->givePermissionTo('approve document registration');
//        $entry = DocumentRegistrationEntry::factory()->pending()->create();
//        $file = DocumentRegistrationEntryFile::factory()->pending()->create([
//            'entry_id' => $entry->id,
//        ]);
//        $this->actingAs($user);
//
//        $response = $this->post(route('document-registry.files.approve', $file->id));
//        $response->assertRedirect();
//        $file->refresh();
//        $this->assertEquals('Implemented', $file->status->name);
//        $this->assertEquals($user->id, $file->implemented_by);
//    }
//
//    public function test_cannot_approve_non_pending_file()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('approve document registration');
//        $entry = DocumentRegistrationEntry::factory()->create();
//        $file = DocumentRegistrationEntryFile::factory()->implemented()->create([
//            'entry_id' => $entry->id,
//        ]);
//        $this->actingAs($user);
//
//        $response = $this->post(route('document-registry.files.approve', $file->id));
//        $response->assertStatus(403);
//    }
//
//    public function test_reject_file()
//    {
//        Notification::fake();
//        $user = User::factory()->create();
//        $user->givePermissionTo('reject document registration');
//        $entry = DocumentRegistrationEntry::factory()->pending()->create();
//        $file = DocumentRegistrationEntryFile::factory()->pending()->create([
//            'entry_id' => $entry->id,
//        ]);
//        $this->actingAs($user);
//
//        $response = $this->post(route('document-registry.files.reject', $file->id), [
//            'rejection_reason' => 'Invalid file format'
//        ]);
//
//        $response->assertRedirect();
//        $file->refresh();
//        $this->assertEquals('Returned', $file->status->name);
//        $this->assertEquals('Invalid file format', $file->rejection_reason);
//        $this->assertEquals($user->id, $file->implemented_by);
//    }
//
//    public function test_reject_file_requires_reason()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('reject document registration');
//        $entry = DocumentRegistrationEntry::factory()->pending()->create();
//        $file = DocumentRegistrationEntryFile::factory()->pending()->create([
//            'entry_id' => $entry->id,
//        ]);
//        $this->actingAs($user);
//
//        $response = $this->post(route('document-registry.files.reject', $file->id), []);
//        $response->assertSessionHasErrors('rejection_reason');
//    }
//
//    public function test_preview_file()
//    {
//        Storage::fake('local');
//        $user = User::factory()->create();
//        $user->givePermissionTo('view own document registrations');
//        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $user->id]);
//        $file = DocumentRegistrationEntryFile::factory()->create([
//            'entry_id' => $entry->id,
//            'file_path' => 'test/file.pdf',
//            'mime_type' => 'application/pdf'
//        ]);
//        Storage::disk('local')->put('test/file.pdf', 'test pdf content');
//        $this->actingAs($user);
//
//        $response = $this->get(route('document-registry.preview-entry', $entry) . '?file_id=' . $file->id);
//        $response->assertOk();
//    }
//
//    public function test_preview_unsupported_file_type()
//    {
//        Storage::fake('local');
//        $user = User::factory()->create();
//        $user->givePermissionTo('view own document registrations');
//        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $user->id]);
//        $file = DocumentRegistrationEntryFile::factory()->create([
//            'entry_id' => $entry->id,
//            'file_path' => 'test/file.zip',
//            'mime_type' => 'application/zip'
//        ]);
//        Storage::disk('local')->put('test/file.zip', 'test content');
//        $this->actingAs($user);
//
//        $response = $this->get(route('document-registry.preview-entry', $entry) . '?file_id=' . $file->id);
//        $response->assertStatus(400);
//    }
//
//    public function test_download_file()
//    {
//        Storage::fake('local');
//        $user = User::factory()->create();
//        $user->givePermissionTo('view own document registrations');
//        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $user->id]);
//        $file = DocumentRegistrationEntryFile::factory()->create([
//            'entry_id' => $entry->id,
//            'file_path' => 'test/file.pdf',
//            'original_filename' => 'document.pdf'
//        ]);
//        Storage::disk('local')->put('test/file.pdf', 'test content');
//        $this->actingAs($user);
//
//        $response = $this->get(route('document-registry.download', $entry) . '?file_id=' . $file->id);
//        $response->assertOk();
//    }
//
//    public function test_cannot_download_nonexistent_file()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('view own document registrations');
//        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $user->id]);
//        $file = DocumentRegistrationEntryFile::factory()->create([
//            'entry_id' => $entry->id,
//            'file_path' => 'nonexistent/file.pdf'
//        ]);
//        $this->actingAs($user);
//
//        $response = $this->get(route('document-registry.download', $entry) . '?file_id=' . $file->id);
//        $response->assertStatus(404);
//    }
//
//    public function test_cannot_access_others_files()
//    {
//        Storage::fake('local');
//        $user = User::factory()->create();
//        $otherUser = User::factory()->create();
//        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $otherUser->id]);
//        $file = DocumentRegistrationEntryFile::factory()->create([
//            'entry_id' => $entry->id,
//            'file_path' => 'test/file.pdf'
//        ]);
//        Storage::disk('local')->put('test/file.pdf', 'test content');
//        $this->actingAs($user);
//
//        $response = $this->get(route('document-registry.download', $entry) . '?file_id=' . $file->id);
//        $response->assertStatus(403);
//    }
}
