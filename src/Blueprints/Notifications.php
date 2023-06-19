<?php

namespace Laborb\StatamicNotifications\Blueprints;

use Statamic\Facades\Blueprint;
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
        return collect(YAML::file(config('statamic.notifications.path'))->parse())
            ->merge(YAML::file(config('statamic.notifications.path'))->parse())
            ->all();
    }

    public static function getClassNames()
    {
        $configArray = array_keys(config('statamic.notifications.events'));

        foreach ($configArray as $class) {
            $className = class_basename(implode(' ', preg_split('/(?=[A-Z])/', $class)));
            $classNames[$class] = $className;
        }

        return $classNames;
    }

    public static function getServices()
    {
        $configArray = config('statamic.notifications.channels');

        foreach ($configArray as $key => $service) {
            $services[$key] = $service['label'];
        }

        return $services;
    }

    public static function blueprint()
    {
        return Blueprint::make()->setContents([
            'sections' => [
                'channels' => [
                    'display' => __('statamic-notifications-channel::cp.channels.title'),
                    'fields' => [
                        [
                            'handle' => 'introduction',
                            'field' => [
                                'default' => true,
                                'display' => __('statamic-notifications-channel::cp.channels.title'),
                                'instructions' => __('statamic-notifications-channel::cp.channels.description'),
                                'type' => 'section',
                                'icon' => 'section',
                                'visibility' => 'visible',
                            ],
                        ],
                        [
                            'handle' => 'warning',
                            'field' => [
                                'html' => '<span class="flex items-center gap-1 text-red-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">'
                                    . '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />'
                                    . '</svg>'
                                    . __('statamic-notifications-channel::cp.channels.warning.description') . '</span>',
                                'display' => __('statamic-notifications-channel::cp.channels.warning.title'),
                                'type' => 'html',
                                'icon' => 'html',
                                'if' => [
                                    'enable_notifications' => 'equals false',
                                ],
                            ],
                        ],
                        [
                            'handle' => 'channels',
                            'field' => [
                                'collapse' => true,
                                'sets' => [
                                    'channel' => [
                                        'display' => __('statamic-notifications-channel::cp.channels.sets.title'),
                                        'fields' => [
                                            [
                                                'handle' => 'channel_name',
                                                'field' => [
                                                    'type' => 'text',
                                                    'required' => false,
                                                    'display' => __('statamic-notifications-channel::cp.channels.sets.name'),
                                                    'width' => 100,
                                                ],
                                            ],
                                            [
                                                'handle' => 'service',
                                                'field' => [
                                                    'options' => static::getServices(),
                                                    'required' => true,
                                                    'display' => __('statamic-notifications-channel::cp.channels.sets.title'),
                                                    'type' => 'button_group',
                                                    'icon' => 'button_group',
                                                    'listable' => 'hidden',
                                                    'default' => 'mail'
                                                ]
                                            ],
                                            [
                                                'handle' => 'events',
                                                'field' => [
                                                    'options' => static::getClassNames(),
                                                    'required' => true,
                                                    'multiple' => true,
                                                    'clearable' => true,
                                                    'searchable' => true,
                                                    'taggable' => false,
                                                    'push_tags' => false,
                                                    'cast_booleans' => false,
                                                    'display' => __('statamic-notifications-channel::cp.channels.sets.events'),
                                                    'instructions' => __('statamic-notifications-channel::cp.channels.sets.events_description'),
                                                    'type' => 'select',
                                                    'icon' => 'select',
                                                    'listable' => 'hidden',
                                                ]
                                            ],
                                            [
                                                'handle' => 'email_address',
                                                'field' => [
                                                    'input_type' => 'text',
                                                    'antlers' => false,
                                                    'display' => __('statamic-notifications-channel::cp.channels.sets.email_address'),
                                                    'type' => 'text',
                                                    'icon' => 'text',
                                                    'if' => [
                                                        'service' => 'equals mail'
                                                    ],
                                                    'validate' => [
                                                        ['email']
                                                    ]
                                                ]
                                            ],
                                            [
                                                'handle' => 'slack_url',
                                                'field' => [
                                                    'required' => false,
                                                    'input_type' => 'text',
                                                    'antlers' => false,
                                                    'display' => __('statamic-notifications-channel::cp.channels.sets.slack'),
                                                    'instructions' => __('statamic-notifications-channel::cp.channels.sets.slack_description'),
                                                    'type' => 'text',
                                                    'icon' => 'text',
                                                    'if' => [
                                                        'service' => 'equals slack'
                                                    ],
                                                    'validate' => [
                                                        ['url']
                                                    ]
                                                ]
                                            ],
                                            [
                                                'handle' => 'teams_url',
                                                'field' => [
                                                    'required' => false,
                                                    'input_type' => 'text',
                                                    'antlers' => false,
                                                    'display' => __('statamic-notifications-channel::cp.channels.sets.teams'),
                                                    'instructions' => __('statamic-notifications-channel::cp.channels.sets.teams_description'),
                                                    'type' => 'text',
                                                    'icon' => 'text',
                                                    'if' => [
                                                        'service' => 'equals teams'
                                                    ],
                                                    'validate' => [
                                                        ['url']
                                                    ]
                                                ]
                                            ],
                                            [
                                                'handle' => 'webhook_url',
                                                'field' => [
                                                    'required' => false,
                                                    'input_type' => 'text',
                                                    'antlers' => false,
                                                    'display' => __('statamic-notifications-channel::cp.channels.sets.webhook'),
                                                    'type' => 'text',
                                                    'icon' => 'text',
                                                    'if' => [
                                                        'service' => 'equals webhook'
                                                    ],
                                                    'validate' => [
                                                        ['url']
                                                    ]
                                                ]
                                            ],
                                            [
                                                'handle' => 'enabled',
                                                'field' => [
                                                    'default' => true,
                                                    'display' => __('statamic-notifications-channel::cp.channels.sets.enabled'),
                                                    'type' => 'toggle',
                                                    'icon' => 'toggle',
                                                    'visibility' => 'hidden',
                                                ],
                                            ],
                                        ]
                                    ]
                                ],
                                'display' => __('statamic-notifications-channel::cp.channels.title'),
                                'type' => 'replicator',
                                'icon' => 'replicator',
                                'listable' => 'hidden',
                                'instructions_position' => 'above',
                            ],
                        ]
                    ],
                ],
                'settings' => [
                    'display' => __('statamic-notifications-channel::cp.settings.title'),
                    'fields' => [
                        [
                            'handle' => 'settings_introduction',
                            'field' => [
                                'default' => true,
                                'display' => __('statamic-notifications-channel::cp.settings.title'),
                                'instructions' => __('statamic-notifications-channel::cp.settings.description'),
                                'type' => 'section',
                                'icon' => 'section',
                                'visibility' => 'visible',
                            ],
                        ],
                        [
                            'handle' => 'enable_notifications',
                            'field' => [
                                'default' => true,
                                'display' => __('statamic-notifications-channel::cp.settings.enable_notifications'),
                                'instructions' => __('statamic-notifications-channel::cp.settings.enable_notifications_description'),
                                'type' => 'toggle',
                                'icon' => 'toggle',
                            ],
                        ],
                        [
                            'handle' => 'log_events',
                            'field' => [
                                'default' => false,
                                'display' => __('statamic-notifications-channel::cp.settings.log.title'),
                                'instructions' => __('statamic-notifications-channel::cp.settings.log.description'),
                                'type' => 'toggle',
                                'icon' => 'toggle',
                                'width' => 33,
                                'if' => [
                                    'enable_notifications' => 'equals true'
                                ],
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
                                'icon' => 'select',
                                'multiple' => false,
                                'clearable' => false,
                                'searchable' => false,
                                'taggable' => false,
                                'push_tags' => false,
                                'cast_booleans' => false,
                                'width' => 66,
                                'if' => [
                                    'log_events' => 'equals true',
                                    'enable_notifications' => 'equals true'
                                ],
                            ],
                        ],
                    ]
                ]
            ],
        ]);
    }
}