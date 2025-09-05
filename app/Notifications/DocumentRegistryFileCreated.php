<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\DocumentRegistrationEntryFile;
use App\Models\User;

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
       \Log::info('Sending DocumentRegistryFileCreated notification', [
           'recipient' => $notifiable->email,
           'filename' => $this->file->original_filename,
           'document_no' => $this->file->registrationEntry->document_no,
           'submitted_by' => $this->file->registrationEntry->submittedBy->name
       ]);

       return (new MailMessage)
           ->subject('New File Submitted - ' . $this->file->original_filename)
           ->greeting('Hello ' . $notifiable->name . ',')
           ->line('A new file has been submitted to a document registry entry.')
           ->line('File: ' . $this->file->original_filename)
           ->line('Document No: ' . $this->file->registrationEntry->document_no)
           ->line('Submitted by: ' . $this->file->registrationEntry->submittedBy->name)
           ->action('View Entry', url(route('document-registry.show', $this->file->registrationEntry->document_no)))
           ->line('Thank you for using our document management system!');
   }

    public function uniqueId()
    {
        return 'file-created-' . $this->file->original_filename . '-' . time();
    }

    public static function sendToAdmins(DocumentRegistrationEntryFile $file)
    {
        $admins = User::role(['SuperAdmin', 'DCCAdmin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new static($file));
        }
    }
}
