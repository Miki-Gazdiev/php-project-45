<?php
namespace BrainGames\Games\Progression;

use function BrainGames\Engine\runGame;

const DESCRIPTION = 'What number is missing in the progression?';

function getQuestionAndAnswer() : array
{
    $start = rand(1, 20);
    $step = rand(1, 10);
    $sequenceOfNumbers = getSequenceOfNumbers($start, $step);
    $hiddenIndex = rand(0, count($sequenceOfNumbers) - 1);
    $correctAnswer = $sequenceOfNumbers[$hiddenIndex];
    $questionSequence = $sequenceOfNumbers;
    $questionSequence[$hiddenIndex] = '..';
    $question = implode(' ', $questionSequence);
    return [$question, (string)$correctAnswer];
}

function getSequenceOfNumbers(int $start, int $step) : array
{
	$result = [];
        $length = rand(5, 10);	
		for ($i = 0; $i < $length; $i++) {
		$result[] = $start + ($i * $step);
		} 
	return $result;
}

function run() : void
{
   runGame(DESCRIPTION, fn() => getQuestionAndAnswer());
}
