<?php

namespace Laborb\StatamicNotificationsChannel\Listeners;

use Illuminate\Support\Facades\Notification;
use Laborb\StatamicNotificationsChannel\Blueprints\Notifications;
use Laborb\StatamicNotificationsChannel\Notifications\StatamicNotification;
use Illuminate\Support\Facades\Log;

class NotificationsListener
{
    /**
     * Handle the incoming event.
     *
     * @param Statamic\Event $incomingEvent
     * @return void
     */
    public function handle($incomingEvent, $data): void
    {
        if (!Notifications::values()['enable_notifications']) {
            return;
        }

        $channels = collect(Notifications::values()['channels'])->where('enabled', true);

        foreach ($channels as $channel) {
            foreach ($channel['events'] as $event) {
                if ($event == $incomingEvent) {
                    if ($channel['service'] == 'webhook') {
                        $info = [
                            'event' => config('statamic.notifications.events.' . $incomingEvent),
                            'info' => serialize($data[0])
                        ];
                    } else {
                        $info = $this->getEventData($incomingEvent, $data);
                    }

                    $handle = config('statamic.notifications.channels.' . $channel['service'] . '.handle');

                    Notification::route($channel['service'], $channel[$handle])
                        ->notify(new StatamicNotification($info));

                    if (Notifications::values()['log_events'] && $channel['enabled']) {
                        Log::channel(Notifications::values()['log_channel'])->info($incomingEvent . ' notification sent to ' . $channel['service'] . '(' . $channel[$handle] . ')');
                    }
                }
            }
        }
    }

    /**
     * Extract event data.
     *
     * @param getEventData $event, $data
     * @return Array
     */
    public function getEventData($event, $data): array
    {
        $formattedData = [
            'event' => Notifications::getClassNames()[$event],
        ];

        if (config('statamic.notifications.events.' . $event) !== NULL) {
            $handler = config('statamic.notifications.events.' . $event);
            $formattedData['info'] = (new $handler())->info($data);
        }
        
        return $formattedData;
    }
}