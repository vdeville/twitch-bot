<?php

/**
 * Twitch Bot
 */
namespace TwitchBot;

date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';

DEFINE('USER', '');
DEFINE('OAUTH', 'oauth:');
DEFINE('IRC_ADDRESS', 'irc.chat.twitch.tv');
DEFINE('PORT', 6667);
DEFINE('CHANNEL', 'v_deville');

$connect = new IrcConnect(IRC_ADDRESS, PORT, USER,CHANNEL,OAUTH);
$connect->launch($connect->connect());