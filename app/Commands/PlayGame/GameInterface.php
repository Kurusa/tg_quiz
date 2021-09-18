<?php

namespace App\Commands\PlayGame;

interface GameInterface {

	function sendMsg();
	function sendStatus();

	function checkDie();
    function checkReply();

    function setAnswer();
    function setScore();

    function createGameEntity();
}