<?php

try {

    require_once "../common.php";
    $controller = new Controller($logger, $recorder);
    $controller->run();
    
} catch (\Exception $exception) {
    
    die( "FATAL ERROR<br><h1>".$exception->getMessage()."</h1>".nl2br($exception->getTraceAsString()) );
    
}
