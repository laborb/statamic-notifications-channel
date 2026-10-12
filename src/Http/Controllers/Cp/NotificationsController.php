<?php

namespace Laborb\StatamicNotificationsChannel\Http\Controllers\Cp;

use Illuminate\Http\Request;
use Laborb\StatamicNotificationsChannel\Blueprints\Notifications;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Facades\File;
use Statamic\Facades\YAML;
use Statamic\CP\PublishForm;

final class NotificationsController extends CpController
{
    public function index()
    {
        return PublishForm::make(Notifications::blueprint())
            ->title(__('statamic-notifications-channel::cp.general.headline'))
            ->values(Notifications::values())
            ->asConfig()
            ->submittingTo(cp_route('laborb.notifications.update'), 'PUT');
    }

    public function update(Request $request)
    {
        $values = PublishForm::make(Notifications::blueprint())->submit($request->all());

        File::put(config('statamic.notifications.path'), YAML::dump($values));
    }
}
