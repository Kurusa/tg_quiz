<?php

namespace App\Commands\PlayGame;

use App\Commands\BaseCommand;

class TruthOrDare extends BaseCommand implements GameInterface {

    private $mode = 'truthOrDare';
    private $answer;

    function processCommand()
    {
        if ($this->tgParser::getCallbackByKey('a') == 'stat') {
            $this->sendStatus();
            exit;
        }

        if ($this->userData['mode'] == $this->mode) {
            $this->checkReply();
        } else {
            $this->db->table('scoreList')->insert(['chatId' => $this->chatId, 'gameId' => $this->userData['gameId']]);
            $this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
        }

        if ($par == 'own') {
            $this->sendMsg();
        }
    }

    function sendMsg()
    {
        $this->tg->sendMessageWithKeyboard($par ? $par : $this->text['truthOrDare'], [
            [$this->text['truth'], $this->text['dare']],
            [$this->text['cancel']],
        ]);
    }

    function getAnswer()
    {
    }

    function insertAnswer($data)
    {
    }

    function checkReply()
    {
        if ($this->tgParser::getMessage() == $this->text['truth']) {
            $data = $this->quizApi->call('truthOrDare', ['dare']);
        } elseif ($this->tgParser::getMessage() == $this->text['dare']) {
             $data = $this->quizApi->call('truthOrDare', ['dare']);
        }
        error_log(json_encode($data['data']));
        $this->tg->sendMessage($data['data']);
    }

    function sendStatus()
    {
    }

    function checkDie()
    {
        // TODO: Implement checkDie() method.
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