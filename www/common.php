<?php

require_once("config.php");

function __autoload($class_name) {
    $filename = APP_ROOT . "/lib/{$class_name}.php";
    if (!is_readable($filename)) {
        throw new \Exception("Could not load ${class_name}, file ${filename} does not exist or is not readable");
    }
    require_once $filename;
}

$logger = new Logger("raspicam.log", Logger::INFO, "/tmp/");
$recorder = new Recorder( $logger );