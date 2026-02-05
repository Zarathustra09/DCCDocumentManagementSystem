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

class DocumentRegistrationEntryControllerTest extends TestCase
{
//    use RefreshDatabase;
//
//    protected function setUp(): void
//    {
//        parent::setUp();
//        Permission::create(['name' => 'submit document for approval']);
//        Permission::create(['name' => 'approve document registration']);
//        Permission::create(['name' => 'reject document registration']);
//        Permission::create(['name' => 'require revision for document']);
//        Permission::create(['name' => 'withdraw document submission']);
//        Permission::create(['name' => 'view all document registrations']);
//        Permission::create(['name' => 'view own document registrations']);
//        Permission::create(['name' => 'edit document registration details']);
//        Permission::create(['name' => 'bulk approve document registrations']);
//        Permission::create(['name' => 'bulk reject document registrations']);
//        Permission::create(['name' => 'reassign document approver']);
//        Permission::create(['name' => 'override approval process']);
//    }
//
//    public function test_user_can_view_own_documents_index()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('view own document registrations');
//        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $user->id]);
//        $this->actingAs($user);
//
//        $response = $this->get(route('document-registry.index'));
//        $response->assertOk();
//        $response->assertSee($entry->document_no);
//    }
//
//    public function test_user_can_create_document_registration_entry()
//    {
//        Notification::fake();
//        $user = User::factory()->create();
//        $user->givePermissionTo('submit document for approval');
//        $this->actingAs($user);
//
//        Storage::fake('local');
//        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
//
//        $response = $this->post(route('document-registry.store'), [
//            'document_no' => 'DOC-001',
//            'document_title' => 'Test Document',
//            'revision_no' => 'A',
//            'originator_name' => 'Originator',
//            'document_file' => $file,
//        ]);
//
//        $response->assertRedirect();
//        $this->assertDatabaseHas('document_registration_entries', [
//            'document_no' => 'DOC-001',
//            'document_title' => 'Test Document',
//        ]);
//        $entry = DocumentRegistrationEntry::where('document_no', 'DOC-001')->first();
//        $this->assertDatabaseHas('document_registration_entry_files', [
//            'entry_id' => $entry->id,
//            'original_filename' => 'test.pdf',
//        ]);
//    }
//
//    public function test_user_cannot_create_without_permission()
//    {
//        $user = User::factory()->create();
//        $this->actingAs($user);
//
//        $response = $this->post(route('document-registry.store'), [
//            'document_no' => 'DOC-001',
//            'document_title' => 'Test Document',
//            'revision_no' => 'A',
//            'originator_name' => 'Originator',
//        ]);
//
//        $response->assertStatus(403);
//    }
//
//    public function test_user_can_view_own_document()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('view own document registrations');
//        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $user->id]);
//        $this->actingAs($user);
//
//        $response = $this->get(route('document-registry.show', $entry));
//        $response->assertOk();
//        $response->assertSee($entry->document_title);
//    }
//
//    public function test_user_cannot_view_others_document_without_permission()
//    {
//        $user = User::factory()->create();
//        $otherUser = User::factory()->create();
//        $entry = DocumentRegistrationEntry::factory()->create(['submitted_by' => $otherUser->id]);
//        $this->actingAs($user);
//
//        $response = $this->get(route('document-registry.show', $entry));
//        $response->assertStatus(403);
//    }
//
//    public function test_user_can_edit_own_pending_document()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('edit document registration details');
//        $entry = DocumentRegistrationEntry::factory()->pending()->create([
//            'submitted_by' => $user->id,
//        ]);
//        $this->actingAs($user);
//
//        $response = $this->put(route('document-registry.update', $entry), [
//            'document_title' => 'Updated Title',
//            'revision_no' => 'B',
//            'originator_name' => 'Originator',
//        ]);
//
//        $response->assertRedirect();
//        $this->assertDatabaseHas('document_registration_entries', [
//            'id' => $entry->id,
//            'document_title' => 'Updated Title',
//        ]);
//    }
//
//    public function test_user_cannot_edit_approved_document()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('edit document registration details');
//        $entry = DocumentRegistrationEntry::factory()->approved()->create([
//            'submitted_by' => $user->id,
//        ]);
//        $this->actingAs($user);
//
//        $response = $this->put(route('document-registry.update', $entry), [
//            'document_title' => 'Updated Title',
//            'revision_no' => 'B',
//            'originator_name' => 'Originator',
//        ]);
//
//        $response->assertStatus(403);
//    }
//
//    public function test_approve_document_registration_entry()
//    {
//        Notification::fake();
//        $user = User::factory()->create();
//        $user->givePermissionTo('approve document registration');
//        $entry = DocumentRegistrationEntry::factory()->pending()->create();
//        DocumentRegistrationEntryFile::factory()->pending()->create([
//            'entry_id' => $entry->id,
//        ]);
//        $this->actingAs($user);
//
//        $response = $this->post(route('document-registry.approve', $entry));
//        $response->assertRedirect();
//        $entry->refresh();
//        $this->assertEquals('Approved', $entry->status->name);
//    }
//
//    public function test_reject_document_registration_entry()
//    {
//        Notification::fake();
//        $user = User::factory()->create();
//        $user->givePermissionTo('reject document registration');
//        $entry = DocumentRegistrationEntry::factory()->pending()->create();
//        $this->actingAs($user);
//
//        $response = $this->post(route('document-registry.reject', $entry), [
//            'rejection_reason' => 'Invalid document',
//        ]);
//
//        $response->assertRedirect();
//        $entry->refresh();
//        $this->assertEquals('Rejected', $entry->status->name);
//        $this->assertEquals('Invalid document', $entry->rejection_reason);
//    }
//
//    public function test_reject_requires_reason()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('reject document registration');
//        $entry = DocumentRegistrationEntry::factory()->pending()->create();
//        $this->actingAs($user);
//
//        $response = $this->post(route('document-registry.reject', $entry), []);
//        $response->assertSessionHasErrors('rejection_reason');
//    }
//
//    public function test_require_revision()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('require revision for document');
//        $entry = DocumentRegistrationEntry::factory()->pending()->create();
//        $this->actingAs($user);
//
//        $response = $this->post(route('document-registry.require-revision', $entry), [
//            'revision_notes' => 'Please revise',
//        ]);
//
//        $response->assertRedirect();
//        $entry->refresh();
//        $this->assertEquals('Rejected', $entry->status->name);
//    }
//
//    public function test_user_can_withdraw_own_pending_document()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('withdraw document submission');
//        $entry = DocumentRegistrationEntry::factory()->pending()->create([
//            'submitted_by' => $user->id,
//        ]);
//        $file = DocumentRegistrationEntryFile::factory()->create(['entry_id' => $entry->id]);
//        $this->actingAs($user);
//
//        $response = $this->delete(route('document-registry.withdraw', $entry));
//        $response->assertRedirect();
//        $this->assertDatabaseMissing('document_registration_entries', ['id' => $entry->id]);
//        $this->assertDatabaseMissing('document_registration_entry_files', ['id' => $file->id]);
//    }
//
//    public function test_user_cannot_withdraw_others_document()
//    {
//        $user = User::factory()->create();
//        $otherUser = User::factory()->create();
//        $user->givePermissionTo('withdraw document submission');
//        $entry = DocumentRegistrationEntry::factory()->pending()->create([
//            'submitted_by' => $otherUser->id,
//        ]);
//        $this->actingAs($user);
//
//        $response = $this->delete(route('document-registry.withdraw', $entry));
//        $response->assertStatus(403);
//    }
//
//    public function test_bulk_approve()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('bulk approve document registrations');
//        $entries = DocumentRegistrationEntry::factory()->pending()->count(3)->create();
//        $this->actingAs($user);
//
//        $response = $this->post(route('document-registry.bulk-approve'), [
//            'entries' => $entries->pluck('id')->toArray(),
//        ]);
//
//        $response->assertRedirect();
//        foreach ($entries as $entry) {
//            $entry->refresh();
//            $this->assertEquals('Approved', $entry->status->name);
//        }
//    }
//
//    public function test_bulk_reject()
//    {
//        $user = User::factory()->create();
//        $user->givePermissionTo('bulk reject document registrations');
//        $entries = DocumentRegistrationEntry::factory()->pending()->count(3)->create();
//        $this->actingAs($user);
//
//        $response = $this->post(route('document-registry.bulk-reject'), [
//            'entries' => $entries->pluck('id')->toArray(),
//            'rejection_reason' => 'Bulk reject reason',
//        ]);
//
//        $response->assertRedirect();
//        foreach ($entries as $entry) {
//            $entry->refresh();
//            $this->assertEquals('Rejected', $entry->status->name);
//            $this->assertEquals('Bulk reject reason', $entry->rejection_reason);
//        }
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
//        $response = $this->get(route('document-registry.download', $entry));
//        $response->assertOk();
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
//        $response = $this->get(route('document-registry.preview', $entry) . '?file_id=' . $file->id);
//        $response->assertOk();
//    }
}
