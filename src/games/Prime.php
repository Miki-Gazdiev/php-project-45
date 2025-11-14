<?php

namespace BrainGames\Games\Prime;

use function BrainGames\Engine\runGame;

const DESCRIPTION = 'Answer "yes" if given number is prime. Otherwise answer "no".';

function getQuestionAndAnswer(): array
{
    $question = rand(1, 100);
    $correctAnswer = isPrime($question) ? 'yes' : 'no';
    return [(string)$question, $correctAnswer];
}

function isPrime(int $number): bool
{
    if ($number < 2) {
        return false;
    }

    for ($i = 2; $i < $number; $i++) {
        if ($number % $i === 0) {
            return false;
        }
    }
    return true;
}

function run(): void
{
    runGame(DESCRIPTION, fn() => getQuestionAndAnswer());
}
