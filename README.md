Raspicam Live
=============

License: GPLv3+ by Benjamin Sonntag <benjamin@sonntag.fr>

This is raspicam live, a set of scripts to transform your raspberry pi + camera into a proper conference filming / saving / live streaming platform.

Installation instructions
-------------------------

    apt-get install apache2-mpm-prefork libapache2-mod-php5 php5-cli sudo screen avahi-daemon ffmpeg 

Change the default vhost to point to /var/www/www

    cd /var/www/
    git clone https://github.com/vincib/raspicamlive.git .

edit config.php if needed 

ensure you have /tmp mounted as a ramdrive, and a big storage (usb key or harddrive) mounted on /mnt

copy the sudoers file : 

    cp /var/www/sudoers.raspicamlive /etc/sudoers.d/

launch the stream daemon :

    screen -d -m /var/www/sh/streamer_daemon

(you can add it to /etc/rc.local)

use a browser to go to this (or your raspberry hostname)

    http://raspilive01.local/ 

enjoy!



