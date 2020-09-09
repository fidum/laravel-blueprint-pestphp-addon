<?php

namespace Tests\Feature\Http\Controllers;

use App\Events\NewPost;
use App\Http\Controllers\PostController;
use App\Http\Requests\PostStoreRequest;
use App\Jobs\SyncMedia;
use App\Mail\ReviewPost;
use App\Post;
use App\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

it('displays view on index', function () {
    $posts = factory(Post::class, 3)->create();

    $response = $this->get(route('post.index'));

    $response->assertOk();
    $response->assertViewIs('post.index');
    $response->assertViewHas('posts');
});

it('uses form request validation on store')
    ->assertActionUsesFormRequest(
        PostController::class,
        'store',
        PostStoreRequest::class
    );

it('saves and redirects on store', function () {
    $title = $this->faker->sentence(4);
    $content = $this->faker->paragraphs(3, true);
    $author = factory(User::class)->create();

    Mail::fake();
    Queue::fake();
    Event::fake();

    $response = $this->post(route('post.store'), [
        'title' => $title,
        'content' => $content,
        'author_id' => $author->id,
    ]);

    $posts = Post::query()
        ->where('title', $title)
        ->where('content', $content)
        ->where('author_id', $author->id)
        ->get();
    expect($posts)->toHaveCount(1);
    $post = $posts->first();

    $response->assertRedirect(route('post.index'));
    $response->assertSessionHas('post.title', $post->title);

    Mail::assertSent(ReviewPost::class, function ($mail) use ($post) {
        return $mail->hasTo($post->author->email) && $mail->post->is($post);
    });
    Queue::assertPushed(SyncMedia::class, function ($job) use ($post) {
        return $job->post->is($post);
    });
    Event::assertDispatched(NewPost::class, function ($event) use ($post) {
        return $event->post->is($post);
    });
});
