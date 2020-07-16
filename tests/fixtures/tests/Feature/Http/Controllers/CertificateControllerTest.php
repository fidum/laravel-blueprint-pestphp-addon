<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\CertificateController;
use App\Http\Requests\CertificateStoreRequest;
use App\Http\Requests\CertificateUpdateRequest;
use App\Models\Certificate;
use App\Models\CertificateType;

it('index behaves as expected', function () {
    $certificates = factory(Certificate::class, 3)->create();

    $response = $this->get(route('certificate.index'));

    $response->assertOK();
    $response->assertJsonStructure([]);
});

it('uses form request validation on store')
    ->assertActionUsesFormRequest(
        CertificateController::class,
        'store',
        CertificateStoreRequest::class
    );

it('saves on store', function () {
    $name = $this->faker->name;
    $certificate_type = factory(CertificateType::class)->create();
    $reference = $this->faker->word;
    $document = $this->faker->word;
    $expiry_date = $this->faker->date();

    $response = $this->post(route('certificate.store'), [
        'name' => $name,
        'certificate_type_id' => $certificate_type->id,
        'reference' => $reference,
        'document' => $document,
        'expiry_date' => $expiry_date,
    ]);

    $certificates = Certificate::query()
        ->where('name', $name)
        ->where('certificate_type_id', $certificate_type->id)
        ->where('reference', $reference)
        ->where('document', $document)
        ->where('expiry_date', $expiry_date)
        ->get();
    assertCount(1, $certificates);
    $certificate = $certificates->first();

    $response->assertCreated();
    $response->assertJsonStructure([]);
});

it('show behaves as expected', function () {
    $certificate = factory(Certificate::class)->create();

    $response = $this->get(route('certificate.show', $certificate));

    $response->assertOK();
    $response->assertJsonStructure([]);
});

it('uses form request validation on update')
    ->assertActionUsesFormRequest(
        CertificateController::class,
        'update',
        CertificateUpdateRequest::class
    );

it('update behaves as expected', function () {
    $certificate = factory(Certificate::class)->create();
    $name = $this->faker->name;
    $certificate_type = factory(CertificateType::class)->create();
    $reference = $this->faker->word;
    $document = $this->faker->word;
    $expiry_date = $this->faker->date();

    $response = $this->put(route('certificate.update', $certificate), [
        'name' => $name,
        'certificate_type_id' => $certificate_type->id,
        'reference' => $reference,
        'document' => $document,
        'expiry_date' => $expiry_date,
    ]);

    $certificate->refresh();

    $response->assertOK();
    $response->assertJsonStructure([]);

    assertSame($name, $certificate->name);
    assertSame($certificate_type->id, $certificate->certificate_type_id);
    assertSame($reference, $certificate->reference);
    assertSame($document, $certificate->document);
    assertSame($expiry_date, $certificate->expiry_date);
});

it('deletes and responds with on destroy', function () {
    $certificate = factory(Certificate::class)->create();

    $response = $this->delete(route('certificate.destroy', $certificate));

    $response->assertOk();

    $this->assertDeleted($certificate);
});
