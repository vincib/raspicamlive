<?php

require_once("config.php");
$app_path = realpath(__DIR__."/../");



// Error codes
define("ERR_OK",0);
define("ERR_ALREADY",1);
define("ERR_FATAL",2);

function isRecording() {
  // are we recording?
  if (!is_file(RASPIVID_PID)) return false;
  if (!is_dir("/proc/".intval(file_get_contents(RASPIVID_PID)))) return false;
  return true;
  // @todo : also add a test on the last .TS file and its size.
}

function startRecording() {
  global $app_path;
  if (isRecording()) {
    return ERR_ALREADY;
  }
  exec("sudo ${app_path}/sh/start_recording",$output,$return_var);
  
  if( 0 !== $return_var ){
      // todo log ôutput
      return ERR_FATAL;
  }
  
  if (isRecording()) {
    return ERR_OK;
  } else {
    return ERR_FATAL;
  }  
}

function stopRecording() {
  global $app_path;

  if (!isRecording()) {
    return ERR_ALREADY;
  }
  exec("sudo ${app_path}/sh/stop_recording",$output,$return_var);
  
  if( 0 !== $return_var ){
      // todo log ôutput
      return ERR_FATAL;
  }
  sleep(1);
  if (!isRecording()) {
    return ERR_OK;
  } else {
    return ERR_FATAL;
  }  
}

/**
 * Store the settings in the setting file
 * $options array of settings (none is mandatory)
 * If any setting is unset, set the default value
 */
function setSettings($options=array()) {
  global $default_video_settings;
  $settings=array();
  foreach($default_video_settings as $key=>$val)  {
    if (isset($options[$key]))  {
      $settings[$key]=$options[$key];
    } else {
      $settings[$key]=$val;
    }
  }
  file_put_contents(CAPTURE_SETTINGS,json_encode($settings));
  chown(CAPTURE_SETTINGS,"www-data");
  chmod(CAPTURE_SETTINGS,0666);
}

/**
 * Return the video capture settings as an array
 */
function getSettings() {
  if (!is_file(CAPTURE_SETTINGS)) setSettings();
  return json_decode(file_get_contents(CAPTURE_SETTINGS),true);
}

/**
 * Return the recording folder name (make your own model here if needed
 */
function getNewRecordingFolder() {
  // 20150118_173012
  return "rec_".date("Ymd_His");
}

function setProjectMetadata($meta,$project="") {
  if (!$project) $project=file_get_contents(FILE_CURRRENT_RECORDING_FOLDER);
  if (!$project) return false;
  $f=fopen(STORAGEPATH."/".$project."/.meta.json.lock","ab");
  if (flock($f, LOCK_EX)) {
    $metadata=@json_decode(@file_get_contents(STORAGEPATH."/".$project."/meta.json"),true);
    if (!$metadata) $metadata=array();
    foreach($meta as $key=>$val) {
      $metadata[$key]=$val;
    }
    file_put_contents(STORAGEPATH."/".$project."/meta.json",json_encode($metadata));
  } 
  unlink(STORAGEPATH."/".$project."/.meta.json.lock");
  fclose($f); 
  if (getmyuid()==0) { 
    chown(STORAGEPATH."/".$project."/meta.json","www-data"); 
    chmod(STORAGEPATH."/".$project,0777); 
  }
}

function getProjectMetadata($project="") {
  if (!$project) $project=file_get_contents(FILE_CURRRENT_RECORDING_FOLDER);
  if (!$project) return false;
  $f=fopen(STORAGEPATH."/".$project."/.meta.json.lock","ab");
  if (flock($f, LOCK_EX)) {
    $metadata=@json_decode(@file_get_contents(STORAGEPATH."/".$project."/meta.json"),true);
    if (!$metadata) $metadata=array();
  } 
  unlink(STORAGEPATH."/".$project."/.meta.json.lock");
  fclose($f); 
  return $metadata;
}

function getStorageSpace() {
  $out=array();
  exec("df -m |grep ".escapeshellarg("^".MOUNTDRIVE),$out);
  if (isset($out[0])) {
    list(,$size,$used,$free)=preg_split('# +#',$out[0]);
    return array($size,$used,$free);
  }
  return false;
}

