#!/usr/bin/env bash

OPTION=$(dialog --title "Twitch Bot" --menu "What do you want ?" 10 78 2 \
"1" "Launch the bot" \
"2" "Dump autoload"  3>&1 1>&2 2>&3)

STATUS=$?
if [ $STATUS = 0 ]; then
    case $OPTION in
    1)
      php bot.php
      ;;
    2)
      php composer.phar dumpautoload &> /dev/null
      ;;
    *)
      echo "exit"
      ;;
    esac
fi

clear