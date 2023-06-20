<?php

return [
    'general' => [
        'headline' => 'Notifications Channel',
        'description' => 'Benachrichtigungen für Statamic-Ereignisse konfigurieren',
    ],
    'permissions' => [
        'settings' => 'Benachrichtigungseinstellungen',
        'view_settings' => 'Einstellungen anzeigen',
        'view_settings_description' => 'Berechtigung zum Anzeigen der Benachrichtigungseinstellungen.',
    ],
    'channels' => [
        'title' => 'Benachrichtigungskanäle',
        'description' => 'Benachrichtigungskanäle konfigurieren, hinzufügen oder löschen.',
        'warning' => [
            'title' => 'Hinweis',
            'description' => 'Benachrichtigungen sind in den Einstellungen deaktiviert. Benachrichtigungen werden nicht gesendet.',
        ],
        'sets' => [
            'title' => 'Kanal',
            'name' => 'Name',
            'events' => 'Ereignisse',
            'events_description' => 'Statamic-Events auswählen, über die benachrichtigt werden soll. Eine vollständige Liste der Ereignisse finden Sie in der <a href="https://statamic.dev/extending/events" target="_blank">Statamic-Dokumentation</a>.',
            'email_address' => 'E-Mail-Adresse',
            'slack' => 'Slack / Mattermost Webhook-URL',
            'slack_description' => 'Eine <a href="https://slack.com/intl/de-de/help/articles/115005265063-Incoming-Webhooks-for-Slack" target="_blank">Slack-Webhook-URL</a> abrufen.',
            'teams' => 'Microsoft Teams Webhook-URL',
            'teams_description' => 'Eine <a href="https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook" target="_blank">Microsoft Teams Webhook-URL</a> abrufen.',
            'webhook' => 'Webhook-URL',
            'enabled' => 'Aktiviert',
        ],
    ],
    'settings' => [
        'title' => 'Einstellungen',
        'description' => 'Benachrichtigungen für Statamic-Ereignisse konfigurieren',
        'save' => 'Einstellungen speichern',
        'saved' => 'Einstellungen gespeichert',
        'unable_to_save' => 'Einstellungen können nicht gespeichert werden. Bitte beheben Sie die Fehler und versuchen Sie es erneut.',
        'enable_notifications' => 'Benachrichtigungen aktivieren',
        'enable_notifications_description' => 'Benachrichtigungen für Statamic-Ereignisse aktivieren oder deaktivieren.',
        'log' => [
            'title' => 'Benachrichtigungen protokollieren',
            'description' => 'Benachrichtigungen in einer Protokolldatei protokollieren',
            'log_channel' => 'Protokollkanal',
            'log_channel_description' => 'Definieren Sie einen <a href="https://laravel.com/docs/10.x/logging#configuring-the-channel" target="_blank">Laravel-Protokollkanal</a>, um Benachrichtigungen zu protokollieren.',
        ],
    ],

    'unable_to_save' => 'Einstellungen können nicht gespeichert werden. Bitte beheben Sie die Fehler und versuchen Sie es erneut.',
];
