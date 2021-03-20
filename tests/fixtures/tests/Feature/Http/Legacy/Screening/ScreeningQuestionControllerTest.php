<?php

namespace Tests\Feature\Http\Controllers\Screening;

use App\Http\Controllers\Screening\ScreeningQuestionController;
use App\Http\Requests\Screening\ScreeningQuestionStoreRequest;
use App\Http\Requests\Screening\ScreeningQuestionUpdateRequest;
use App\Models\Appointment\AppointmentType;
use App\Models\QuestionType;
use App\Models\Screening\Report;
use App\Models\Screening\ScreeningQuestion;

it('displays view on index', function () {
    $screeningQuestions = factory(ScreeningQuestion::class, 3)->create();

    $response = $this->get(route('screening-question.index'));

    $response->assertOk();
    $response->assertViewIs('screeningQuestion.index');
    $response->assertViewHas('screeningQuestions');
});

it('displays view on create', function () {
    $response = $this->get(route('screening-question.create'));

    $response->assertOk();
    $response->assertViewIs('screeningQuestion.create');
});

it('uses form request validation on store')
    ->assertActionUsesFormRequest(
        ScreeningQuestionController::class,
        'store',
        ScreeningQuestionStoreRequest::class
    );

it('saves and redirects on store', function () {
    $report = factory(Report::class)->create();
    $appointment_type = factory(AppointmentType::class)->create();
    $question_type = factory(QuestionType::class)->create();

    $response = $this->post(route('screening-question.store'), [
        'report_id' => $report->id,
        'appointment_type_id' => $appointment_type->id,
        'question_type_id' => $question_type->id,
    ]);

    $screeningQuestions = ScreeningQuestion::query()
        ->where('report_id', $report->id)
        ->where('appointment_type_id', $appointment_type->id)
        ->where('question_type_id', $question_type->id)
        ->get();
    expect($screeningQuestions)->toHaveCount(1);
    $screeningQuestion = $screeningQuestions->first();

    $response->assertRedirect(route('screeningQuestion.index'));
    $response->assertSessionHas('screeningQuestion.id', $screeningQuestion->id);
});

it('displays view on show', function () {
    $screeningQuestion = factory(ScreeningQuestion::class)->create();

    $response = $this->get(route('screening-question.show', $screeningQuestion));

    $response->assertOk();
    $response->assertViewIs('screeningQuestion.show');
    $response->assertViewHas('screeningQuestion');
});

it('displays view on edit', function () {
    $screeningQuestion = factory(ScreeningQuestion::class)->create();

    $response = $this->get(route('screening-question.edit', $screeningQuestion));

    $response->assertOk();
    $response->assertViewIs('screeningQuestion.edit');
    $response->assertViewHas('screeningQuestion');
});

it('uses form request validation on update')
    ->assertActionUsesFormRequest(
        ScreeningQuestionController::class,
        'update',
        ScreeningQuestionUpdateRequest::class
    );

it('redirects on update', function () {
    $screeningQuestion = factory(ScreeningQuestion::class)->create();
    $report = factory(Report::class)->create();
    $appointment_type = factory(AppointmentType::class)->create();
    $question_type = factory(QuestionType::class)->create();

    $response = $this->put(route('screening-question.update', $screeningQuestion), [
        'report_id' => $report->id,
        'appointment_type_id' => $appointment_type->id,
        'question_type_id' => $question_type->id,
    ]);

    $screeningQuestion->refresh();

    $response->assertRedirect(route('screeningQuestion.index'));
    $response->assertSessionHas('screeningQuestion.id', $screeningQuestion->id);

    expect($screeningQuestion->report_id)->toBe($report->id);
    expect($screeningQuestion->appointment_type_id)->toBe($appointment_type->id);
    expect($screeningQuestion->question_type_id)->toBe($question_type->id);
});

it('deletes and redirects on destroy', function () {
    $screeningQuestion = factory(ScreeningQuestion::class)->create();

    $response = $this->delete(route('screening-question.destroy', $screeningQuestion));

    $response->assertRedirect(route('screeningQuestion.index'));

    $this->assertDeleted($screeningQuestion);
});
