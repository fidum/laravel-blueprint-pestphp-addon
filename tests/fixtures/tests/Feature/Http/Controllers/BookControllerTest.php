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
use App\Mail\ReviewNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

it('displays view on index', function () {
    $books = factory(Book::class, 3)->create();

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

    Mail::fake();
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
    assertCount(1, $books);
    $book = $books->first();

    $response->assertRedirect(route('book.index'));
    $response->assertSessionHas('book.title', $book->title);

    Mail::assertSent(ReviewNotification::class, function ($mail) use ($book) {
        return $mail->hasTo($book->author) && $mail->book->is($book);
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
    $book = factory(Book::class)->create();
    $title = $this->faker->sentence(4);
    $email = $this->faker->safeEmail;
    $content = $this->faker->paragraphs(3, true);

    Mail::fake();
    Queue::fake();
    Event::fake();

    $response = $this->put(route('book.update', $book), [
        'title' => $title,
        'email' => $email,
        'content' => $content,
    ]);

    $response->assertRedirect(route('book.index'));
    $response->assertSessionHas('book.title', $book->title);

    Mail::assertSent(ReviewNotification::class, function ($mail) use ($book) {
        return $mail->hasTo($book->author) && $mail->book->is($book);
    });
    Queue::assertPushed(SyncMedia::class, function ($job) use ($book) {
        return $job->book->is($book);
    });
    Event::assertDispatched(UpdatedBook::class, function ($event) use ($book) {
        return $event->book->is($book);
    });
});

it('deletes and redirects on destroy', function () {
    $book = factory(Book::class)->create();

    Mail::fake();
    Queue::fake();
    Event::fake();

    $response = $this->delete(route('book.destroy', $book));

    $response->assertRedirect(route('book.index'));

    $this->assertDeleted($book);

    Mail::assertSent(ReviewNotification::class, function ($mail) use ($book) {
        return $mail->hasTo($book) && $mail->book->is($book);
    });
    Queue::assertPushed(SyncMedia::class, function ($job) use ($book) {
        return $job->book->is($book);
    });
    Event::assertDispatched(DeletedBook::class, function ($event) use ($book) {
        return $event->book->is($book);
    });
});
