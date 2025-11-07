<?php

namespace BrainGames\Even;

use function cli\line;
use function cli\prompt;

function isEven(int $number) : bool
{
     return $number % 2 === 0;
}

function runGame(string $userName) : void
{
   line('Answer "yes" if the number is even, otherwise answer "no".');

   $correctAnswerToWin = 3;
   $correctAnswerCount = 0;

   while ($correctAnswerCount < $correctAnswerToWin) {
   $number = rand(1, 100);
   line('Question: %s', $number);
   $userAnswer = prompt('Your answer:');
   $correctAnswer = isEven($number) ? 'yes' : 'no';
   if ($userAnswer === $correctAnswer) {
	   line('Correct!');
	   $correctAnswerCount++;
   } else {
	   line("'%s' is wrong answer ;(. Correct answer was '%s'.", $userAnswer, $correctAnswer);
	   line("Let's try again, %s!", $userName);
	   return;
      }
   }
   line('Congratulations, %s!', $userName);
}
