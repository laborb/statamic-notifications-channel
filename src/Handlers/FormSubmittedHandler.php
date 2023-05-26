<?php

namespace Laborb\StatamicNotifications\Handlers;

class FormSubmittedHandler {
    public function info($data) {
        return '**' . $data[0]->submission->form->title . '**';
    }
}
