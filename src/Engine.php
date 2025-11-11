<?php

namespace BrainGames\Engine;

use function cli\line;
use function cli\prompt;

function runGame(string $description, callable $getQuestionAndAnswer): void
{
    line('Welcome to the Brain Games!');
    $userName = prompt('May I have your name?');
    line("Hello, %s!", $userName);
    line($description);

    $roundsCount = 3;

    for ($i = 0; $i < $roundsCount; $i++) {
        [$question, $correctAnswer] = $getQuestionAndAnswer();
        line("Question: %s", $question);
        $userAnswer = prompt('Your answer');

        if ($userAnswer === $correctAnswer) {
            line('Correct!');
        } else {
            line("'%s' is wrong answer ;(. Correct answer was '%s'.", $userAnswer, $correctAnswer);
            line("Let's try again, %s!", $userName);
            return;
        }
    }

    line('Congratulations, %s!', $userName);
}
