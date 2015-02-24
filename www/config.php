<?php

// RaspiCam Live configuration file

// Where do we mount the storage, and store the video (fs shall support hardlinks!)
define("STORAGEPATH","/mnt");
// Where do we have available storage? 
define("MOUNTDRIVE","/dev/sda1");
// Where is the www folder
define("APP_ROOT",__DIR__);

// rsync login and password for *stream* share
$rsync_login="pi";
$rsync_password="surdeux";


// Where do we store the PID
define("RASPIVID_PID","/run/raspivid.pid");
// Where do we store the capture file
define("CAPTURE_FILE","/tmp/capture.jpg");
// Where do we store capture settings
define("CAPTURE_SETTINGS","/tmp/settings.json");
// Where do we store current recording folder
define("RECORDING_FOLDER","/mnt/current");
// Where do we store current recording folder
define("FILE_CURRRENT_RECORDING_FOLDER","/mnt/current_recording");
