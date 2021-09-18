<?php

namespace App\Services;

use App\Models\Game;
use GuzzleHttp\Client;

/**
 * Class responsible for making requests to Quiz API
 */
class QuizApiService
{

    const API_URL = 'https://engine.lifeis.porn/api/';

    /**
     * @var Client
     */
    private $guzzle;

    public function __construct()
    {
        $this->guzzle = new Client();
    }

    public function call(int $gameId, array $params = []): array
    {
        $response = $this->guzzle->get($this->getApiUrl(Game::find($gameId)->api_path), [
            'query' => $params,
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    protected function getApiUrl(string $method): string
    {
        return self::API_URL . $method;
    }

}
