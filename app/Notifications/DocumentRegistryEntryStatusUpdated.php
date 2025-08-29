<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\DocumentRegistrationEntry;

class DocumentRegistryEntryStatusUpdated extends Notification
{
    use Queueable;

    public $entry;
    public $status;

    public function __construct(DocumentRegistrationEntry $entry, $status)
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
        return (new MailMessage)
            ->subject('Document Registry Entry Status Updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The status of your document registry entry has been updated.')
            ->line('Document: ' . $this->entry->document_title)
            ->line('Document No: ' . $this->entry->document_no)
            ->line('New Status: ' . $this->status)
            ->action('View Entry', url(route('document-registry.show', $this->entry->id)))
            ->line('Thank you for using our document registry system!');
    }
}
