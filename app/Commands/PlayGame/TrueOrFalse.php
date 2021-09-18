<?php

namespace App\Commands\PlayGame;

use App\Commands\BaseCommand;

    class TrueOrFalse extends BaseCommand implements GameInterface {

    private $mode = 'trueOrFalse';
    private $answer;

    function processCommand()
    {
        if ($this->tgParser::getCallbackByKey('a') == 'stat') {
            $this->sendStatus();
            exit;
        }
        if ($this->userData['mode'] == $this->mode) {
            $this->getAnswer();
            $this->checkReply();
        } else {
            $this->db->table('scoreList')->insert(['chatId' => $this->chatId, 'gameId' => $this->userData['gameId']]);
            $this->db->table('userList')->where('chatId', $this->chatId)->update(['mode' => $this->mode]);
        }

        $this->sendMsg();
    }

    function sendMsg()
    {
        $data = $this->quizApi->call('trueOrFalse');
        $this->tg->sendMessageWithKeyboard($this->text['question'] . "\n" . $data['data'][0]['fact'], [
            [$this->text['trueFact'], $this->text['falseFact']],
            [$this->text['cancel']],
        ]);

        $this->insertAnswer($data);
    }

    function getAnswer()
    {
        $this->answer = $this->db->table('answers')->where('chatId', $this->chatId)->where('gameId', $this->userData['gameId'])->select(['answer'])->results();
    }

    function insertAnswer($data)
    {
        $this->db->table('answers')->insert(['chatId' => $this->chatId, 'gameId' => $this->userData['gameId'], 'answer' => $data['data'][0]['is_true']]);
    }

    function checkReply()
    {
        $answerArr = [
            '0' => $this->text['falseFact'],
            '1' => $this->text['trueFact'],
        ];

        if ($answerArr[$this->answer[0]['answer']] == $this->tgParser::getMessage()) {
            $mysql = "UPDATE scoreList SET score = score+1";
            $this->tg->sendMessage($this->text['right']);
        } else {
            $mysql = "UPDATE scoreList SET wrong = wrong+1";
            $this->tg->sendMessage($this->text['wrong']);
        }

        $mysql .= " WHERE chatId = " . $this->chatId . " AND gameId = " . $this->userData['gameId'] . " AND status = 'new'";
        $this->db->query($mysql);

        $this->db->query('DELETE FROM answers WHERE chatId = ' . $this->chatId);
    }

    function sendStatus()
    {
        // TODO make world status
        $score = $this->db->query('SELECT count(*) AS count, SUM(score) AS scoreSum, SUM(wrong) AS wrongSum, MAX(score) AS record  
                  FROM scoreList 
                  WHERE status = "done" AND gameId = 2 AND chatId = ' . $this->chatId)->results();
        if ($score[0]) {
            $this->tg->sendMessage($this->text['gameCount'] . $score[0]['count']
                . "\n" . $this->text['rightCount'] . $score[0]['scoreSum']
                . "\n" . $this->text['wrongCount'] . $score[0]['wrongSum']
                . "\n" . $this->text['record'] . $score[0]['record']);
        } else {
            $this->tg->sendMessage($this->text['noScore']);
        }
    }
}