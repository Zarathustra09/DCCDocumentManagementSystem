<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\DocumentRegistrationEntry;

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
        return (new MailMessage)
            ->subject('New Document Registry Entry Created')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new document registry entry has been created.')
            ->line('Document: ' . $this->entry->document_title)
            ->line('Document No: ' . $this->entry->document_no)
            ->action('View Entry', url(route('document-registry.show', $this->entry->id)))
            ->line('Thank you for using our document registry system!');
    }
}
