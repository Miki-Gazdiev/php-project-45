<?php

require_once __DIR__ . '/vendor/autoload.php';

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;

class BrainGamesBot
{
    private $telegram;
    private $bot_username;
    private $bot_token;
    
    // Хранилище состояния игроков
    private $userStates = [];

    public function __construct($token, $username)
    {
        $this->bot_token = $token;
        $this->bot_username = $username;
        $this->telegram = new Telegram($this->bot_token, $this->bot_username);
    }

    public function handleUpdate($update)
    {
        try {
            $message = $update->getMessage();
            if (!$message) {
                return;
            }

            $chatId = $message->getChat()->getId();
            $text = $message->getText();
            $userId = $message->getFrom()->getId();

            // Обработка команд
            if (strpos($text, '/') === 0) {
                $this->handleCommand($chatId, $text, $userId);
                return;
            }

            // Обработка ответов в активной игре
            if (isset($this->userStates[$userId]['game'])) {
                $this->handleGameAnswer($chatId, $text, $userId);
                return;
            }

            $this->sendMessage($chatId, "Выберите игру с помощью команд:\n/start - меню\n/games - список игр");

        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
        }
    }

    private function handleCommand($chatId, $command, $userId)
    {
        switch ($command) {
            case '/start':
                $this->showMainMenu($chatId);
                break;
            case '/games':
                $this->showGamesMenu($chatId);
                break;
            case '/game_even':
                $this->startGame($chatId, $userId, 'even');
                break;
            case '/game_calc':
                $this->startGame($chatId, $userId, 'calc');
                break;
            case '/game_gcd':
                $this->startGame($chatId, $userId, 'gcd');
                break;
            case '/game_progression':
                $this->startGame($chatId, $userId, 'progression');
                break;
            case '/game_prime':
                $this->startGame($chatId, $userId, 'prime');
                break;
            case '/stop':
                $this->stopGame($chatId, $userId);
                break;
            default:
                $this->sendMessage($chatId, "Неизвестная команда. Используйте /start для начала.");
        }
    }

    private function showMainMenu($chatId)
    {
        $text = "🎮 Добро пожаловать в Brain Games!\n\n";
        $text .= "Я бот с коллекцией математических игр для развития ума.\n\n";
        $text .= "Доступные команды:\n";
        $text .= "/games - список всех игр\n";
        $text .= "/stop - остановить текущую игру\n\n";
        $text .= "Выберите игру и начните тренировать свой мозг!";

        $this->sendMessage($chatId, $text);
    }

    private function showGamesMenu($chatId)
    {
        $text = "🎯 Доступные игры:\n\n";
        $text .= "🔹 /game_even - Проверка на четность\n";
        $text .= "🔹 /game_calc - Арифметические примеры\n";
        $text .= "🔹 /game_gcd - Наибольший общий делитель\n";
        $text .= "🔹 /game_progression - Арифметическая прогрессия\n";
        $text .= "🔹 /game_prime - Простые числа\n\n";
        $text .= "Выберите игру и введите команду для начала!";

        $this->sendMessage($chatId, $text);
    }

    private function startGame($chatId, $userId, $gameType)
    {
        $descriptions = [
            'even' => 'Ответьте "yes", если число четное, и "no", если нечетное.',
            'calc' => 'Решите арифметический пример.',
            'gcd' => 'Найдите наибольший общий делитель двух чисел.',
            'progression' => 'Найдите пропущенное число в прогрессии.',
            'prime' => 'Ответьте "yes", если число простое, и "no", если составное.'
        ];

        $this->userStates[$userId] = [
            'game' => $gameType,
            'round' => 0,
            'score' => 0,
            'total_rounds' => 3
        ];

        $this->sendMessage($chatId, "🎮 Игра: " . $this->getGameName($gameType));
        $this->sendMessage($chatId, $descriptions[$gameType]);
        $this->sendNextQuestion($chatId, $userId);
    }

    private function getGameName($gameType)
    {
        $names = [
            'even' => 'Проверка на четность',
            'calc' => 'Калькулятор',
            'gcd' => 'Наибольший общий делитель',
            'progression' => 'Арифметическая прогрессия',
            'prime' => 'Простые числа'
        ];
        return $names[$gameType];
    }

    private function sendNextQuestion($chatId, $userId)
    {
        $gameType = $this->userStates[$userId]['game'];
        [$question, $correctAnswer] = $this->generateQuestion($gameType);

        $this->userStates[$userId]['current_question'] = $question;
        $this->userStates[$userId]['current_answer'] = $correctAnswer;
        $this->userStates[$userId]['round']++;

        $round = $this->userStates[$userId]['round'];
        $total = $this->userStates[$userId]['total_rounds'];

        $this->sendMessage($chatId, "🔹 Раунд {$round}/{$total}\n❓ Вопрос: {$question}");
    }

    private function generateQuestion($gameType)
    {
        switch ($gameType) {
            case 'even':
                return BrainGames\Games\Even\getQuestionAndAnswer();
            case 'calc':
                return BrainGames\Games\Calc\getQuestionAndAnswer();
            case 'gcd':
                return BrainGames\Games\Gcd\getQuestionAndAnswer();
            case 'progression':
                return BrainGames\Games\Progression\getQuestionAndAnswer();
            case 'prime':
                return BrainGames\Games\Prime\getQuestionAndAnswer();
            default:
                return ['', ''];
        }
    }

    private function handleGameAnswer($chatId, $userAnswer, $userId)
    {
        $state = $this->userStates[$userId];
        $correctAnswer = $state['current_answer'];

        if (strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer))) {
            $this->userStates[$userId]['score']++;
            $message = "✅ Правильно!";
        } else {
            $message = "❌ Неправильно! Правильный ответ: {$correctAnswer}";
        }

        $this->sendMessage($chatId, $message);

        // Проверяем, завершена ли игра
        if ($state['round'] >= $state['total_rounds']) {
            $this->finishGame($chatId, $userId);
        } else {
            sleep(1);
            $this->sendNextQuestion($chatId, $userId);
        }
    }

    private function finishGame($chatId, $userId)
    {
        $state = $this->userStates[$userId];
        $score = $state['score'];
        $total = $state['total_rounds'];
        $gameName = $this->getGameName($state['game']);

        if ($score == $total) {
            $message = "🎉 Поздравляем! Вы отлично справились с игрой \"{$gameName}\"!";
        } else {
            $message = "🏁 Игра \"{$gameName}\" завершена!\nРезультат: {$score}/{$total}";
        }

        $this->sendMessage($chatId, $message);
        unset($this->userStates[$userId]);

        // Предлагаем сыграть еще
        $this->sendMessage($chatId, "Хотите сыграть еще? Используйте /games для выбора игры.");
    }

    private function stopGame($chatId, $userId)
    {
        if (isset($this->userStates[$userId])) {
            unset($this->userStates[$userId]);
            $this->sendMessage($chatId, "Игра остановлена. Используйте /games для выбора новой игры.");
        } else {
            $this->sendMessage($chatId, "Сейчас нет активной игры. Используйте /games для начала.");
        }
    }

    private function sendMessage($chatId, $text)
    {
        try {
            $data = [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML'
            ];
            
            return Request::sendMessage($data);
        } catch (Exception $e) {
            error_log("Send message error: " . $e->getMessage());
        }
    }

    public function runLongPolling()
    {
        $last_update_id = 0;
        
        while (true) {
            try {
                $response = $this->telegram->handleGetUpdates([
                    'offset' => $last_update_id + 1,
                    'limit' => 100,
                    'timeout' => 30,
                ]);

                $updates = $response->getResult();
                foreach ($updates as $update) {
                    $last_update_id = $update->getUpdateId();
                    $this->handleUpdate($update);
                }

                sleep(1);
            } catch (Exception $e) {
                error_log("Long polling error: " . $e->getMessage());
                sleep(5);
            }
        }
    }
}

// Запуск бота
if (php_sapi_name() === 'cli') {
    $bot_token = 'YOUR_BOT_TOKEN_HERE';
    $bot_username = 'YOUR_BOT_USERNAME_HERE';
    
    $bot = new BrainGamesBot($bot_token, $bot_username);
    echo "Bot started...\n";
    $bot->runLongPolling();
}
