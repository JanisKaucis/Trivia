<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class  TriviaService
{
    function getQuestionFromAPI(): bool|string
    {
        $path = env('TRIVIA_API');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $retValue = curl_exec($ch);
        curl_close($ch);
        $question = $this->saveTriviaQuestions($retValue);

        return $question;
    }

    public function saveTriviaQuestions($retValue)
    {
        $triviaString = explode(' is the ', $retValue);
        $question = $triviaString[1];
        $answer = $triviaString[0];

        session(['question' => $question,'answer' => $answer]);

        Log::debug($answer);

        return $question;
    }
}
