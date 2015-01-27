<?php

$app_path = realpath(__DIR__."/../");
header("Content-Type: video/x-flv");

passthru("/usr/bin/php ${app_path}/www/stream.php|ffmpeg -f mpegts -i - -acodec copy -vcodec copy -f flv -");
