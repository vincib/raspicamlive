<?php

require_once("common.php");
header("Content-Type: image/jpeg");

if (is_file(CAPTURE_FILE)) {
  readfile(CAPTURE_FILE);
} else {
  readfile("assets/mire.jpg");
}

