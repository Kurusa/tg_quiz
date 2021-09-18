<?php

namespace App\Commands;

use App\Models\GameEntity;
use App\Services\Status\UserStatusService;
use Illuminate\Database\Capsule\Manager as DB;

class Cancel extends BaseCommand
{

    function processCommand()
    {
        switch ($this->user->status) {
            case UserStatusService::FEEDBACK:
                $this->triggerCommand(GameList::class);
                break;
            case UserStatusService::TRUEORFALSE:
            case UserStatusService::TRUTHORDARE:
            case UserStatusService::WORDS:

                $game_entity = GameEntity::where('user_id', $this->user->id)->where('game_id', $this->user->game_id)->where('status', 'NEW')->get();
                if ($game_entity[0]['score'] > 0 || $game_entity[0]['wrong'] > 0) {
                    $this->getBot()->sendMessage($this->user->chat_id, $this->text['rightAnswers'] . strval($game_entity[0]['score']) . "\n" . $this->text['wrongAnswers'] . strval($game_entity[0]['wrong']));
                    $game_entity->status = 'DONE';
                    $game_entity->save();
                } else {
                    DB::raw('DELETE FROM score WHERE user_id = ' . $this->user->id . ' AND game_id = ' . $this->user->game_id.' AND status = "NEW"');
                }
                break;
        }
    }

}