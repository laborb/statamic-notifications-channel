<?php

namespace Laborb\StatamicNotificationsChannel\Handlers;

class EntryDeletedHandler {
    public function info($data) {
        return '**' . $data[0]->entry->slug . '** (' . $data[0]->entry->id . ')';
    }
}
