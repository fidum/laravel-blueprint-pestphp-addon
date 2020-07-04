<?php

namespace Tests\Feature\Http\Controllers;

use App\Author;
use App\Http\Controllers\AuthorController;
use App\Http\Requests\AuthorStoreRequest;
use App\Http\Requests\AuthorUpdateRequest;

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
        AuthorController::class,
        'store',
        AuthorStoreRequest::class
    );

it('saves and redirects on store', function () {
    $name = $this->faker->name;
    $email = $this->faker->safeEmail;

    $response = $this->post(route('author.store'), [
        'name' => $name,
        'email' => $email,
    ]);

    $authors = Author::query()
        ->where('name', $name)
        ->where('email', $email)
        ->get();
    assertCount(1, $authors);
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
        AuthorController::class,
        'update',
        AuthorUpdateRequest::class
    );

it('redirects on update', function () {
    $author = factory(Author::class)->create();
    $name = $this->faker->name;
    $email = $this->faker->safeEmail;

    $response = $this->put(route('author.update', $author), [
        'name' => $name,
        'email' => $email,
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
