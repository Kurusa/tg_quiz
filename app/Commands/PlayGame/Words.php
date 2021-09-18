<?php

namespace App\Commands\PlayGame;

use App\Models\{Answer, GameEntity};
use App\Commands\BaseCommand;
use App\Services\TelegramKeyboard;
use Illuminate\Database\Capsule\Manager as DB;
use TelegramBot\Api\Types\{Inline\InlineKeyboardMarkup, ReplyKeyboardMarkup};

class Words extends BaseCommand implements GameInterface
{

    private $answer;
    private $score;

    function processCommand()
    {
        $this->answer = Answer::where('user_id', $this->user->id)->where('game_id', 1)->first()->answer;
        if (!$this->answer) {
            $data = $this->getQuizApi()->call(1, ['rus' => 1]);
            $this->answer = Answer::create([
                'user_id' => $this->user->id,
                'game_id' => 1,
                'answer' => mb_strtolower($data['data']['word'])
            ])->answer;
        }

        // получить уже сохраненное ранее слово
        $word = $this->answer;

        $guesses = $this->score[0]->guess ? explode(' ', $this->score[0]->guess) : false;

        // строится строка с сердечками
        $text = '';
        for ($i = 0; $i < $this->score[0]->lives; $i++) {
            $text .= '❤️';
        }

        $text .= "\n" . "\n" . $this->text['word'] . "\n";

        // добавить само слово или черточки в строку
        if (!$guesses) {
            for ($i = 0; $i < mb_strlen($word); $i++) {
                $text .= ' _ ';
            }
        } else {
            $word_letters_list = mb_str_split($word);
            $array_diff = array_diff($word_letters_list, $guesses);
            $text .= str_replace($array_diff, ' _ ', $word);
        }

        // строится клавиатура с буквами
        $range = array_flip($this->russianAlphabet['russian']);
        $range = $guesses ? array_diff($range, $guesses) : $range;
        TelegramKeyboard::$columns = 8;
        TelegramKeyboard::$list = $range;
        TelegramKeyboard::$action = 'le';
        TelegramKeyboard::build();

        if ($this->update) {
            $this->getBot()->editMessageText($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId(), $text, 'html', false, new InlineKeyboardMarkup(TelegramKeyboard::get()));
        } else {
            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['play'], new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));
            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $text, new InlineKeyboardMarkup(TelegramKeyboard::get()));
        }

    }

    function sendMsg($update = false)
    {
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

    function youDied()
    {
        /*$this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $this->answer[0]['answer']]);
        TelegramKeyboard::addButton($this->text['seeRightAnswer'], ['a' => 'wAns']);
        TelegramKeyboard::addButton($this->text['playByOwn'], ['a' => 'own', 'id' => 4]);
        TelegramKeyboard::addButton($this->text['gameStat'], ['a' => 'stat', 'id' => 4]);
        $this->db->query('DELETE FROM answers WHERE chatId = ' . $this->chatId);
        $this->tg->updateMessageKeyboard($this->tgParser::getMsgId(), $this->text['youDied'], TelegramKeyboard::get());
        $this->db->table('scoreList')->where('chatId', $this->chatId)->where('gameId', 4)->where('status', 'new')->update(['status' => 'done', 'wrong' => 1]);*/
    }

    function youWon()
    {
        /* $this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => 'done']);
         TelegramKeyboard::$buttons = [];
         TelegramKeyboard::addButton($this->text['playByOwn'], ['a' => 'own', 'id' => 4]);
         TelegramKeyboard::addButton($this->text['gameStat'], ['a' => 'stat', 'id' => 4]);
         $this->db->query('DELETE FROM answers WHERE chatId = ' . $this->chatId);
         $this->tg->updateMessageKeyboard($this->tgParser::getMsgId(), $this->text['youWon'], TelegramKeyboard::get());
         $this->db->table('scoreList')->where('chatId', $this->chatId)->where('gameId', 4)->where('status', 'new')->update(['status' => 'done', 'score' => 1]);*/
    }

    function sendStatus()
    {
        // TODO make world status
        /*  $score = $this->db->query('SELECT count(*) AS count, SUM(score) AS scoreSum, SUM(wrong) AS wrongSum, MAX(score) AS record
                    FROM scoreList
                    WHERE status = "done" AND gameId = 4 AND chatId = ' . $this->chatId)->results();

          if ($score[0]) {
              $this->tg->sendMessage($this->text['gameCount'] . $score[0]['count']
                  . "\n" . $this->text['winsCount'] . $score[0]['scoreSum']
                  . "\n" . $this->text['diesCount'] . $score[0]['wrongSum']
                  . "\n" . $this->text['record'] . $score[0]['record']);
          } else {
              $this->tg->sendMessage($this->text['noScore']);
          }*/
    }

    function checkDie(): bool
    {
        $dies = (int)(self::MAX_LIVES - $this->score->lives);
        if ($dies === self::MAX_LIVES) {
            $this->youDied();
            return true;
        }
        return false;
    }

    function setScore()
    {
        $this->score = GameEntity::firstOrCreate([
            'user_id' => $this->user->id,
            'game_id' => 1,
        ], [
            'status' => 'NEW',
            'lifes' => self::MAX_LIVES
        ]);
    }

    function setAnswer(): void
    {

    }

    function createGameEntity()
    {
        GameEntity::create([
            'user_id' => $this->user->id,
            'game_id' => 1
        ]);
    }

    function processGame()
    {
        // TODO: Implement processGame() method.
    }
}