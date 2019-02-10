#!/usr/bin/env bash

VERSION=1.5.0
TITLE="Twitch Bot $VERSION"
OPTION=$(dialog --title "$TITLE" --menu "What do you want ?" 10 78 3 \
"1" "Launch the bot" \
"2" "Launch in screen" \
"3" "Dump autoload" 3>&1 1>&2 2>&3)

STATUS=$?
if [ ${STATUS} = 0 ]; then
    case ${OPTION} in
    1)
      php bot.php
      ;;
    2)
      screen -Sdm twitch-bot php bot.php
      dialog --title "$TITLE" --msgbox "You can re-attach the screen by typing: screen -r twitch-bot" 10 78
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