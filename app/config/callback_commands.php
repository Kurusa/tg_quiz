<?php
return [
    'gameMenu' => \App\Commands\GameMenu::class,
    'playAlone' => \App\Commands\PlayGame\TriggerGameActions::class,
    'friends' => \App\Commands\PlayGame\TriggerGameActions::class,
    'stat' => \App\Commands\PlayGame\TriggerGameActions::class,
    'wtype' => \App\Commands\PlayGame\Words::class,
    'le' => \App\Commands\PlayGame\Words::class,
    'wAns' => \App\Commands\PlayGame\WordsAnswer::class,
    'end_prev_game' => \App\Commands\EndPrevGame::class,
];