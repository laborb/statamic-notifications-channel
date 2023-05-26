<?php

return [
    'general' => [
        'headline' => 'Notifications',
    ],
    'permissions' => [
        'settings'                  => 'Notification settings',
        'view_settings'             => 'View settings',
        'view_settings_description' => 'Permission to view notification settings.',
    ],
    'channels' => [
        'title' => 'Notification channels',
        'description' => 'Configure, add or delete notification channels.',
        'warning' => [
            'title' => 'Notice',
            'description' => 'Notifications are disabled in the settings tab. Notifications will not be sent.',
        ],
        'sets' => [
            'title' => 'Channel',
            'name' => 'Name',
            'events' => 'Events',
            'events_description' => 'Select Statamic events to be notified about. Get a full list of events in the <a href="https://statamic.dev/extending/events" target="_blank">Statamic documentation</a>.',
            'email_address' => 'Email address',
            'slack' => 'Slack / Mattermost Webhook URL',
            'slack_description' => 'Get a <a href="https://slack.com/intl/en-de/help/articles/115005265063-Incoming-Webhooks-for-Slack" target="_blank">Slack Webhook URL</a>.',
            'teams' => 'Microsoft Teams Webhook URL',
            'teams_description' => 'Get a <a href="https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook" target="_blank">Microsoft Teams Webhook URL</a>.',
            'webhook' => 'Webhook URL',
            'enabled' => 'Enabled',
        ]
    ],
    'settings' => [
        'title' => 'Settings',
        'description' => 'Configure notifications for Statamic events',
        'save' => 'Save settings',
        'saved' => 'Settings saved',
        'unable_to_save' => 'Unable to save settings. Please fix the errors and try again.',
        'enable_notifications' => 'Enable notifications',
        'enable_notifications_description' => 'Enable or disable notifications for Statamic events.',
        'log' => [
            'title' => 'Log notifications',
            'description' => 'Log notifications in a log file',
            'log_channel' => 'Log channel',
            'log_channel_description' => 'Define a <a href="https://laravel.com/docs/10.x/logging#configuring-the-channel" target="_blank">Laravel log channel</a> to log notifications.',
        ]
    ],

    'unable_to_save' => 'Unable to save settings. Please fix the errors and try again.',

];