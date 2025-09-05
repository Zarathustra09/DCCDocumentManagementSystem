<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\DocumentRegistrationEntryFile;
use App\Models\User;

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
        \Log::info('Sending DocumentRegistryFileStatusUpdated notification', [
            'recipient' => $notifiable->email,
            'filename' => $this->file->original_filename,
            'document_no' => $this->file->registrationEntry->document_no,
            'new_status' => $this->file->status->name,
            'submitted_by' => $this->file->registrationEntry->submittedBy->name
        ]);

        return (new MailMessage)
            ->subject('Document Registry File Status Updated - ' . $this->file->original_filename)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The status of a file in a document registry entry has been updated.')
            ->line('File: ' . $this->file->original_filename)
            ->line('Document No: ' . $this->file->registrationEntry->document_no)
            ->line('Submitted by: ' . $this->file->registrationEntry->submittedBy->name)
            ->line('New Status: ' . $this->file->status->name)
            ->action('View Entry', url(route('document-registry.show', $this->file->registrationEntry->document_no)))
            ->line('Thank you for using our document management system!');
    }

    public function uniqueId()
    {
        return 'file-status-updated-' . $this->file->original_filename . '-' . $this->status . '-' . time();
    }

    public static function sendToAdmins(DocumentRegistrationEntryFile $file, $status)
    {
        $admins = User::role(['SuperAdmin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new static($file, $status));
        }
    }
}
