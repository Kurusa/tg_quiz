<?php

namespace App\Commands\PlayGame;

use App\Commands\BaseCommand;
use App\TgHelpers\TelegramKeyboard;

class WordsAnswer extends BaseCommand {

    function processCommand()
    {
        TelegramKeyboard::addButton($this->text['playByOwn'], ['a' => 'own', 'id' => 4]);
        $this->tg->updateMessageKeyboard($this->tgParser::getMsgId(), $this->text['seeRightAnswer'] . ': <b>' . $this->userData['mode'] . '</b>', TelegramKeyboard::get());
        $this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => 'done']);
    }

}