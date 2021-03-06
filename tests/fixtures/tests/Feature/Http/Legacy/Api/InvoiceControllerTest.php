<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\InvoiceController;
use App\Http\Requests\Api\InvoiceStoreRequest;
use App\Invoice;

it('responds with on index', function () {
    $invoices = factory(Invoice::class, 3)->create();

    $response = $this->get(route('invoice.index'));

    $response->assertOk();
    $response->assertJson($invoices);
});

it('uses form request validation on store')
    ->assertActionUsesFormRequest(
        InvoiceController::class,
        'store',
        InvoiceStoreRequest::class
    );

it('responds with on store', function () {
    $total = $this->faker->numberBetween(-10000, 10000);

    $response = $this->post(route('invoice.store'), [
        'total' => $total,
    ]);

    $response->assertNoContent();
});

it('responds with on error', function () {
    $response = $this->get(route('invoice.error'));

    $response->assertNoContent(400);
});
