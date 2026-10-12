<?php

namespace Laborb\StatamicNotificationsChannel\Tests;

use Inertia\Testing\AssertableInertia;
use Laborb\StatamicNotificationsChannel\Blueprints\Notifications;
use PHPUnit\Framework\Attributes\DataProvider;
use Statamic\Facades\User;

class NotificationsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::make()->id('test-admin')->email('admin@example.test')->makeSuper());
    }

    public function test_utility_renders_the_native_publish_form_with_existing_settings(): void
    {
        $settings = $this->settings(['channels' => [$this->channel()]]);

        $this->get(cp_route('utilities.notifications-channel'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('PublishForm')
                ->where('title', __('statamic-notifications-channel::cp.general.headline'))
                ->where('submitUrl', cp_route('laborb.notifications.update'))
                ->where('submitMethod', 'PUT')
                ->where('asConfig', true)
                ->where('blueprint.tabs.0.handle', 'channels')
                ->where('blueprint.tabs.1.handle', 'settings')
                ->where('values.enable_notifications', true)
                ->where('values.channels.0.type', 'channel')
                ->where('values.channels.0.service', 'mail')
                ->where('values.channels.0.email_address', $settings['channels'][0]['email_address'])
                ->where('values.channels.0.enabled', true));
    }

    public function test_settings_are_saved_and_reloaded_without_migrating_channel_handles(): void
    {
        $settings = $this->settings(['channels' => [
            $this->channel(),
            $this->channel('slack', ['enabled' => false]),
            $this->channel('teams'),
            $this->channel('webhook'),
        ]]);
        $settings['channels'][0]['channel_name'] = 'Changed name';
        $settings['log_events'] = true;

        $this->putJson(cp_route('laborb.notifications.update'), $settings)->assertSuccessful();

        $saved = Notifications::values();
        $this->assertTrue($saved['log_events']);
        $this->assertSame('single', $saved['log_channel']);
        $this->assertCount(4, $saved['channels']);
        $this->assertSame('Changed name', $saved['channels'][0]['channel_name']);
        $this->assertFalse($saved['channels'][1]['enabled']);

        foreach ($settings['channels'] as $index => $channel) {
            $this->assertSame('channel', $saved['channels'][$index]['type']);
            $this->assertSame($channel['events'], $saved['channels'][$index]['events']);
            $handle = config('statamic.notifications.channels.'.$channel['service'].'.handle');
            $this->assertSame($channel[$handle], $saved['channels'][$index][$handle]);
        }

        $this->getJson(cp_route('laborb.notifications.index'))
            ->assertOk()
            ->assertJsonPath('values.channels.0.channel_name', 'Changed name');
    }

    #[DataProvider('invalidRecipients')]
    public function test_invalid_recipients_do_not_overwrite_settings(string $service, string $handle): void
    {
        $settings = $this->settings(['channels' => [$this->channel($service)]]);
        $original = file_get_contents(config('statamic.notifications.path'));
        $settings['channels'][0][$handle] = 'invalid';

        $this->putJson(cp_route('laborb.notifications.update'), $settings)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('channels.0.'.$handle);

        $this->assertSame($original, file_get_contents(config('statamic.notifications.path')));
    }

    public static function invalidRecipients(): array
    {
        return [
            'mail' => ['mail', 'email_address'],
            'slack / Mattermost' => ['slack', 'slack_url'],
            'Teams' => ['teams', 'teams_url'],
            'webhook' => ['webhook', 'webhook_url'],
        ];
    }

    public function test_missing_settings_file_can_be_opened_and_created(): void
    {
        $this->assertSame([], Notifications::values());
        $this->assertContains(config('statamic.notifications.path'), array_values(\Illuminate\Support\ServiceProvider::pathsToPublish(null, 'laborb-notifications-content')));
        $this->get(cp_route('utilities.notifications-channel'))->assertOk();

        $this->putJson(cp_route('laborb.notifications.update'), [
            'enable_notifications' => false,
            'channels' => [],
            'log_events' => false,
            'log_channel' => 'single',
        ])->assertSuccessful();

        $this->assertFalse(Notifications::values()['enable_notifications']);
    }

    public function test_channels_can_be_removed_and_notifications_disabled(): void
    {
        $settings = $this->settings(['channels' => [$this->channel()]]);
        $settings['channels'] = [];
        $settings['enable_notifications'] = false;

        $this->putJson(cp_route('laborb.notifications.update'), $settings)->assertSuccessful();

        $this->assertSame([], Notifications::values()['channels']);
        $this->assertFalse(Notifications::values()['enable_notifications']);
        $this->getJson(cp_route('laborb.notifications.index'))->assertJsonPath('values.enable_notifications', false);
    }

    public function test_empty_settings_file_and_empty_event_configuration_are_supported(): void
    {
        $this->settings();
        file_put_contents(config('statamic.notifications.path'), '');
        config(['statamic.notifications.events' => [], 'statamic.notifications.channels' => []]);

        $this->assertSame([], Notifications::values());
        $this->assertSame([], Notifications::getClassNames());
        $this->assertSame([], Notifications::getServices());
        $this->get(cp_route('utilities.notifications-channel'))->assertOk();
    }

    public function test_guests_cannot_update_settings(): void
    {
        $settings = $this->settings();
        auth()->logout();

        $this->putJson(cp_route('laborb.notifications.update'), $settings)->assertUnauthorized();
    }
}
