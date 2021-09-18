<?php

namespace App;

use App\Commands\GameList;
use App\Models\User;
use TelegramBot\Api\{Client, Types\Update};

class WebhookController
{

    protected $handlerClassName = false;

    public function handle()
    {
        $client = new Client(getenv('TELEGRAM_BOT_TOKEN'));

        $client->on(function (Update $update) {

            if ($update->getCallbackQuery()) {
                $this->processCallbackCommand($update);
            } elseif ($update->getMessage()) {
                $text = $update->getMessage()->getText();

                $this->processSlashCommand($text);
                $this->processKeyboardCommand($text);
                $this->processStatusCommand($update);
                $this->processGameCommand($text);
            }

            $this->checkHandlerClassName();
            (new $this->handlerClassName($update))->handle();
        }, function (Update $update) {
            return true;
        });

        $client->run();
    }

    protected function checkHandlerClassName()
    {
        if (!$this->handlerClassName) {
            $this->handlerClassName = GameList::class;
        }
    }

    protected function processCallbackCommand($update)
    {
        $config = include_once(__DIR__ . '/config/callback_commands.php');
        $action = \json_decode($update->getCallbackQuery()->getData(), true)['a'];

        if (isset($config[$action])) {
            $this->handlerClassName = $config[$action];
        }
    }

    protected function processSlashCommand($text)
    {
        if (strpos($text, '/') === 0) {
            $handlers = include_once(__DIR__ . '/config/slash_commands.php');
            $this->handlerClassName = $handlers[$text];
        }
    }

    protected function processKeyboardCommand($text)
    {
        if (!$this->handlerClassName) {
            $config = include_once(__DIR__ . '/config/bot.php');
            $translations = \array_flip($config);
            if (isset($translations[$text])) {
                $key = $translations[$text];
                $handlers = include_once(__DIR__ . '/config/keyboard_commands.php');
                $this->handlerClassName = $handlers[$key];
            }
        }
    }

    protected function processStatusCommand($update)
    {
        if (!$this->handlerClassName) {
            $handlers = include_once(__DIR__ . '/config/status_commands.php');
            $user = User::where('chat_id', $update->getMessage()->getFrom()->getId())->first();
            $this->handlerClassName = $handlers[$user->status];
        }
    }

    protected function processGameCommand($text)
    {
        if (!$this->handlerClassName) {
            $handlers = include_once(__DIR__ . '/config/game_commands.php');
            $this->handlerClassName = $handlers[$text];
        }
    }

}