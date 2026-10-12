<?php

namespace Laborb\StatamicNotificationsChannel\Tests;

use Illuminate\Support\Facades\Notification;
use Laborb\StatamicNotificationsChannel\Notifications\StatamicNotification;
use Laborb\StatamicNotificationsChannel\Listeners\NotificationsListener;
use PHPUnit\Framework\Attributes\DataProvider;
use Statamic\Facades\Asset;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Entry;
use Statamic\Facades\Collection;
use Statamic\Facades\Form;
use Statamic\Facades\User;
use Statamic\Forms\Submission;

class EventHandlersTest extends TestCase
{
    #[DataProvider('events')]
    public function test_real_statamic_events_produce_the_existing_message_format(string $eventClass, string $label, string $expected): void
    {
        Notification::fake();
        $this->settings(['channels' => [$this->channel('mail', ['events' => [$eventClass]])]]);

        $arguments = match (true) {
            str_contains($eventClass, 'Asset') => [Asset::make()->container(AssetContainer::make()->handle('images'))->path('photo.jpg')],
            str_contains($eventClass, 'Entry') => [Entry::make()->id('entry-id')->slug('hello-world')->collection(Collection::make()->handle('news'))],
            str_contains($eventClass, 'Form') => [(new Submission())->form(Form::make()->handle('contact')->title('Contact'))],
            default => [User::make()->id('member')->email('member@example.test')],
        };
        if ($eventClass === \Statamic\Events\AssetUploaded::class) {
            $arguments[] = 'photo.jpg';
        }
        (new NotificationsListener())->handle($eventClass, [new $eventClass(...$arguments)]);

        Notification::assertSentOnDemand(StatamicNotification::class, fn ($notification) => $notification->event === [
            'event' => $label,
            'info' => $expected,
        ]);
        Notification::assertCount(1);
    }

    public static function events(): array
    {
        return [
            'asset deleted' => [\Statamic\Events\AssetDeleted::class, 'Asset Deleted', '**photo.jpg** to container **images**'],
            'asset uploaded' => [\Statamic\Events\AssetUploaded::class, 'Asset Uploaded', '**photo.jpg** to container **images**'],
            'entry created' => [\Statamic\Events\EntryCreated::class, 'Entry Created', '**hello-world** (entry-id) in collection **news**'],
            'entry saved' => [\Statamic\Events\EntrySaved::class, 'Entry Saved', '**hello-world** (entry-id) in collection **news**'],
            'entry deleted' => [\Statamic\Events\EntryDeleted::class, 'Entry Deleted', '**hello-world** (entry-id)'],
            'form submitted' => [\Statamic\Events\FormSubmitted::class, 'Form Submitted', '**Contact**'],
            'user registered' => [\Statamic\Events\UserRegistered::class, 'User Registered', '**member@example.test**'],
            'user saved' => [\Statamic\Events\UserSaved::class, 'User Saved', '**member@example.test**'],
            'user deleted' => [\Statamic\Events\UserDeleted::class, 'User Deleted', '**member@example.test**'],
        ];
    }
}
