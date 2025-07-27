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

    public function getQuestion()
    {
        $question = $this->service->getQuestionFromAPI();

        return response()->json([
            'question' => $question
        ]);
    }
    public function postAnswer(AnswerRequest $request)
    {
        $answer = $request->input('answer');

        if ($answer != session()->get('answer')) {
            throw ValidationException::withMessages([
                'answer' => ['You lost']
            ]);
        }
        return $this->getQuestion();
    }
}
