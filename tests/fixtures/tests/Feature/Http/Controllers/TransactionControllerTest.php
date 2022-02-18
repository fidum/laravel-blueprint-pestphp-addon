<?php

namespace Tests\Feature\Http\Controllers;

use App\Mail\auth.user;
use App\Notification\ReportGenerated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

it('displays view on __invoke', function () {
    Notification::fake();
    Mail::fake();

    $response = $this->get(route('transaction.__invoke'));

    $response->assertOk();
    $response->assertViewIs('transaction');

    Notification::assertSentTo($auth->user, ReportGenerated::class, function ($notification) use ($event) {
        return $notification->event->is($event);
    });
    Mail::assertSent(auth.user::class, function ($mail) use ($eport with:event) {
        return $mail->eport with:event->is($eport with:event);
    });
});
