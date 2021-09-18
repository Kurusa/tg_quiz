<?php

namespace App\Commands\PlayGame;

use App\Commands\BaseCommand;
use App\Models\Game;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class TriggerGameActions extends BaseCommand
{

    function processCommand()
    {
        $game_id = json_decode($this->update->getCallbackQuery()->getData(), true)['id'];
        $action = json_decode($this->update->getCallbackQuery()->getData(), true)['a'];

        // одиночная игра
        if ($action == 'own') {
            $this->getBot()->deleteMessage($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId());

            // если пользователь не закончил предыдующую игру
            if ($this->user->game_id !== 0) {
                // кинуть предупреждение о том, чтобы закончить предыдущую игру
                $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['endGame'], new InlineKeyboardMarkup([
                    [
                        'text' => $this->text['end_prev_game_button'],
                        'callback_data' => json_encode([
                            'a' => 'end_prev_game'
                        ])
                    ]
                ]));
            } else {
                $this->user->game_id = $game_id;
                $this->user->save();
            }
        }

        $this->triggerCommand('App\Commands\PlayGame\\' . Game::find($game_id)['trigger_class']);

    }

}