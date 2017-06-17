#!/usr/bin/env bash


function installPhpOrDie() {
    if (dialog --title "PHP Checks" --yes-button "Yes" --no-button "No"  --yesno "Do you want to install PHP and PHP-CURL ?" 10 78); then
        clear
        apt-get update
        apt-get install -y php5-cli php5-curl
    else
        dialog --title "Twitch Bot" --msgbox "PHP is require to use this bot, thanks." 10 78
    fi
}

# Step 1
dialog --title "Twitch Bot" --msgbox "
Welcome to the installer, his check if you have all requirements and configure it's for you.

Choose Ok to begin." 10 78


# Step 2
PHPVERSION=$(php --version 2> /dev/null | head -n 1 | cut -d " " -f 2 | cut -c 1,3);
if [ $PHPVERSION < 56 ] || [ -z $PHPVERSION ]; then
    installPhpOrDie
fi

# Step 3
dialog --title "Twitch Bot" --msgbox "
Next step download file on getcomposer.org and install localy

Choose Ok to continue." 10 78

wget https://getcomposer.org/installer -O composer-setup.php &> /dev/null
php composer-setup.php &> /dev/null


# Step 4
dialog --title "Twitch Bot" --msgbox "
To finish, launch composer update by pressing Ok on this step

Choose Ok to continue." 10 78
php composer.phar update &> /dev/null

# Step 5
dialog --title "Twitch Bot" --msgbox "It' done ! Installation succesfully terminated. You can launch the bot by typing: bash launch.sh in your terminal" 10 78