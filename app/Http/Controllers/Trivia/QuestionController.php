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
    public TriviaService $service;
    public function __construct(TriviaService $service)
    {
        $this->service = $service;
    }

    public function getQuestion(): \Illuminate\Http\JsonResponse
    {
        $correctAnswerCount = session()->get('correctAnswerCount') ?? 0;
        Log::debug($correctAnswerCount);
        $question = $this->service->getTriviaQuestion();

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
            $this->service->forgetTriviaSessionValues();

            return response()->json([
                'lose' => 'You lost, correct answer was: '. $correctAnswer .', you answered correctly to '.
                    $correctAnswerCount . ($correctAnswerCount == 1 ? ' question.' : ' questions.')
            ]);
        }

        $correctAnswerCount++;

        if ($correctAnswerCount == env('TRIVIA_QUESTION_COUNT')) {
            $this->service->forgetTriviaSessionValues();

            return response()->json([
               'win' => 'You won, you answered '. env('TRIVIA_QUESTION_COUNT') .' questions correctly'
            ]);
        }

        session(['correctAnswerCount' => $correctAnswerCount]);

        return $this->getQuestion();
    }
}
