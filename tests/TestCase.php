<?php

namespace Laborb\StatamicNotificationsChannel\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Laborb\StatamicNotificationsChannel\ServiceProvider;
use Statamic\Facades\YAML;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
    protected string $testDirectory;

    protected function getPackageProviders($app)
    {
        return [
            ...parent::getPackageProviders($app),
            \Illuminate\Notifications\SlackChannelServiceProvider::class,
            \NotificationChannels\MicrosoftTeams\MicrosoftTeamsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->testDirectory = sys_get_temp_dir().'/notifications-channel-'.bin2hex(random_bytes(8));
        $app['files']->makeDirectory($this->testDirectory, 0755, true);
        $app['config']->set('statamic.notifications.path', $this->testDirectory.'/addons/notifications.yaml');
        $app['config']->set('statamic.users.directory', $this->testDirectory.'/users');
        $app['config']->set('statamic.editions.pro', true);
        $app['config']->set('session.driver', 'array');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('logging.channels.single.path', $this->testDirectory.'/notifications.log');

        foreach ($app['config']->get('statamic.stache.stores') as $store => $config) {
            $app['config']->set("statamic.stache.stores.{$store}.directory", $this->testDirectory.'/content/'.$store);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
        Mail::fake();
        Queue::fake();
        $this->app->bind(Client::class, fn () => new Client(['handler' => HandlerStack::create(new MockHandler())]));
        $this->app->bind(\NotificationChannels\MicrosoftTeams\MicrosoftTeamsChannel::class, fn ($app) => new \NotificationChannels\MicrosoftTeams\MicrosoftTeamsChannel(
            new \NotificationChannels\MicrosoftTeams\MicrosoftTeams($app->make(Client::class))
        ));
        $outpost = \Mockery::mock(\Statamic\Licensing\Outpost::class);
        $outpost->shouldReceive('response')->andReturn(['statamic' => ['valid' => true], 'packages' => [], 'public' => false]);
        $this->app->instance(\Statamic\Licensing\Outpost::class, $outpost);
    }

    protected function tearDown(): void
    {
        if (isset($this->testDirectory)) {
            $this->app['files']->deleteDirectory($this->testDirectory);
        }

        parent::tearDown();
    }

    protected function settings(array $overrides = []): array
    {
        $settings = array_replace([
            'enable_notifications' => true,
            'channels' => [],
            'log_events' => false,
            'log_channel' => 'single',
        ], $overrides);
        $path = config('statamic.notifications.path');
        $this->app['files']->ensureDirectoryExists(dirname($path));
        $this->app['files']->put($path, YAML::dump($settings));

        return $settings;
    }

    protected function channel(string $service = 'mail', array $overrides = []): array
    {
        return array_replace([
            'type' => 'channel',
            'channel_name' => 'Test channel',
            'enabled' => true,
            'service' => $service,
            'events' => [\Statamic\Events\UserSaved::class],
            'email_address' => 'notifications@example.test',
            'slack_url' => 'https://chat.example.test/hooks/slack',
            'teams_url' => 'https://chat.example.test/hooks/teams',
            'webhook_url' => 'https://chat.example.test/hooks/webhook',
        ], $overrides);
    }
}
