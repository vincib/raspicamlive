Raspicam Live
=============

License: GPLv3+ by Benjamin Sonntag <benjamin@sonntag.fr>

This is raspicam live, a set of scripts to transform your raspberry pi + camera into a proper conference filming / saving / live streaming platform.

Requisites
-------------------------

# A Raspberry Pi + Camera Module 

# Internet access for install and local network when filming

# An USB stick or disk to save your videos (mandatory)

# A laptop for streaming ogg 

# An Icecast or any other streaming server 


Installation instructions
-------------------------

Become root on your RaspberryPi, go the directory and clone it

    sudo -s
    cd /usr/local/lib
    git clone -depth 1 https://github.com/vincib/raspicamlive.git raspicamlive
    cd raspicamlive
    ./install.sh

Alternatively, if you're confident enough, you can just

    curl https://raw.githubusercontent.com/vincib/raspicamlive/master/download.sh | sh

At this point, the script should handle everything for you once you provide

*   Raspberry pi name (ex: raspilivecam01) to access the Pi on http://raspilivecam01.local






Old installation instructions (deprecated, kept as reference for now)
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

Screenshots
-----------

![](https://github.com/vincib/raspicamlive/blob/master/doc/cap1.png)

![](https://github.com/vincib/raspicamlive/blob/master/doc/cap2.png)


