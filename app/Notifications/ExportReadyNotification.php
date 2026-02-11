<?php

namespace App\Notifications;

use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class ExportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Export $export)
    {
        Log::info('ExportReadyNotification: queued', [
            'export_id' => $this->export->id,
            'control_no' => $this->export->control_no,
            'employee_no' => $this->export->employee_no,
        ]);
    }

    public function via($notifiable): array
    {
        Log::info('Export notification: via channels', [
            'export_id' => $this->export->id,
            'control_no' => $this->export->control_no,
            'recipient_email' => $notifiable->email ?? null,
        ]);

        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        $url = route('exports.download', $this->export);

        Log::info('Export notification: database payload', [
            'export_id' => $this->export->id,
            'control_no' => $this->export->control_no,
            'recipient_name' => $notifiable->name ?? null,
        ]);

        return [
            'title' => 'Your DCN Export is Complete',
            'body' => 'Your DCN export has been successfully processed and is now ready to download.',
            'action_text' => 'Download File',
            'action_url' => $url,
            'export_id' => $this->export->id,
            'control_no' => $this->export->control_no,
            'file_name' => basename($this->export->file_name),
            'completed_at' => $this->export->completed_at?->format('M d, Y h:i A'),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('exports.download', $this->export);
        $fileName = basename($this->export->file_name);

        Log::info('Export notification: mail payload', [
            'export_id' => $this->export->id,
            'control_no' => $this->export->control_no,
            'recipient_name' => $notifiable->name ?? null,
            'recipient_email' => $notifiable->email ?? null,
        ]);

        return (new MailMessage)
            ->subject('Your DCN Export is Ready for Download - ' . $this->export->control_no)
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . '!')
            ->line('Great news! Your DCN export has been successfully processed.')
            ->line('**Control No:** ' . $this->export->control_no)
            ->line('**File:** ' . $fileName)
            ->line('**Completed:** ' . ($this->export->completed_at?->format('F d, Y \a\t h:i A') ?? 'Just now'))
            ->action('Download Export', $url)
            ->line('Click the button above to download your export file. The download link will remain active for your convenience.')
            ->line('If you have any questions or need assistance, please contact support.')
            ->salutation('Best regards,
The ' . config('app.name') . ' Team')
            ->withSymfonyMessage(function ($message) {
                $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
                $messageId = $this->export->control_no . '-' . uniqid() . '@' . $host;
                $message->getHeaders()->addIdHeader('Message-ID', $messageId);
                $message->getHeaders()->remove('In-Reply-To');
                $message->getHeaders()->remove('References');
                $message->getHeaders()->addTextHeader('X-Export-Control-No', $this->export->control_no);
            });
    }

    public function id(): string
    {
        return 'export-ready-' . $this->export->control_no;
    }
}
