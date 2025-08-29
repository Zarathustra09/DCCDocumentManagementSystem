<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\DocumentRegistrationEntryFile;

class DocumentRegistryFileCreated extends Notification
{
    use Queueable;

    public $file;

    public function __construct(DocumentRegistrationEntryFile $file)
    {
        $this->file = $file;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New File Submitted to Document Registry Entry')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new file has been submitted to a document registry entry.')
            ->line('File: ' . $this->file->original_filename)
            ->line('Entry ID: ' . $this->file->entry_id)
            ->action('View Entry', url(route('document-registry.show', $this->file->entry_id)))
            ->line('Thank you for using our document registry system!');
    }
}
