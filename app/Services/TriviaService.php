<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class  TriviaService
{
    public function getTriviaQuestion(): string
    {
        $triviaString = $this->getQuestionFromAPI();

        while ($this->checkIfTriviaRepeatsItself($triviaString)) {
            $triviaString = $this->getTriviaQuestion();
        }

        $pattern = '/^([\d.+eE+-]+)\s(is(?:\s(?:a|the))?)\s(.+)$/';
        preg_match($pattern, $triviaString, $triviaArray);

        $answer = $triviaArray[1];
        $question = $triviaArray[3];

        $this->saveTrivia($answer, $triviaString);

        Log::debug($answer);

        return $question;
    }

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

        return $retValue;
    }

    public function saveTrivia($answer, $triviaString): void
    {
        session(['answer' => $answer]);

        if (session()->has('triviaStrings')) {
            session()->push('triviaStrings', $triviaString);
        }else {
            session()->put('triviaStrings', [$triviaString]);
        }
    }

    public function checkIfTriviaRepeatsItself($triviaString): bool
    {
        $triviaStrings = session()->get('triviaStrings') ?? [];

        if (in_array($triviaString, $triviaStrings)) {
            return true;
        }

        return false;
    }

    public function forgetTriviaSessionValues(): void
    {
        session()->flush();
    }
}
