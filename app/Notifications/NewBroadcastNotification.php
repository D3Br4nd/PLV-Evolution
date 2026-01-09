<?php

namespace App\Notifications;

use App\Models\BroadcastNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class NewBroadcastNotification extends Notification
{
    protected BroadcastNotification $broadcast;

    /**
     * Create a new notification instance.
     */
    public function __construct(BroadcastNotification $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        // Extract plain text from HTML content for the body
        $plainBody = strip_tags($this->broadcast->content);
        $shortBody = mb_strlen($plainBody) > 100 
            ? mb_substr($plainBody, 0, 100) . '...' 
            : $plainBody;

        return (new WebPushMessage)
            ->title($this->broadcast->title)
            ->body($shortBody)
            ->icon('/favicon.png')
            ->data([
                'url' => '/me/broadcasts/' . $this->broadcast->id,
                'broadcast_id' => $this->broadcast->id,
            ])
            ->badge('/favicon.png');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $plainBody = strip_tags($this->broadcast->content);
        $shortBody = mb_strlen($plainBody) > 100 
            ? mb_substr($plainBody, 0, 100) . '...' 
            : $plainBody;

        return [
            'title' => $this->broadcast->title,
            'body' => $shortBody,
            'broadcast_id' => $this->broadcast->id,
            'url' => '/me/broadcasts/' . $this->broadcast->id,
            'type' => 'broadcast',
            'has_image' => !empty($this->broadcast->featured_image_path),
            'has_attachment' => !empty($this->broadcast->attachment_path),
        ];
    }
}
