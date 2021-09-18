<?php

namespace App\Commands;

use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class GameMenu extends BaseCommand
{

    function processCommand()
    {
        $gameId = json_decode($this->update->getCallbackQuery()->getData(), true)['id'];

        $buttons = [[[
            'text' => $this->text['playByOwn'],
            'callback_data' => json_encode([
                'a' => 'playAlone',
                'id' => $gameId
            ])
        ]]];

        $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, "<b>" . $this->russianAlphabet['gameList'][$gameId] . "</b>" . "\n" . $this->text['whatToDo'], new InlineKeyboardMarkup($buttons));
    }

}