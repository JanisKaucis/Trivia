<?php

namespace App\Http\Controllers\Trivia;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trivia\AnswerRequest;
use App\Services\TriviaService;

class TriviaController extends Controller
{
    public TriviaService $service;

    public function __construct(TriviaService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return inertia('trivia/Trivia');
    }

    public function getTrivia(): \Illuminate\Http\JsonResponse
    {
        $trivia = $this->service->getTriviaQuestion();

        return response()->json([
            'question' => $trivia['question'],
            'answers' => $trivia['answers'],
        ]);
    }

    public function postAnswer(AnswerRequest $request): \Illuminate\Http\JsonResponse
    {
        $userAnswer = $request->input('answer');
        $correctAnswer = session()->get('answer');
        $correctAnswerCount = session()->get('correctAnswerCount') ?? 0;

        if ($userAnswer != $correctAnswer) {
            $this->service->forgetTriviaSessionValues();

            return response()->json([
                'lose' => 'You lost, correct answer was: ' . $correctAnswer . ', you answered correctly to ' .
                    $correctAnswerCount . ($correctAnswerCount == 1 ? ' question.' : ' questions.')
            ]);
        }

        $correctAnswerCount++;

        if ($correctAnswerCount == env('TRIVIA_QUESTION_COUNT')) {
            $this->service->forgetTriviaSessionValues();

            return response()->json([
                'win' => 'You won, you answered ' . env('TRIVIA_QUESTION_COUNT') . ' questions correctly!'
            ]);
        }

        session(['correctAnswerCount' => $correctAnswerCount]);

        return $this->getTrivia();
    }
}
