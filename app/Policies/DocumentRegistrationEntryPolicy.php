<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DocumentRegistrationEntry;

class DocumentRegistrationEntryPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given user can create (submit) a document registration entry.
     * Uses Spatie permission 'submit document for approval'.
     */
    public function create(User $user): bool
    {
        return $user->can('submit document for approval');
    }

    /**
     * Determine if the given user can view the document registration entry.
     *
     * Preserve exact previous helper logic:
     * return Auth::user()->can('view all document registrations')
     *     || (Auth::user()->can('view own document registrations') && $entry->submitted_by === Auth::id())
     *     || ($entry->submitted_by === Auth::id());
     */
    public function view(User $user, DocumentRegistrationEntry $entry): bool
    {
        return $user->can('view all document registrations')
            || ($user->can('view own document registrations') && $entry->submitted_by === $user->id)
            || ($entry->submitted_by === $user->id);
    }

    /**
     * Determine if the given user can update (edit) the document registration entry.
     *
     * Rules preserved:
     * - Users with Spatie permission 'edit document registration details' may edit.
     * - The submitter may edit while the entry status is 'Pending'.
     */
    public function update(User $user, DocumentRegistrationEntry $entry): bool
    {
        if ($user->can('edit document registration details')) {
            return true;
        }

        return $entry->submitted_by === $user->id && (($entry->status->name ?? '') === 'Pending');
    }

    /**
     * Determine if the given user can approve the document registration entry.
     * - User must have 'approve document registration' permission
     * - Entry must be in 'Pending' status
     */
    public function approve(User $user, DocumentRegistrationEntry $entry): bool
    {
        return $user->can('approve document registration')
            && (($entry->status->name ?? '') === 'Pending');
    }

    /**
     * Determine if the given user can reject the document registration entry.
     * - User must have 'reject document registration' permission
     * - Entry must be in 'Pending' status
     */
    public function reject(User $user, DocumentRegistrationEntry $entry): bool
    {
        return $user->can('reject document registration')
            && (($entry->status->name ?? '') === 'Pending');
    }

    /**
     * Determine if the given user can request a revision for the document registration entry.
     * - User must have 'require revision for document' permission
     * - Entry must be in 'Pending' status
     */
    public function requireRevision(User $user, DocumentRegistrationEntry $entry): bool
    {
        return $user->can('require revision for document')
            && (($entry->status->name ?? '') === 'Pending');
    }
}
