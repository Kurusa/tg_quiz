<?php

namespace App\Commands\PlayGame;

use App\Models\Answer;
use App\Services\TelegramKeyboard;
use Illuminate\Database\Capsule\Manager as DB;
use App\Commands\BaseCommand;
use App\Models\GameEntity;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class FindPair extends BaseCommand implements GameInterface
{

    private $answer;
    private $score;

    function processCommand()
    {
        // получить ход игры
        $this->setScore();
        // получить json с ответами
        $this->getAnswer();

        $a = false;
        if ($a == true) {
            $this->checkReply();
            $this->sendMsg(true);
        } else {
            $this->sendMsg();
        }
    }

    function sendMsg($update = false)
    {
        $pairs = json_decode($this->answer[0]['answer'], true);

        $buttons = [];
        foreach ($pairs as $key => $pair) {
            foreach ($pair as $item) {
                $buttons[$key][] = [
                    'text' => $item['native'],
                    'callback_data' => json_encode([
                        'a' => 'pair',
                        'val' => $item['unicode']
                    ])
                ];
            }
        }

        if ($update) {
            $this->getBot()->editMessageText($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId(), $this->text['find_pair'], 'html', false, new InlineKeyboardMarkup(TelegramKeyboard::get()));
        } else {
            $update = $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['play'], new InlineKeyboardMarkup($buttons));
            sleep(2);

            $buttons = [];
            foreach ($pairs as $key => $pair) {
                foreach ($pair as $item) {
                    $buttons[$key][] = [
                        'text' => '',
                        'callback_data' => json_encode([
                            'a' => 'pair',
                            'val' => $item['unicode']
                        ])
                    ];
                }
            }
            $this->getBot()->editMessageReplyMarkup($this->user->chat_id, $update->getMessageId(), new InlineKeyboardMarkup($buttons));
        }

    }

    function checkReply()
    {
        $id = json_decode($this->update->getCallbackQuery()->getData(), true)['id'];

        // если такая буква есть в слове
        if (strpos($this->answer[0]->answer, $id) !== false) {
            $this->getBot()->answerCallbackQuery($this->update->getCallbackQuery()->getId(), $this->text['right']);
        } else {
            // no such letter
            $this->getBot()->answerCallbackQuery($this->update->getCallbackQuery()->getId(), $this->text['wrong']);
            GameEntity::where('user_id', $this->user->id)->where('game_id', $this->user->game_id)->where('status', 'NEW')->decrement('lives', 1);
        }

        DB::statement('UPDATE score SET guess = CONCAT(guess, "' . $id . ' ") WHERE user_id = ' . $this->user->id . ' AND game_id = ' . $this->user->game_id . ' AND status = "NEW"');

        $guess = GameEntity::where('user_id', $this->user->id)->where('game_id', $this->user->game_id)->where('status', 'NEW')->get();
        $guesses = $guess[0]['guess'] ? explode(' ', $guess[0]['guess']) : false;

        if ($guesses) {
            $count = 0;
            foreach ($guesses as $guess) {
                if ($guess) {
                    if (strpos($this->answer[0]['answer'], $guess) !== false) {
                        $count++;
                    }
                }
            }
            // пользователь выиграл
            if ($count == mb_strlen($this->answer[0]['answer'])) {
                $this->youWon();
                exit();
            }
        }
    }

    function youWon()
    {
    }

    function sendStatus()
    {
    }

    function setScore()
    {
        $this->score = GameEntity::where('user_id', $this->user->id)->where('game_id', 5)->where('status', 'NEW')->get();
        if (!$this->score) {
            GameEntity::create([
                'user_id' => $this->user->id,
                'game_id' => 5
            ]);
            $this->score = GameEntity::where('user_id', $this->user->id)->where('game_id', 5)->where('status', 'NEW')->get();
        }
    }

    function getAnswer()
    {
        DB::statement('SET NAMES utf8mb4');

        $this->answer = Answer::where('user_id', $this->user->id)->where('game_id', 5)->get();
        if (!$this->answer[0]->answer) {
            $emoji_array = [];
            for ($i = 0; $i < 3; $i++) {
                $emoji_data = $this->curl('emoji.kurusa.uno');
                $emoji_array[0][$i]['native'] = $emoji_data[0]['native'];
                $emoji_array[0][$i]['unicode'] = $emoji_data[0]['unicode'];
            }

            $emoji_array[1] = $emoji_array[0];
            $rand_keys = array_rand($emoji_array[1], count($emoji_array[1]));
            shuffle($rand_keys);

            uksort($emoji_array[1], function ($a, $b) use ($rand_keys) {
                return array_search($a, $rand_keys) - array_search($b, $rand_keys);
            });
            $this->answer = Answer::create([
                'user_id' => $this->user->id,
                'game_id' => 5,
                'answer' => json_encode($emoji_array, JSON_UNESCAPED_UNICODE)
            ]);
        }
    }

    function checkDie(): bool
    {
    }

    function curl($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,
            CURLINFO_HEADER_OUT, true); // enable tracking
        $result = curl_exec($curl);
        return json_decode($result, true);
    }
}