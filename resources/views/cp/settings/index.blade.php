@extends('statamic::layout')
@section('title', __('statamic-notifications-channel::cp.general.headline'))

@section('content')
    <publish-form
        title="@lang('statamic-notifications-channel::cp.general.headline')"
        action="{{ cp_route('laborb.notifications.update') }}"
        method="put"
        :blueprint='@json($blueprint)'
        :meta='@json($meta)'
        :values='@json($values)'
    ></publish-form>
@endsection