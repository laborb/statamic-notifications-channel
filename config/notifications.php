<?php

return [
    'path' => base_path('content/addons/notifications.yaml'),

    /*
    |--------------------------------------------------------------------------
    | Add you own event handlers here to format the output of the notification
    | to your needs.
    |--------------------------------------------------------------------------
    |
    | The key is the event class and the value is the handler class.
    | See all Statamic events here: https://statamic.dev/extending/events
    */

    'events' => [
        Statamic\Events\AssetDeleted::class  => Laborb\StatamicNotificationsChannel\Handlers\AssetDeletedHandler::class,
        Statamic\Events\AssetUploaded::class => Laborb\StatamicNotificationsChannel\Handlers\AssetUploadedHandler::class,
        Statamic\Events\EntryCreated::class  => Laborb\StatamicNotificationsChannel\Handlers\EntryCreatedHandler::class,
        Statamic\Events\EntrySaved::class   => Laborb\StatamicNotificationsChannel\Handlers\EntrySavedHandler::class,
        Statamic\Events\EntryDeleted::class => Laborb\StatamicNotificationsChannel\Handlers\EntryDeletedHandler::class,
        Statamic\Events\FormSubmitted::class => Laborb\StatamicNotificationsChannel\Handlers\FormSubmittedHandler::class,
        Statamic\Events\UserRegistered::class => Laborb\StatamicNotificationsChannel\Handlers\UserRegisteredHandler::class,
        Statamic\Events\UserSaved::class => Laborb\StatamicNotificationsChannel\Handlers\UserSavedHandler::class,
        Statamic\Events\UserDeleted::class => Laborb\StatamicNotificationsChannel\Handlers\UserDeletedHandler::class,
    ],
    'channels' => [
        'mail' => [
            'label' => 'Email',
            'handle' => 'email_address',
            'handler' => MailHandler::class,
        ],
        'slack' => [
            'label' => 'Slack, Mattermost',
            'handle' => 'slack_url',
            'handler' => SlackHandler::class,
        ],
        'teams' => [
            'label' => 'Microsoft Teams',
            'handle' => 'teams_url',
            'handler' => TeamsHandler::class,
        ],
        'webhook' => [
            'label' => 'Webhook',
            'handle' => 'webhook_url',
            'handler' => WebhookHandler::class,
        ],
    ]
];