#!/bin/sh

while true
do 
    RSYNC_PASSWORD=pi rsync pi@raspilive01.local::video/ video/ -rv --size-only --delete 
    sleep 2 
done 
