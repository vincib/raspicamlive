<?php

// RaspiCam Live configuration file

// Where do we mount the storage, and store the video (fs shall support hardlinks!)
define("STORAGEPATH","/mnt");
// Where do we have available storage? 
define("MOUNTDRIVE","/dev/sda1");

// rsync login and password for *stream* share
$rsync_login="pi";
$rsync_password="surdeux";

// default video parameters:
$default_video_settings=array(
	"width" => "1280",
	"height" => "720",
	"fps" => "25", // number of images per second
	"videobitrate" => "8000", // in kbps
	"audiosource" => "hw:1", // alsa audio source ("" for no audio)
	"audiobitrate" => "128", // in kbps
);

// Where do we store the PID
define("RASPIVID_PID","/run/raspivid.pid");
// Where do we store the capture file
define("CAPTURE_FILE","/tmp/capture.jpg");
// Where do we store capture settings
define("CAPTURE_SETTINGS","/tmp/settings.json");
// Where do we store current recording folder
define("RECORDING_FOLDER","/mnt/current");
