<?php

namespace BrainGames\Games\Gcd;

use function BrainGames\Engine\runGame;

const DESCRIPTION = 'Find the greatest common divisor of given numbers.';

function findGcd(int $a, int $b) : int 
{
    if($b === 0) {
    return $a;
    }
    while ($b != 0) {
        $temp = $b;
        $b = $a % $b;
        $a = $temp;
    }
    return $a;
}
function getQuestionAndAnswer(): array
{
    $num1 = rand(1, 100);
    $num2 = rand(1, 100);
    $question = "{$num1} {$num2}";
    $correctAnswer = findGcd($num1, $num2);
    
    return [$question,(string) $correctAnswer];
}

function run() : void
{
  runGame(DESCRIPTION, fn() => getQuestionAndAnswer());
}
