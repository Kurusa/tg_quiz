<?php
return [
    \App\Services\Status\UserStatusService::FEEDBACK => \App\Commands\Feedback::class,
    \App\Services\Status\UserStatusService::WORDS => \App\Commands\PlayGame\Words::class,
    'trueOrFalse' => \App\Commands\PlayGame\TrueOrFalse::class,
    'trueOrDare' => \App\Commands\PlayGame\TruthOrDare::class,
];