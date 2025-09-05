<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\DocumentRegistrationEntry;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DocumentRegistryEntryCreated extends Notification
{
    use Queueable;

    public $entry;

    public function __construct(DocumentRegistrationEntry $entry)
    {
        $this->entry = $entry;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        \Log::info('Sending DocumentRegistryEntryCreated notification', [
            'recipient' => $notifiable->email,
            'document_no' => $this->entry->document_no,
            'document_title' => $this->entry->document_title,
            'submitted_by' => $this->entry->submittedBy->name
        ]);

        return (new MailMessage)
            ->subject('New Document Registry Entry Created - ' . $this->entry->document_no)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new document registry entry has been created.')
            ->line('Document: ' . $this->entry->document_title)
            ->line('Document No: ' . $this->entry->document_no)
            ->line('Submitted by: ' . $this->entry->submittedBy->name)
            ->action('View Entry', url(route('document-registry.show', $this->entry->document_no)))
            ->line('Thank you for using our document management system!');
    }

    public function uniqueId()
    {
        return 'entry-created-' . $this->entry->document_no . '-' . time();
    }

    public static function sendToAdmins(DocumentRegistrationEntry $entry)
    {
        $admins = User::role(['SuperAdmin', 'DCCAdmin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new static($entry));
        }
    }
}
