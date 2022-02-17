<?php

namespace Tests\Feature\Http\Controllers;

use App\Jobs\GenerateReport;
use Illuminate\Support\Facades\Queue;

it('displays view on __invoke', function () {
    Queue::fake();

    $response = $this->get(route('report.__invoke'));

    $response->assertOk();
    $response->assertViewIs('report');

    Queue::assertPushed(GenerateReport::class, function ($job) use ($event) {
        return $job->event->is($event);
    });
});
