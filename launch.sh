#!/usr/bin/env bash

OPTION=$(dialog --title "Twitch Bot" --menu "What do you want ?" 10 78 3 \
"1" "Launch the bot" \
"2" "Launch in screen" \
"3" "Dump autoload" 3>&1 1>&2 2>&3)

STATUS=$?
if [ $STATUS = 0 ]; then
    case $OPTION in
    1)
      php bot.php
      ;;
    2)
      screen -Sdm twitch-bot php bot.php
      dialog --title "Twitch Bot" --msgbox "You can re-attach the screen by typing: screen -r twitch-bot" 10 78
      ;;
    3)
      php composer.phar dumpautoload &> /dev/null
      ;;
    *)
      echo "exit"
      ;;
    esac
fi

clear