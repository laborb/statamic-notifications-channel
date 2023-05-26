<?php

namespace Laborb\StatamicNotifications\Handlers;

class UserRegisteredHandler {
    public function info($data) {
        return '**' . $data[0]->user->email . '**';
    }
}
