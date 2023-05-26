<?php

namespace Laborb\StatamicNotifications\Handlers;

class UserDeletedHandler {
    public function info($data) {
        return '**' . $data[0]->user->email . '**';
    }
}
