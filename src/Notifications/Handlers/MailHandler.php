<?php

namespace Laborb\StatamicNotifications\Notifications\Handlers;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;

class MailHandler extends Notification {
    public function send($notifiable, $event) {
        //Log::info($event);

        return (new MailMessage)
                    ->subject(config('app.name') . ': ' . $event['event'])
                    ->line($event['event'] . ': ' . $event['info']);
    }
}
