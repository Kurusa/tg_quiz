<?php

namespace App\Commands;

use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Feedback extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status === UserStatusService::FEEDBACK) {
            if ($this->update->getMessage()) {
                \App\Models\Feedback::create([
                    'user_id' => $this->user->id,
                    'text' => $this->update->getMessage()->getText()
                ]);
                $this->getBot()->sendMessage($this->user->chat_id, $this->text['msgSend']);
            }
        } else {
            $this->user->status = UserStatusService::FEEDBACK;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['preSendFeedback'], new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));
        }
    }

}