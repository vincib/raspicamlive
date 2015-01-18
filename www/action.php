<?php

require_once("common.php");

switch ($_REQUEST["action"]) {

  // Return a json telling when was taken the last capture and if we are recording (for display purpose on the web page)
case "updatecapture":
  header("Content-Type: application/json");
  $result=array(
			 "isrecording" => isrecording(),
			 "lastcaptime" => @filemtime(CAPTURE_FILE),
			 "currentproject" => @file_get_contents(RECORDING_FOLDER)
		);
  $storage=getStorageSpace(); // size used avail in MB
  if (count($storage)==3) {
    $result["storagesize"]=$storage[0];
    $result["storageused"]=$storage[1];
    $result["storageavail"]=$storage[2];
  }
  echo json_encode($result);
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
  
}
