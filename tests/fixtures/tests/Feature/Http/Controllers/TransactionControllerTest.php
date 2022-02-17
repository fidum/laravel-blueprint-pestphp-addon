<?php

namespace Tests\Feature\Http\Controllers;

use App\Notification\ReportGenerated;
use Illuminate\Support\Facades\Notification;

it('displays view on __invoke', function () {
    Notification::fake();

    $response = $this->get(route('transaction.__invoke'));

    $response->assertOk();
    $response->assertViewIs('transaction');

    Notification::assertSentTo($auth->user, ReportGenerated::class, function ($notification) use ($transaction) {
        return $notification->transaction->is($transaction);
    });
});
