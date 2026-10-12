<?php

namespace Laborb\StatamicNotificationsChannel\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Laborb\StatamicNotificationsChannel\Notifications\StatamicNotification;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsChannel;
use NotificationChannels\Webhook\WebhookChannel;
use PHPUnit\Framework\Attributes\DataProvider;

class StatamicNotificationTest extends TestCase
{
    #[DataProvider('channels')]
    public function test_routes_select_the_correct_delivery_channel(string $service, string $channel): void
    {
        $notifiable = (new AnonymousNotifiable())->route($service, 'recipient');
        $notification = new StatamicNotification(['event' => 'User Saved', 'info' => '**member@example.test**']);

        $this->assertSame([$channel], $notification->via($notifiable));
    }

    public static function channels(): array
    {
        return [
            'mail' => ['mail', 'mail'],
            'Slack / Mattermost incoming webhook' => ['slack', 'slack'],
            'Teams' => ['teams', MicrosoftTeamsChannel::class],
            'webhook' => ['webhook', WebhookChannel::class],
        ];
    }

    public function test_mail_message_retains_the_subject_and_event_information(): void
    {
        config(['app.name' => 'Example site']);
        $message = (new StatamicNotification(['event' => 'User Saved', 'info' => '**member@example.test**']))
            ->toMail(new AnonymousNotifiable());

        $this->assertSame('Example site: User Saved', $message->subject);
        $this->assertSame(['User Saved: **member@example.test**'], $message->introLines);
    }

    #[DataProvider('webhookServices')]
    public function test_webhook_transports_receive_the_expected_url_and_payload(string $service): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200)]));
        $stack->push(Middleware::history($history));
        $this->app->instance(Client::class, new Client(['handler' => $stack]));
        $url = 'https://chat.example.test/hooks/'.$service;
        $event = ['event' => 'User Saved', 'info' => '**member@example.test**'];

        Notification::route($service, $url)->notify(new StatamicNotification($event));

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame($url, (string) $request->getUri());
        $payload = json_decode((string) $request->getBody(), true, flags: JSON_THROW_ON_ERROR);

        if ($service === 'slack') {
            $this->assertSame('User Saved: **member@example.test**', $payload['text']);
        } elseif ($service === 'teams') {
            $this->assertSame('User Saved', $payload['title']);
            $this->assertSame('**member@example.test**', $payload['text']);
        } else {
            $this->assertSame(['event' => $event['event'], 'serialized' => $event['info']], $payload);
            $this->assertSame('Statamic-Notifications-Agent', $request->getHeaderLine('User-Agent'));
            $this->assertSame('application/json', $request->getHeaderLine('content-type'));
        }
    }

    public static function webhookServices(): array
    {
        return [['slack'], ['teams'], ['webhook']];
    }
}
