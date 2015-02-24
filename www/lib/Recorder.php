<?php

/**
 * Contains all the logic related to recording, files and metadata
 */
class Recorder {

    // Error codes
    const ERR_OK = 0;
    const ERR_ALREADY = 1;
    const ERR_FATAL = 2;

    /**
     * default video parameters:
     * @var array
     */
    protected $default_video_settings = array(
        "width" => "1280",
        "height" => "720",
        "fps" => "25", // number of images per second
        "videobitrate" => "8000", // in kbps
        "audiosource" => "", // alsa audio source ("" for no audio)
        "audiobitrate" => "128", // in kbps
    );

    /**
     *
     * @var string
     */
    protected $project = "";

    /**
     * 
     * @param type $filename
     */
    function fixPerms($filename) {
        chown($filename, "www-data");
        chmod($filename, 0666);
        return $filename;
    }

    /**
     * Lists all projects and returns their metadata
     * 
     * @return array
     */
    function getAllProjectsInfo() {
        $records = array();
        $d = opendir(STORAGEPATH);
        while (($c = readdir($d)) !== false) {
            $metadata_file = STORAGEPATH . "/" . $c . "/meta.json";
            if (preg_match("#^rec_#", $c) && is_file($metadata_file)) {
                $records[$c] = $this->jsonDecode($metadata_file);
            }
        }
        closedir($d);
        return $records;
    }

    /**
     * Return the recording folder name (make your own model here if needed
     * 
     * @return string
     */
    function getNewRecordingFolder() {
        // 20150118_173012
        return "rec_" . date("Ymd_His");
    }

    /**
     * 
     * @param string $project
     * @return boolean|array
     */
    function getProjectMetadata($project = "") {

        $this->getProjectName($project);
        
        if( !$this->project){
            return array();
        }

        // Files used to lock & write
        $lock_file = STORAGEPATH . "/" . $this->project . "/.meta.json.lock";
        $metadata_file = STORAGEPATH . "/" . $this->project . "/meta.json";

        // Put a lock
        $metadataFileHandle = fopen($lock_file, "ab");

        // Failed to lock
        if (!flock($metadataFileHandle, LOCK_EX)) {
            throw new \Exception("Could not put a lock on ${lock_file}.");
        }

        // Retrieve metadata
        $metadata = $this->jsonDecode($metadata_file, true);

        // Remove the lock and close the handle
        unlink($lock_file);
        fclose($metadataFileHandle);

        return $metadata;
    }

    /**
     * Gets project by parameter or by opening file on disk
     * 
     * @param string $name
     * @return string
     */
    function getProjectName($project = "") {

        // Attempt to read project from file if none provided
        if (!$project) {

            // Return internal value
            if ($this->project) {
                return $this->project;
            }

            // Attempt to read from file 
            if (!is_readable(FILE_CURRRENT_RECORDING_FOLDER)) {
                $this->fixPerms(FILE_CURRRENT_RECORDING_FOLDER);
            }
            $project = file_get_contents(FILE_CURRRENT_RECORDING_FOLDER);
        }

        // Failed to read project
        if (!$project) {
            return "";
        }

        // Set internal state and return it
        $this->project = $project;
        return $project;
    }

    /**
     * Return the video capture settings 
     * 
     * @return array
     * @throws \Exception
     */
    function getSettings() {

        // Attempt to set default settings if none available
        if (!is_file(CAPTURE_SETTINGS)) {
            setSettings();
        }

        if (!is_readable(CAPTURE_SETTINGS)) {
            throw new \Exception("You should try to delete " . CAPTURE_SETTINGS . ". Failed to read because: ");
        }

        if (!is_writable(CAPTURE_SETTINGS)) {
            $this->fixPerms(CAPTURE_SETTINGS);
            if (!is_writable(CAPTURE_SETTINGS)) {
                throw new \Exception("Cannot write to" . CAPTURE_SETTINGS . " please check users rights ");
            }
        }

        // Attempt to read the settings as array
        $settings = $this->jsonDecode(CAPTURE_SETTINGS);

        return $settings;
    }

    /**
     * 
     * Reads the storage space through bash command
     * 
     * @return array
     */
    function getStorageSpace() {
        $out = array();
        $result = array("?", "?", "?");
        exec("df -m |grep " . escapeshellarg("^" . MOUNTDRIVE), $out);
        if (isset($out[0])) {
            list(, $size, $used, $free) = preg_split('# +#', $out[0]);
            return array($size, $used, $free);
        }
        return $result;
    }

    /**
     * Checks recording is effective
     * 
     * @return boolean
     */
    function isRecording() {
        // are we recording?
        if (!is_file(RASPIVID_PID)) {
            return false;
        }
        if (!is_dir("/proc/" . intval(file_get_contents(RASPIVID_PID)))) {
            return false;
        }
        return true;
        // @todo : also add a test on the last .TS file and its size.
    }

    /**
     * Encapsulates JSON decoding
     * @param string $filename
     * @return array
     * @throws \Exception
     */
    function jsonDecode($filename, $allow_empty_file = FALSE) {

        // Fix permissions if necessary
        if (!is_readable($filename)) {
            $this->fixPerms($filename);
        }

        // Attempt to get content
        $content = file_get_contents($filename);

        // Fail, throw or return empty
        if (!$content) {
            if ($allow_empty_file != TRUE) {
                throw new \Exception("Failed to get content from ${filename}.");
            } else {
                return array();
            }
        }

        // Attempt to decode json
        $result = json_decode($content, true);

        // Check for error 
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception("Corrupted file " . CAPTURE_SETTINGS . ". Try deleting it to reset. Error code is: " . json_last_error());
        }
        return $result;
    }

    /**
     * 
     * @param array $meta
     * @param string $project
     * @return boolean
     */
    function setProjectMetadata($meta, $project = "") {

        // Retrieve metadata
        $metadata = $this->getProjectMetadata($project);

        // File to write to
        $metadata_file = STORAGEPATH . "/" . $this->project . "/meta.json";

        // Set metadata
        foreach ($meta as $key => $val) {
            $metadata[$key] = $val;
        }

        // Record metadata
        file_put_contents($metadata_file, json_encode($metadata));

        // Fix perms on the metadata
        $this->fixPerms($metadata_file);
    }
    
    /**
     * starts recording through shell scripts
     * 
     * @return int
     */
    function startRecording() {

        if ($this->isRecording()) {
            return self::ERR_ALREADY;
        }

        // Attempt to run bash start command
        exec("sudo " . APP_ROOT . "/sh/start_recording", $output, $return_var);

        // Failed ?
        if (0 !== $return_var) {
            // todo log ôutput
            return self::ERR_FATAL;
        }

        if ($this->isRecording()) {
            return self::ERR_OK;
        } else {
            return self::ERR_FATAL;
        }
    }
    
    /**
     * Store the settings in the setting file
     * $options array of settings (none is mandatory)
     * If any setting is unset, set the default value
     * 
     * @param type $options
     */
    function setSettings($options = array()) {
        $settings = array();
        foreach ($this->default_video_settings as $key => $val) {
            if (isset($options[$key]) && $options[$key]) {
                $settings[$key] = $options[$key];
            } else {
                $settings[$key] = $val;
            }
        }
        file_put_contents(CAPTURE_SETTINGS, json_encode($settings));
        $this->fixPerms(CAPTURE_SETTINGS);
    }


    /**
     * Stops recording through shell scripts
     * 
     * @return int
     */
    function stopRecording() {

        if (!$this->isRecording()) {
            return self::ERR_ALREADY;
        }

        // Attempt to run bash stop command
        exec("sudo " . APP_ROOT . "/sh/stop_recording", $output, $return_var);

        // Failed ?
        if (0 !== $return_var) {
            // todo log ôutput
            return self::ERR_FATAL;
        }

        // Wait for capture to stop
        sleep(1);

        // Failed ?
        if (!$this->isRecording()) {
            return self::ERR_OK;
        } else {
            return self::ERR_FATAL;
        }
    }


}
