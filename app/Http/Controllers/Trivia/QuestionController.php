<?php

namespace App\Http\Controllers\Trivia;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trivia\AnswerRequest;
use App\Services\TriviaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    public $service;
    public function __construct(TriviaService $service)
    {
        $this->service = $service;
    }

    public function getQuestion(): \Illuminate\Http\JsonResponse
    {
        $question = $this->service->getQuestionFromAPI();

        return response()->json([
            'question' => $question
        ]);
    }
    public function postAnswer(AnswerRequest $request): \Illuminate\Http\JsonResponse
    {
        $userAnswer = $request->input('answer');
        $correctAnswer = session()->get('answer');

        $correctAnswerCount = session()->get('correctAnswerCount') ?? 0;

        if ($userAnswer != $correctAnswer) {
            session(['correctAnswerCount' => 0]);
            throw ValidationException::withMessages([
                'answer' => ['You lost, correct answer was: '. $correctAnswer .', you answered correctly to '.
                    $correctAnswerCount . ($correctAnswerCount === 1 ? ' questions.' : ' question.')]
            ]);
        }

        $correctAnswerCount++;
        session(['correctAnswerCount' => $correctAnswerCount]);

        return $this->getQuestion();
    }
}
