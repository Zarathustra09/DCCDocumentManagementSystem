<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\DocumentRegistrationEntryFile;

class DocumentRegistryFileStatusUpdated extends Notification
{
    use Queueable;

    public $file;
    public $status;

    public function __construct(DocumentRegistrationEntryFile $file, $status)
    {
        $this->file = $file;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Document Registry File Status Updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The status of a file in your document registry entry has been updated.')
            ->line('File: ' . $this->file->original_filename)
            ->line('New Status: ' . $this->status)
            ->action('View Entry', url(route('document-registry.show', $this->file->entry_id)))
            ->line('Thank you for using our document registry system!');
    }
}
