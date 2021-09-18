<?php

namespace App\Commands;

class EndPrevGame extends BaseCommand
{

    function processCommand()
    {
        $this->getBot()->sendMessage($this->user->chat_id, json_encode($this->user->game_entity));
    }

}