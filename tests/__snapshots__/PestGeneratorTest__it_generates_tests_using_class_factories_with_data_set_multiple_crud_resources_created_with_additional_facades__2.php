<?php

namespace Tests\Feature\Http\Controllers;

use App\Book;
use App\Events\DeletedBook;
use App\Events\NewBook;
use App\Events\UpdatedBook;
use App\Http\Controllers\BookController;
use App\Http\Requests\BookStoreRequest;
use App\Http\Requests\BookUpdateRequest;
use App\Jobs\SyncMedia;
use App\Notification\ReviewNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

it('displays view on index', function () {
    $books = Book::factory()->times(3)->create();

    $response = $this->get(route('book.index'));

    $response->assertOk();
    $response->assertViewIs('book.index');
    $response->assertViewHas('books');
});

it('uses form request validation on store')
    ->assertActionUsesFormRequest(
        BookController::class,
        'store',
        BookStoreRequest::class
    );

it('saves and redirects on store', function () {
    $title = $this->faker->sentence(4);
    $email = $this->faker->safeEmail;
    $content = $this->faker->paragraphs(3, true);

    Notification::fake();
    Queue::fake();
    Event::fake();

    $response = $this->post(route('book.store'), [
        'title' => $title,
        'email' => $email,
        'content' => $content,
    ]);

    $books = Book::query()
        ->where('title', $title)
        ->where('email', $email)
        ->where('content', $content)
        ->get();
    expect($books)->toHaveCount(1);
    $book = $books->first();

    $response->assertRedirect(route('book.index'));
    $response->assertSessionHas('book.title', $book->title);

    Notification::assertSentTo($book->author, ReviewNotification::class, function ($notification) use ($book) {
        return $notification->book->is($book);
    });
    Queue::assertPushed(SyncMedia::class, function ($job) use ($book) {
        return $job->book->is($book);
    });
    Event::assertDispatched(NewBook::class, function ($event) use ($book) {
        return $event->book->is($book);
    });
});

it('uses form request validation on update')
    ->assertActionUsesFormRequest(
        BookController::class,
        'update',
        BookUpdateRequest::class
    );

it('redirects on update', function () {
    $book = Book::factory()->create();
    $title = $this->faker->sentence(4);
    $email = $this->faker->safeEmail;
    $content = $this->faker->paragraphs(3, true);

    Notification::fake();
    Queue::fake();
    Event::fake();

    $response = $this->put(route('book.update', $book), [
        'title' => $title,
        'email' => $email,
        'content' => $content,
    ]);

    $book->refresh();

    $response->assertRedirect(route('book.index'));
    $response->assertSessionHas('book.title', $book->title);

    expect($book->title)->toBe($title);
    expect($book->email)->toBe($email);
    expect($book->content)->toBe($content);

    Notification::assertSentTo($book->author, ReviewNotification::class, function ($notification) use ($book) {
        return $notification->book->is($book);
    });
    Queue::assertPushed(SyncMedia::class, function ($job) use ($book) {
        return $job->book->is($book);
    });
    Event::assertDispatched(UpdatedBook::class, function ($event) use ($book) {
        return $event->book->is($book);
    });
});

it('deletes and redirects on destroy', function () {
    $book = Book::factory()->create();

    Notification::fake();
    Queue::fake();
    Event::fake();

    $response = $this->delete(route('book.destroy', $book));

    $response->assertRedirect(route('book.index'));

    $this->assertModelMissing($book);

    Notification::assertSentTo($book, ReviewNotification::class, function ($notification) use ($book) {
        return $notification->book->is($book);
    });
    Queue::assertPushed(SyncMedia::class, function ($job) use ($book) {
        return $job->book->is($book);
    });
    Event::assertDispatched(DeletedBook::class, function ($event) use ($book) {
        return $event->book->is($book);
    });
});
