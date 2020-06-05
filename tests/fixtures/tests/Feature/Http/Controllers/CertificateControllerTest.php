<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Certificate;

it('index behaves as expected', function () {
    $certificates = factory(Certificate::class, 3)->create();

    $response = $this->get(route('certificate.index'));
});

it('uses form request validation on store')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\CertificateController::class,
        'store',
        \App\Http\Requests\CertificateStoreRequest::class
    );

it('saves on store', function () {
    $certificate = $this->faker->word;

    $response = $this->post(route('certificate.store'), [
        'certificate' => $certificate,
    ]);

    $certificates = Certificate::query()
        ->where('certificate', $certificate)
        ->get();
    assertCount(1, $certificates);
    $certificate = $certificates->first();
});

it('show behaves as expected', function () {
    $certificate = factory(Certificate::class)->create();

    $response = $this->get(route('certificate.show', $certificate));
});

it('uses form request validation on update')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\CertificateController::class,
        'update',
        \App\Http\Requests\CertificateUpdateRequest::class
    );

it('update behaves as expected', function () {
    $certificate = factory(Certificate::class)->create();
    $certificate = $this->faker->word;

    $response = $this->put(route('certificate.update', $certificate), [
        'certificate' => $certificate,
    ]);
});

it('deletes and responds with on destroy', function () {
    $certificate = factory(Certificate::class)->create();

    $response = $this->delete(route('certificate.destroy', $certificate));

    $response->assertOk();

    $this->assertDeleted($certificate);
});
