<?php

namespace Laborb\StatamicNotificationsChannel\Blueprints;

use Statamic\Facades\Blueprint;
use Statamic\Facades\File;
use Statamic\Facades\YAML;

class Notifications
{
    public static function augmentedValues()
    {
        return static::blueprint()
            ->fields()
            ->addValues(static::values())
            ->augment()
            ->values();
    }

    public static function values()
    {
        $path = config('statamic.notifications.path');

        return File::exists($path) ? (YAML::file($path)->parse() ?? []) : [];
    }

    public static function getClassNames()
    {
        $classNames = [];

        foreach (array_keys(config('statamic.notifications.events', [])) as $class) {
            $classNames[$class] = trim(implode(' ', preg_split('/(?=[A-Z])/', class_basename($class))));
        }

        return $classNames;
    }

    public static function getServices()
    {
        return collect(config('statamic.notifications.channels', []))
            ->mapWithKeys(fn ($channel, $handle) => [$handle => $channel['label']])
            ->all();
    }

    public static function blueprint()
    {
        return Blueprint::make()->setContents([
            'tabs' => [
                'channels' => [
                    'display' => __('statamic-notifications-channel::cp.channels.title'),
                    'sections' => [
                        [
                            'display' => __('statamic-notifications-channel::cp.channels.title'),
                            'instructions' => __('statamic-notifications-channel::cp.channels.description'),
                            'fields' => [
                                [
                                    'handle' => 'warning',
                                    'field' => [
                                        'html' => '<span class="text-red-500">'
                                            . __('statamic-notifications-channel::cp.channels.warning.description') . '</span>',
                                        'display' => __('statamic-notifications-channel::cp.channels.warning.title'),
                                        'type' => 'html',
                                        'if' => ['enable_notifications' => 'equals false'],
                                    ],
                                ],
                                [
                                    'handle' => 'channels',
                                    'field' => [
                                        'collapse' => true,
                                        'sets' => [
                                            'channels' => [
                                                'display' => __('statamic-notifications-channel::cp.channels.title'),
                                                'sets' => [
                                                    'channel' => [
                                                        'display' => __('statamic-notifications-channel::cp.channels.sets.title'),
                                                        'fields' => static::channelFields(),
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'display' => __('statamic-notifications-channel::cp.channels.title'),
                                        'type' => 'replicator',
                                        'listable' => 'hidden',
                                        'instructions_position' => 'above',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'settings' => [
                    'display' => __('statamic-notifications-channel::cp.settings.title'),
                    'sections' => [
                        [
                            'display' => __('statamic-notifications-channel::cp.settings.title'),
                            'instructions' => __('statamic-notifications-channel::cp.settings.description'),
                            'fields' => [
                                [
                                    'handle' => 'enable_notifications',
                                    'field' => [
                                        'default' => true,
                                        'display' => __('statamic-notifications-channel::cp.settings.enable_notifications'),
                                        'instructions' => __('statamic-notifications-channel::cp.settings.enable_notifications_description'),
                                        'type' => 'toggle',
                                    ],
                                ],
                                [
                                    'handle' => 'log_events',
                                    'field' => [
                                        'default' => false,
                                        'display' => __('statamic-notifications-channel::cp.settings.log.title'),
                                        'instructions' => __('statamic-notifications-channel::cp.settings.log.description'),
                                        'type' => 'toggle',
                                        'width' => 33,
                                        'if' => ['enable_notifications' => 'equals true'],
                                    ],
                                ],
                                [
                                    'handle' => 'log_channel',
                                    'field' => [
                                        'default' => 'single',
                                        'options' => array_keys(config('logging.channels')),
                                        'display' => __('statamic-notifications-channel::cp.settings.log.log_channel'),
                                        'instructions' => __('statamic-notifications-channel::cp.settings.log.log_channel_description'),
                                        'type' => 'select',
                                        'multiple' => false,
                                        'clearable' => false,
                                        'searchable' => false,
                                        'width' => 66,
                                        'if' => [
                                            'log_events' => 'equals true',
                                            'enable_notifications' => 'equals true',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private static function channelFields(): array
    {
        return [
            [
                'handle' => 'channel_name',
                'field' => [
                    'type' => 'text',
                    'display' => __('statamic-notifications-channel::cp.channels.sets.name'),
                ],
            ],
            [
                'handle' => 'service',
                'field' => [
                    'options' => static::getServices(),
                    'required' => true,
                    'display' => __('statamic-notifications-channel::cp.channels.sets.title'),
                    'type' => 'button_group',
                    'default' => 'mail',
                ],
            ],
            [
                'handle' => 'events',
                'field' => [
                    'options' => static::getClassNames(),
                    'required' => true,
                    'multiple' => true,
                    'clearable' => true,
                    'searchable' => true,
                    'display' => __('statamic-notifications-channel::cp.channels.sets.events'),
                    'instructions' => __('statamic-notifications-channel::cp.channels.sets.events_description'),
                    'type' => 'select',
                    'listable' => 'hidden',
                ],
            ],
            [
                'handle' => 'email_address',
                'field' => [
                    'display' => __('statamic-notifications-channel::cp.channels.sets.email_address'),
                    'type' => 'text',
                    'if' => ['service' => 'equals mail'],
                    'validate' => ['nullable', 'email'],
                ],
            ],
            ...array_map(fn ($service) => [
                'handle' => $service.'_url',
                'field' => [
                    'display' => __('statamic-notifications-channel::cp.channels.sets.'.$service),
                    'instructions' => $service === 'webhook' ? null : __('statamic-notifications-channel::cp.channels.sets.'.$service.'_description'),
                    'type' => 'text',
                    'if' => ['service' => 'equals '.$service],
                    'validate' => ['nullable', 'url'],
                ],
            ], ['slack', 'teams', 'webhook']),
            [
                'handle' => 'enabled',
                'field' => [
                    'default' => true,
                    'display' => __('statamic-notifications-channel::cp.channels.sets.enabled'),
                    'type' => 'toggle',
                    'visibility' => 'hidden',
                ],
            ],
        ];
    }
}
