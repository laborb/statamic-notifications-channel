# Notifications Channel

This Statamic Addon makes it easy to send notifications to popular chat systems like Slack, Teams or Mattermost. But also email or webhook is possible.
Typical use cases are sending form submissions to Teams chat or informing a Slack group about a certain alert.
In contrast to unencrypted emails, Notifications Channel uses https and is therefore especially suitable for sensitive data such as recruitment forms.

## Features

Supported notification channels:

- Email
- Slack
- Mattermost
- Microsoft Teams
- Webhook
- more to follow

Supported [Statamic Events](https://statamic.dev/extending/events)

- Statamic\Events\AssetDeleted
- Statamic\Events\AssetUploaded
- Statamic\Events\EntryCreated
- Statamic\Events\EntrySaved
- Statamic\Events\EntryDeleted
- Statamic\Events\FormSubmitted
- Statamic\Events\UserRegistered
- Statamic\Events\UserSaved
- Statamic\Events\UserDeleted

Other events can easily be added with a handler class.

## How to Install

Version compatibility:

1. **Addon 2.x:** Statamic 6, Laravel 12 and PHP 8.3 or newer.
2. **Addon 1.x:** the existing release line for Statamic 5 projects. Keep the Composer requirement at `^1.1` in these projects.

Install the Statamic 6 version from your project root:

``` bash
composer require laborb/statamic-notifications-channel:^2.0
```

The 2.x release must be published before this command can resolve it. This repository prepares that release; it does not publish it automatically.

For the Statamic boilerplate, change `laborb/statamic-notifications-channel` from `^1.1` to `^2.0` after publication and run:

```bash
composer update laborb/statamic-notifications-channel --with-dependencies
php artisan statamic:install
```

Use `ddev composer` and `ddev php` for these commands in a DDEV project. Existing `content/addons/notifications.yaml` settings and published configuration can be reused without a data migration.

## How to Use

You can access the addon settings in the control panel: `Utilities > Notifications`.

You can enable/disable all notifications and enable/disable the built in logging function in the `Settings` tab.

In the `Notifications channels` tab you can add notifications channels with the according settings on email address or webhook URLs.

All settings are displayed as normal Statamic blueprints would be. 

## How to extend

You can add new Statamic events by adding an EventHandler class in the config file. You can then format the output in your own class. 

## Development and tests

Install dependencies and run the test suite:

```bash
composer install
composer test
```

Tests use PHPUnit and Statamic's `AddonTestCase` with Orchestra Testbench. Every test uses an isolated temporary directory for settings and content. Mail and queued jobs are faked, and HTTP transports use Guzzle mocks; tests do not send notifications to external services.

The suite covers the native Statamic 6 publish form, settings persistence and validation, existing YAML compatibility, all nine event handlers, channel selection, notification payloads, and logging. The lockfile is resolved for PHP 8.3 so the same dependencies install on every supported PHP version.

For a browser smoke test in a test project:

1. Open `Utilities > Notifications` and check the `Notification channels` and `Settings` tabs.
2. Add a channel, select an event, enter its recipient and save. Reload and verify that it persists.
3. Switch between Email, Slack/Mattermost, Teams and Webhook and verify the matching recipient field. Edit and delete a channel, save and reload.
4. Enter an invalid email address or webhook URL and verify that saving shows an error and preserves the previous settings.
5. Disable notifications in `Settings` and verify the warning in `Notification channels`. Enable logging and verify the log-channel selector.

# Support

If you encounter any issues please open an issue in the Github repo.

You can also contact us via email: support@laborb.de
