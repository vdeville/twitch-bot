<?php

/**
 * Twitch Bot
 */

date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';

DEFINE('USER', '');
DEFINE('OAUTH', 'oauth:');
DEFINE('IRC_ADDRESS', 'irc.chat.twitch.tv');

$connect = new \TwitchBot\IrcConnect(IRC_ADDRESS, 6667, USER,'v_deville',OAUTH);
$connect->launch($connect->connect());