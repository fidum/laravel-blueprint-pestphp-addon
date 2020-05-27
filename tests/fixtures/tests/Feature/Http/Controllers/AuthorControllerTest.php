<?php

namespace Tests\Feature\Http\Controllers;

use App\Author;

it('displays view on index', function () {
    $authors = factory(Author::class, 3)->create();

    $response = $this->get(route('author.index'));

    $response->assertOk();
    $response->assertViewIs('author.index');
    $response->assertViewHas('authors');
});

it('displays view on create', function () {
    $response = $this->get(route('author.create'));

    $response->assertOk();
    $response->assertViewIs('author.create');
});

it('uses form request validation on store')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\AuthorController::class,
        'store',
        \App\Http\Requests\AuthorStoreRequest::class
    );

it('saves and redirects on store', function () {
    $author = $this->faker->word;

    $response = $this->post(route('author.store'), [
        'author' => $author,
    ]);

    $authors = Author::query()
        ->where('author', $author)
        ->get();
    $this->assertCount(1, $authors);
    $author = $authors->first();

    $response->assertRedirect(route('author.index'));
    $response->assertSessionHas('author.id', $author->id);
});

it('displays view on show', function () {
    $author = factory(Author::class)->create();

    $response = $this->get(route('author.show', $author));

    $response->assertOk();
    $response->assertViewIs('author.show');
    $response->assertViewHas('author');
});

it('displays view on edit', function () {
    $author = factory(Author::class)->create();

    $response = $this->get(route('author.edit', $author));

    $response->assertOk();
    $response->assertViewIs('author.edit');
    $response->assertViewHas('author');
});

it('uses form request validation on update')
    ->assertActionUsesFormRequest(
        \App\Http\Controllers\AuthorController::class,
        'update',
        \App\Http\Requests\AuthorUpdateRequest::class
    );

it('redirects on update', function () {
    $author = factory(Author::class)->create();
    $author = $this->faker->word;

    $response = $this->put(route('author.update', $author), [
        'author' => $author,
    ]);

    $response->assertRedirect(route('author.index'));
    $response->assertSessionHas('author.id', $author->id);
});

it('deletes and redirects on destroy', function () {
    $author = factory(Author::class)->create();

    $response = $this->delete(route('author.destroy', $author));

    $response->assertRedirect(route('author.index'));

    $this->assertDeleted($author);
});
