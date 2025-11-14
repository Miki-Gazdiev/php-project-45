<?php

namespace BrainGames\Games\Even;

use function BrainGames\Engine\runGame;

const DESCRIPTION = 'Answer "yes" if the number is even, otherwise answer "no".';
function isEven(int $number) : bool
{
  return $number % 2 === 0;
}

function getQuestionAndAnswer() : array
{
   $number = rand(1, 100);
   $correctAnswer = isEven($number) ? 'yes' : 'no';
   return [$number, (string)$correctAnswer];
}

function run() : void
{
  runGame(DESCRIPTION, fn() => getQuestionAndAnswer());
}
