<?php

namespace Tests\Feature\Http\Controllers;

it('uses form request validation on store')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\PostController::class,
        'store',
        \App\Http\Requests\PostStoreRequest::class
    );
