<?php

namespace Laborb\StatamicNotifications\Handlers;

class EntrySavedHandler {
    public function info($data) {
        return '**' . $data[0]->entry->slug . '** (' . $data[0]->entry->id . ')' . ' in collection **' . $data[0]->entry->collection . '**';
    }
}
