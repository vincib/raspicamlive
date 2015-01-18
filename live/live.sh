#!/bin/sh

php stream.php | 
ffmpeg -i - -r 12 -s 640x360 -vb 1000k -f ogg - | 
oggfwd -p -n "My RaspberryPi Stream" stream.server.com 80 mySecretIceCastStreamingPassword /test 
