<?php

header("Content-Type: video/x-flv");

passthru("/var/www/www/stream.php|ffmpeg -f mpegts -i - -acodec copy -vcodec copy -f flv -");
