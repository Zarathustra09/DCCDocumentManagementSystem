<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryStatus;
use App\Models\User;

class DocumentRegistryEntryStatusUpdated extends Notification
{
    use Queueable;

    public $entry;
    public $status;

    public function __construct(DocumentRegistrationEntry $entry, DocumentRegistrationEntryStatus $status)
    {
        $this->entry = $entry;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        \Log::info('Sending DocumentRegistryEntryStatusUpdated notification', [
            'recipient' => $notifiable->email,
            'document_no' => $this->entry->document_no,
            'document_title' => $this->entry->document_title,
            'new_status' => $this->status->name,
            'submitted_by' => $this->entry->submittedBy->name
        ]);

        return (new MailMessage)
            ->subject('Document Registry Entry Status Updated - ' . $this->entry->document_no)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The status of a document registry entry has been updated.')
            ->line('Document: ' . $this->entry->document_title)
            ->line('Document No: ' . $this->entry->document_no)
            ->line('Submitted by: ' . $this->entry->submittedBy->name)
            ->line('New Status: ' . $this->status->name)
            ->action('View Entry', url(route('document-registry.show', $this->entry->document_no)))
            ->line('Thank you for using our document management system!');
    }

    public function uniqueId()
    {
        return 'entry-status-updated-' . $this->entry->document_no . '-' . $this->status->id . '-' . time();
    }

    public static function sendToAdmins(DocumentRegistrationEntry $entry, DocumentRegistrationEntryStatus $status)
    {
        $admins = User::role(['SuperAdmin', 'DCCAdmin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new static($entry, $status));
        }
    }
}
