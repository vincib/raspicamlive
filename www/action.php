<?php

require_once("common.php");

class AjaxResponse {
    
    static function send($options ){

        // Attempts to retrieve code
        if (isset($options["code"]) && ! is_null($options["code"])) {
            $code = $options["code"];
        } else {
            $code = 0;
        }

        // Attempts to retrieve message|
        if (isset($options["message"]) && ! is_null($options["message"])) {
            $message = $options["message"];
        } else {
            $message = "OK";
        }

        // Attempts to retrieve payload
        if (isset($options["payload"]) && is_array($options["payload"])) {
            $payload = $options["payload"];
        } else {
            $payload = array();
        }

        die (json_encode(array(
            "code" => $code,
            "message" => $message,
            "payload" => $payload

        )));
    }
}
switch ($_REQUEST["action"]) {

  // Return a json telling when was taken the last capture and if we are recording (for display purpose on the web page)
case "updatecapture":
  header("Content-Type: application/json");
    
  $payload=array(
    "isrecording" => isRecording(),
    "lastcaptime" => @filemtime(CAPTURE_FILE),
    "currentproject" => @file_get_contents(FILE_CURRRENT_RECORDING_FOLDER)
  );
  $storage=getStorageSpace(); // size used avail in MB
  if (count($storage)==3) {
    $payload["storagesize"]=$storage[0];
    $payload["storageused"]=$storage[1];
    $payload["storageavail"]=$storage[2];
  }
  
  die( AjaxResponse::send(array(
      "payload" => $payload
  )));
  break;

  // Start the recording on the PI
case "startrecording":
  header("Content-Type: text/plain; charset=UTF-8");
  switch (startRecording()) {
  case ERR_OK:
    echo "Recording started";
    break;
  case ERR_ALREADY:
    echo "Already recording";
    break;
  case ERR_FATAL:
    echo "Error launching the recording";
    break;
  }
  break;
  
  // Stop the recording on the PI
case "stoprecording": 
  header("Content-Type: text/plain; charset=UTF-8");
  switch (stopRecording()) {
  case ERR_OK:
    echo "Recording stopped";
    break;
  case ERR_ALREADY:
    echo "I am not recording";
    break;
  case ERR_FATAL:
    echo "Error stopping the recording";
    break;
  }
  break;

case "savetitle":
  if (isset($_REQUEST["rectitle"])) {
    setProjectMetadata(array("title"=>$_REQUEST["rectitle"]));
  }
  echo "Title saved";
  break;

case "shutdown":
  if (isRecording()) {
    stopRecording();
    sleep(5);
  }
  exec("sudo /sbin/poweroff");
  echo "Shutdown in progress";
  break;

case "reboot":
  if (isRecording()) {
    stopRecording();
    sleep(5);
  }
  exec("sudo /sbin/reboot");
  echo "Reboot in progress";
  break;
}

