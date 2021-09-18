<?php

namespace App\Commands;

use App\Utils\Api;
use App\Models\User;
use App\Services\QuizApiService;
use TelegramBot\Api\{BotApi, Types\Update, Types\User as TelegramBotUser};

/**
 * Class BaseCommand
 * @package App\Commands
 */
abstract class BaseCommand
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var TelegramBotUser $user
     */
    protected $botUser;

    protected $text;

    protected $russianAlphabet;

    protected $update;

    /**
     * @var QuizApiService
     */
    protected $quizApi;

    /**
     * @var BotApi $bot
     */
    private $bot;

    public function __construct(Update $update)
    {
        $this->update = $update;
        if ($update->getCallbackQuery()) {
            $this->botUser = $update->getCallbackQuery()->getFrom();
        } elseif ($update->getMessage()) {
            $this->botUser = $update->getMessage()->getFrom();
        } else {
            throw new \Exception('cant get telegram user data');
        }
    }

    public function handle()
    {
        $this->user = User::firstOrCreate([
            'chat_id' => $this->botUser->getId()
        ], [
            'user_name' => $this->botUser->getUsername(),
            'first_name' => $this->botUser->getFirstName(),
        ]);

        $this->russianAlphabet = include_once(__DIR__ . '/../config/russian_alphabet.php');
        $this->text = include_once(__DIR__ . '/../config/bot.php');

        $this->processCommand();
    }

    /**
     * @return Api
     */
    public function getBot(): Api
    {
        if (!$this->bot) {
            $this->bot = new Api(env('TELEGRAM_BOT_TOKEN'));
        }

        return $this->bot;
    }

    /**
     * @return QuizApiService
     */
    public function getQuizApi(): QuizApiService
    {
        if (!$this->quizApi) {
            $this->quizApi = new QuizApiService();
        }

        return $this->quizApi;
    }

    /**
     * @param $class
     */
    function triggerCommand($class)
    {
        (new $class($this->update))->handle();
    }

    abstract function processCommand();

}