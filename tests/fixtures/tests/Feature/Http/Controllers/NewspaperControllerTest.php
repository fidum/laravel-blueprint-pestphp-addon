<?php

namespace Tests\Feature\Http\Controllers;

use App\Newspaper;

it('displays view on index', function () {
    $newspapers = factory(Newspaper::class, 3)->create();

    $response = $this->get(route('newspaper.index'));

    $response->assertOk();
    $response->assertViewIs('newspaper.index');
    $response->assertViewHas('newspapers');
});

it('displays view on edit', function () {
    $newspaper = factory(Newspaper::class)->create();

    $response = $this->get(route('newspaper.edit', $newspaper));

    $response->assertOk();
    $response->assertViewIs('newspaper.edit');
    $response->assertViewHas('newspaper');
});

it('saves and redirects on update', function () {
    $newspaper = factory(Newspaper::class)->create();
    $newspapers = factory(Newspaper::class, 3)->create();

    $response = $this->put(route('newspaper.update', $newspaper));

    $response->assertRedirect(route('newspaper.edit', ['newspaper' => $newspaper]));

    $this->assertDatabaseHas('newspapers', [ /* ... */ ]);
});
