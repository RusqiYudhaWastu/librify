<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    private $title;
    private $message;
    private $link;
    private $type; // success, warning, danger, info

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $link = '#', $type = 'info')
    {
        $this->title = $title;
        $this->message = $message;
        $this->link = $link;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Kita simpan ke database biar muncul di lonceng
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'link' => $this->link,
            'type' => $this->type,
        ];
    }
}