<?php

namespace Tests\Feature\Http\Controllers;

use App\Entry;

it('displays view on show', function () {
    $entry = Entry::factory()->create();

    $response = $this->get(route('entry.show', $entry));

    $response->assertOk();
    $response->assertViewIs('entry.show');
    $response->assertViewHas('entry');
});
