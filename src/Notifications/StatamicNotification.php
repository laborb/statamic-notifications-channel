<?php

namespace Laborb\StatamicNotifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Webhook\WebhookChannel;
use NotificationChannels\Webhook\WebhookMessage;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsChannel;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsMessage;

class StatamicNotification extends Notification
{
    public $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if (array_key_exists('mail', $notifiable->routes)) return ['mail'];
        if (array_key_exists('slack', $notifiable->routes)) return ['slack'];
        if (array_key_exists('teams', $notifiable->routes)) return [MicrosoftTeamsChannel::class];
        if (array_key_exists('webhook', $notifiable->routes)) return [WebhookChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(config('app.name') . ': ' . $this->event['event'])
                    ->line($this->event['event'] . ': ' . $this->event['info']);
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        return (new SlackMessage)
                    ->content($this->event['event'] . ': ' . $this->event['info']);
    }

    /**
     * Get the Microsoft Teams representation of the notification.
     */
    public function toMicrosoftTeams($notifiable): MicrosoftTeamsMessage
    {
        return MicrosoftTeamsMessage::create()
            ->to($notifiable->routes['teams'])
            ->type('primary')
            ->title($this->event['event'])
            ->content($this->event['info']);
    }

    /**
     * Get the webhook representation of the notification.
     */
    public function toWebhook($notifiable): WebhookMessage
    {
        return WebhookMessage::create()
            ->data([
               'event' => $this->event['event'],
               'serialized' => $this->event['info']
            ])
            ->userAgent("Statamic-Notifications-Agent")
            ->header('content-type', 'application/json');
    }
}
