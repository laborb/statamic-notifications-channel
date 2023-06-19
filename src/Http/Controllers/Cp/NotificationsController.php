<?php

namespace Laborb\StatamicNotificationsChannel\Http\Controllers\Cp;

use Illuminate\Http\Request;
use Laborb\StatamicNotificationsChannel\Blueprints\Notifications;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Facades\File;
use Statamic\Facades\YAML;

final class NotificationsController extends CpController
{
    protected $path;

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        $blueprint = Notifications::blueprint();
        $data = Notifications::values();

        $fields = $blueprint->fields()->addValues($data)->preProcess();

        return view('notifications::cp.settings.index', [
            'blueprint' => $blueprint->toPublishArray(),
            'values'    => $fields->values(),
            'meta'      => $fields->meta(),
        ]);
    }

    public function update(Request $request)
    {
        $blueprint = Notifications::blueprint();

        $fields = $blueprint->fields()->addValues($request->all());

        $fields->validate();

        File::put(config('statamic.notifications.path'), YAML::dump($fields->process()->values()->all()));
    }
}