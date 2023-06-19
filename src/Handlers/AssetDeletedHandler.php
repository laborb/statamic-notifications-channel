<?php

namespace Laborb\StatamicNotificationsChannel\Handlers;

class AssetDeletedHandler {
    public function info($data) {
        return '**' . $data[0]->asset->path . '** to container **' . $data[0]->asset->container->handle . '**';
    }
}
