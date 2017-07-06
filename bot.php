<?php

/**
 * Twitch Bot
 */
namespace TwitchBot;

date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';
$config = json_decode(file_get_contents(__DIR__ . '/config.json'));

$connect = new IrcConnect(
        $config->twitch->irc_address,
        $config->twitch->port,
        $config->twitch->user,
        $config->twitch->channels,
        $config->twitch->oauth
);
$connect->launch($connect->connect());