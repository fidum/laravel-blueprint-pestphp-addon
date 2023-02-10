<?php

namespace Tests\Feature\Http\Controllers;

use App\Newspaper;

it('displays view on index', function () {
    $newspapers = Newspaper::factory()->times(3)->create();

    $response = $this->get(route('newspaper.index'));

    $response->assertOk();
    $response->assertViewIs('newspaper.index');
    $response->assertViewHas('newspapers');
});

it('displays view on edit', function () {
    $newspaper = Newspaper::factory()->create();

    $response = $this->get(route('newspaper.edit', $newspaper));

    $response->assertOk();
    $response->assertViewIs('newspaper.edit');
    $response->assertViewHas('newspaper');
});

it('saves and redirects on update', function () {
    $newspaper = Newspaper::factory()->create();
    $newspapers = Newspaper::factory()->times(3)->create();

    $response = $this->put(route('newspaper.update', $newspaper));

    $response->assertRedirect(route('newspaper.edit', ['newspaper' => $newspaper]));

    $this->assertDatabaseHas('newspapers', [/* ... */]);
});
