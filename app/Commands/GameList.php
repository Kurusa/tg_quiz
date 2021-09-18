<?php

namespace App\Commands;

use App\Models\Game;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class GameList extends BaseCommand
{

    function processCommand()
    {
        $buttons = [];
        foreach (Game::all() as $game) {
            $buttons[] = [
                $game['title']
            ];
        }

        $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['gameList'], new ReplyKeyboardMarkup($buttons, false, true));
    }

}