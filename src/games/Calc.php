<?php

namespace BrainGames\Games\Calc;

use function BrainGames\Engine\runGame;

function run(): void
{
    $description = 'What is the result of the expression?';
    runGame($description, fn() => generateQuestionAndAnswer());
}

function generateQuestionAndAnswer(): array
{
    $operators = ['+', '-', '*'];
    $number1 = rand(1, 100);
    $number2 = rand(1, 100);
    $operator = $operators[array_rand($operators)];

    $question = "{$number1} {$operator} {$number2}";
    $correctAnswer = calculate($number1, $number2, $operator);

    return [$question, (string)$correctAnswer];
}

function calculate(int $num1, int $num2, string $operator): int
{
    switch ($operator) {
        case '+':
            return $num1 + $num2;
        case '-':
            return $num1 - $num2;
        case '*':
            return $num1 * $num2;
        default:
            throw new \InvalidArgumentException("Unknown operator: {$operator}");
    }
}
