#! /bin/bash

## Easy install 
sudo -s
cd /usr/local/lib
git clone -depth 1 https://github.com/vincib/raspicamlive.git raspicamlive
cd raspicamlive
./install.sh