<?php

namespace App\Commands\PlayGame;

use App\Commands\BaseCommand;
use App\Services\Status\UserStatusService;

class TasksList extends BaseCommand implements GameInterface
{
    function processCommand()
    {
        $this->user->status = UserStatusService::TASKS;
        $this->user->save();

        $this->sendMsg();
    }

    function sendMsg()
    {
        $game_id = json_decode($this->update->getCallbackQuery()->getData(), true)['id'];

        $data = $this->quizApi->call($game_id);
        $this->getBot()->sendMessage($this->user->chat_id, \GuzzleHttp\json_encode($data));
    }

    function sendStatus()
    {
        // TODO: Implement sendStatus() method.
    }

    function checkDie()
    {
        // TODO: Implement checkDie() method.
    }

    function checkReply()
    {
        // TODO: Implement checkReply() method.
    }

    function setAnswer()
    {
        // TODO: Implement setAnswer() method.
    }

    function setScore()
    {
        // TODO: Implement setScore() method.
    }

    function createGameEntity()
    {
        // TODO: Implement createGameEntity() method.
    }
}