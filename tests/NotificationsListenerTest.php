<?php

namespace Laborb\StatamicNotificationsChannel\Tests;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Laborb\StatamicNotificationsChannel\Handlers\UserSavedHandler;
use Laborb\StatamicNotificationsChannel\Notifications\StatamicNotification;
use PHPUnit\Framework\Attributes\DataProvider;
use Statamic\Events\UserSaved;
use Statamic\Facades\User;
use Statamic\Facades\YAML;

class NotificationsListenerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    #[DataProvider('inactiveSettings')]
    public function test_events_without_an_active_matching_channel_send_nothing(array $settings): void
    {
        $this->settings($settings);
        $this->dispatchUserSaved();

        Notification::assertNothingSent();
    }

    public static function inactiveSettings(): array
    {
        $channel = [
            'type' => 'channel',
            'enabled' => true,
            'service' => 'mail',
            'email_address' => 'notifications@example.test',
            'events' => [UserSaved::class],
        ];

        return [
            'global switch off' => [['enable_notifications' => false, 'channels' => [$channel]]],
            'channel off' => [['channels' => [array_replace($channel, ['enabled' => false])]]],
            'event not selected' => [['channels' => [array_replace($channel, ['events' => [\Statamic\Events\EntrySaved::class]])]]],
            'no channels' => [['channels' => []]],
            'null channels' => [['channels' => null]],
            'no events' => [['channels' => [array_replace($channel, ['events' => []])]]],
            'missing events' => [['channels' => [array_diff_key($channel, ['events' => true])]]],
        ];
    }

    public function test_missing_settings_file_sends_nothing(): void
    {
        $this->dispatchUserSaved();
        Notification::assertNothingSent();
    }

    public function test_missing_channel_list_sends_nothing(): void
    {
        $this->settings();
        file_put_contents(config('statamic.notifications.path'), YAML::dump(['enable_notifications' => true]));

        $this->dispatchUserSaved();
        Notification::assertNothingSent();
    }

    public function test_legacy_settings_without_logging_flags_can_send_notifications(): void
    {
        $settings = $this->settings(['channels' => [$this->channel()]]);
        unset($settings['log_events'], $settings['log_channel']);
        file_put_contents(config('statamic.notifications.path'), YAML::dump($settings));

        $this->dispatchUserSaved();
        Notification::assertCount(1);
    }

    public function test_each_matching_channel_receives_the_event_at_its_configured_recipient(): void
    {
        $channels = array_map(fn ($service) => $this->channel($service), ['mail', 'slack', 'teams', 'webhook']);
        $this->settings(['channels' => $channels]);
        $event = $this->dispatchUserSaved();

        Notification::assertCount(4);
        foreach ($channels as $channel) {
            $service = $channel['service'];
            $handle = config('statamic.notifications.channels.'.$service.'.handle');
            Notification::assertSentOnDemand(StatamicNotification::class, function ($notification, $via, $notifiable) use ($service, $handle, $channel, $event) {
                if ($notifiable->routes !== [$service => $channel[$handle]]) {
                    return false;
                }
                if ($service === 'webhook') {
                    $this->assertSame(UserSavedHandler::class, $notification->event['event']);
                    $this->assertSame(serialize($event), $notification->event['info']);
                } else {
                    $this->assertSame(['event' => 'User Saved', 'info' => '**member@example.test**'], $notification->event);
                }

                return $notifiable->routes === [$service => $channel[$handle]];
            });
        }
    }

    public function test_settings_are_read_once_per_event(): void
    {
        $this->settings(['channels' => [$this->channel(), $this->channel('slack')], 'log_events' => true]);
        $parser = YAML::file(config('statamic.notifications.path'));
        YAML::shouldReceive('file')->once()->with(config('statamic.notifications.path'))->andReturn($parser);

        $this->dispatchUserSaved();

        Notification::assertCount(2);
    }

    public function test_notifications_are_logged_to_the_selected_channel(): void
    {
        $this->settings(['channels' => [$this->channel()], 'log_events' => true, 'log_channel' => 'notifications-test']);
        $logger = \Mockery::mock(\Psr\Log\LoggerInterface::class);
        $logger->shouldReceive('info')->once()->with(UserSaved::class.' notification sent to mail(notifications@example.test)');
        Log::shouldReceive('channel')->once()->with('notifications-test')->andReturn($logger);

        $this->dispatchUserSaved();
        Notification::assertCount(1);
    }

    public function test_disabled_logging_does_not_write_a_log(): void
    {
        $this->settings(['channels' => [$this->channel()]]);
        Log::shouldReceive('channel')->never();

        $this->dispatchUserSaved();
        Notification::assertCount(1);
    }

    private function dispatchUserSaved(): UserSaved
    {
        $event = new UserSaved(User::make()->id('member')->email('member@example.test'));
        event($event);

        return $event;
    }
}
