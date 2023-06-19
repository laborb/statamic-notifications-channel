<?php

namespace Laborb\StatamicNotificationsChannel\Handlers;

class UserSavedHandler {
    public function info($data) {
        return '**' . $data[0]->user->email . '**';
    }
}
