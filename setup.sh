#!/usr/bin/env bash

VERSION=1.3
TITLE="Twitch Bot $VERSION"

function installPhpOrDie() {
    if (dialog --title "$TITLE - PHP Checks" --yes-button "Yes" --no-button "No"  --yesno "Do you want to install PHP and PHP-CURL ?" 10 78); then
        clear
        apt-get update
        apt-get install -y php-cli php-curl
    else
        dialog --title "$TITLE" --msgbox "PHP is required to use this bot, thanks." 10 78
    fi
}

# Step 1
dialog --title "$TITLE" --msgbox "
Welcome to the installer, it checks if your system passes all requirements and configures the bot for you.

Choose Ok to begin." 10 78


# Step 2
PHPVERSION=$(php --version 2> /dev/null | head -n 1 | cut -d " " -f 2 | cut -c 1,3);
if [ ${PHPVERSION} < 56 ] || [ -z ${PHPVERSION} ]; then
    installPhpOrDie
fi

# Step 3
dialog --title "$TITLE" --msgbox "
The next step will download composer on getcomposer.org and install it locally

Choose Ok to continue and wait." 10 78

wget https://getcomposer.org/installer -O composer-setup.php &> /dev/null
php composer-setup.php &> /dev/null
rm composer-setup.php


# Step 4
dialog --title "$TITLE" --msgbox "
To finish, launch composer update by pressing Ok on this step

Choose Ok to continue." 10 78
php composer.phar update &> /dev/null

# Step 5
dialog --title "$TITLE" --msgbox "It' done ! Installation ended successfully. You can launch the bot by typing: bash launch.sh in your terminal" 10 78
