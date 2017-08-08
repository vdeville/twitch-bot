<?php

/**
 * Twitch Bot
 */
namespace TwitchBot;

date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';
$config_path = __DIR__ . '/config.json';
$config = json_decode(file_get_contents($config_path), true);

$connect = new IrcConnect($config);
$connect->launch($connect->connect());