# Twitch bot in PHP

## Users

### Setup the bot
1) Launch setup.sh by typing `bash setup.sh`
2) Edit `bot.php` file and enter your oAuth token, bot name and your channel

### Launch the bot
1) Launch `launch.sh` by typing `bash launch.sh`


## Developpers 

I write this bot to have clean PHP base to create custom Twitch bot in PHP.
This project use modules system with hooks to add your own features. You can see examples of modules in `/modules` folder.

### Available hook:
* onConnect (Execute when bot connect to the channel)
* onMessage (Execute when new message come in the channel)
* onPing (Execute when someone mention the bot (Ex: @YourBot))
* onPong (Execute every 5 minutes when Twitch send PING status)
* onUsernotice (Execute when user subscribe for example)

### Write module

1) Create folder named with the name of your module in `/modules` (Ex: `/modules/responder`)

2) Create php class named with the name of your module (Ex: `/modules/responder/responder.php`)

3) Define class and use TwichtBot module trait like this:
```php
<?php

/**
 * Class Responder
 */
class Responder {

    use \TwitchBot\Module;

    /**
     * Responder constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->client = $client;
        $this->infos = $infos;
    }

    /**
     * @param \TwitchBot\Message $message
     */
    public function onPing($message)
    {
        $this->getClient()->sendMessage('You ping me @' . $message->getUsername() . ' ?! What do you want ?');
    }
}
```

4) Develop your feature using available hook. The function name is `onNameOfTheHook()` (Ex: `onPing($data)`)

5) Refer to the php trait (`/src/class/Module.php`) for informations about functions and parameters

6) Update autoload using this command: `composer dumpautoload`

7) Start your bot in screen or tmux for example ! `php bot.php`


## Examples:
https://www.twitch.tv/warths (with custom modules and default)
