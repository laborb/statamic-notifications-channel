<?php

namespace Laborb\StatamicNotifications\Handlers;

class UserSavedHandler {
    public function info($data) {
        return '**' . $data[0]->user->email . '**';
    }
}
