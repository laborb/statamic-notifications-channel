<?php

return [
    'path' => base_path('content/addons/notifications.yaml'),
    'events' => [
        Statamic\Events\AssetDeleted::class  => Laborb\StatamicNotifications\Handlers\AssetDeletedHandler::class,
        Statamic\Events\AssetUploaded::class => Laborb\StatamicNotifications\Handlers\AssetUploadedHandler::class,
        Statamic\Events\EntryCreated::class  => Laborb\StatamicNotifications\Handlers\EntryCreatedHandler::class,
        Statamic\Events\EntrySaved::class   => Laborb\StatamicNotifications\Handlers\EntrySavedHandler::class,
        Statamic\Events\EntryDeleted::class => Laborb\StatamicNotifications\Handlers\EntryDeletedHandler::class,
        Statamic\Events\FormSubmitted::class => Laborb\StatamicNotifications\Handlers\FormSubmittedHandler::class,
        Statamic\Events\UserRegistered::class => Laborb\StatamicNotifications\Handlers\UserRegisteredHandler::class,
        Statamic\Events\UserSaved::class => Laborb\StatamicNotifications\Handlers\UserSavedHandler::class,
        Statamic\Events\UserDeleted::class => Laborb\StatamicNotifications\Handlers\UserDeletedHandler::class,
    ],
    'channels' => [
        'mail' => [
            'label' => 'Email',
            'handle' => 'email_address',
            'handler' => Laborb\StatamicNotifications\Notifications\Handlers\MailHandler::class,
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