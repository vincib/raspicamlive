#! /bin/bash


#============================================================================================#
## Simple installer for the raspicam live
#============================================================================================#

## Performs checks

crash(){ echo $1; exit 1; }

# check_is_root
[ "root" == "$( whoami )" ] || crash "You must run this script as root. Exiting."

# check_has_net
ping -c 1 -W 1 google.com 2>/dev/null >/dev/null
RESULT=$?; 
[ 0 -eq $RESULT ] || crash "You must have internet access. Exiting."

# check_required_packages


## Include utilities and own library


. install/utilities.sh
. install/lib.sh
. install/variables.sh


## Configure aptitude

# Debian multimedia repository
# apt-get update

# required packages
# apt-get install apache2-mpm-prefork libapache2-mod-php5 php5-cli sudo screen avahi-daemon ffmpeg 

## Configure disk
PI_STORAGE="/mnt"
# read -p  "It is strongly advised to use an USB disk mounted in /mnt"
# check disk is mounted, readable, etc.


## Configure name
PI_NAME="raspilive01"
read -p "Please give your RaspberryPi network name (Default: raspilive01): " REPLY_PI_NAME

[ -z $REPLY_PI_NAME ] || PI_NAME=$REPLY_PI_NAME

## Configure sudoers
cp_check "$APP_PATH/etc/sudoers.d/sudoers.raspicamlive" /etc/sudoers.d/

## Configure avahi
cp_check "$APP_PATH/etc/avahi/avahi-daemon.conf" /etc/avahi/avahi-daemon.conf
service avahi-daemon restart

## Configure apache
# cp_check "$APP_PATH/etc/apache2/sites-enabled/raspicamlive" /etc/apache2/sites-enabled/raspicamlive
# service apache2 restart

## Configure daemon
# cp_check "$APP_PATH/sh/streamer_daemon" /etc/init.d
# update-rc.d streamer_daemon defaults
# /etc/init.d/streamer_daemon start

## Configure rsync


echo "Install completed. You can now try to reach the application on http://${PI_NAME}.local"




