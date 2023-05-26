# Statamic Notifications

Statamic Notifications is a Statamic addon to get notified on different Statamic events.

## Features

Supported notification channels:

- Email
- Slack
- Mattermost
- Microsoft Teams
- Webhook

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

You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require laborb/statamic-notifications
```

## How to Use

You can access the addon settings in the control panel: `Utilities > Notifications`.

You can enable/disable all notifications and enable/disable the built in logging function in the `Settings` tab.

In the `Notification channels` tab you can add notification channels with the according settings on email address or webhook URLs.

All settings are displayed as normal Statamic blueprints would be. 

## How to extend

You can add new Statamic events by adding an EventHandler class in the config file. You can then format the output in your own class. 

# Support

If you encounter any issues please open an issue in the Github repo.

You can also contact us via email: support@laborb.de