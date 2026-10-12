<?php

namespace Laborb\StatamicNotificationsChannel\Tests;

use Illuminate\Support\ServiceProvider;
use Laborb\StatamicNotificationsChannel\Blueprints\Notifications;
use Statamic\Facades\YAML;

class ServiceProviderTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $path = $app['config']->get('statamic.notifications.path');
        $app['files']->ensureDirectoryExists(dirname($path));
        $app['files']->put($path, YAML::dump(['enable_notifications' => false, 'channels' => []]));
    }

    public function test_existing_content_is_not_registered_for_forced_publishing(): void
    {
        $this->assertNotContains(config('statamic.notifications.path'), array_values(ServiceProvider::pathsToPublish(null, 'laborb-notifications-content')));
        $this->artisan('vendor:publish', ['--tag' => 'laborb-notifications-content', '--force' => true])->assertSuccessful();
        $this->assertSame(['enable_notifications' => false, 'channels' => []], Notifications::values());
    }
}
