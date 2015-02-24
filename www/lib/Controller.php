<?php

/**
 * Web controller
 */
class Controller {

    /**
     *
     * @var Recorder
     */
    protected $recorder;

    /**
     * 
     */
    function __construct() {
        $this->layout = "default.php";
        $this->recorder = new Recorder();
    }

    /**
     * 
     */
    function error() {
        $this->render("error");
        die();
    }

    /**
     * main / router
     */
    function run() {
        try {
            $action = trim($_REQUEST["action"]);
            switch ($action) {

                // Return a json telling when was taken the last capture and if we are recording (for display purpose on the web page)
                case "updatecapture":
                    header("Content-Type: application/json");

                    $last_cap_time = ( is_file(CAPTURE_FILE) ? filemtime(CAPTURE_FILE) : "0");
                    $current_project = $this->recorder->getProjectName();

                    $payload = array(
                        "isrecording" => $this->recorder->isRecording(),
                        "lastcaptime" => $last_cap_time,
                        "currentproject" => $current_project
                    );
                    $storage = $this->recorder->getStorageSpace(); // size used avail in MB
                    $payload["storagesize"] = $storage[0];
                    $payload["storageused"] = $storage[1];
                    $payload["storageavail"] = $storage[2];

                    $this->ajax(array(
                        "payload" => $payload
                    ));
                    break;

                // Start the recording on the PI
                case "startrecording":
                    header("Content-Type: text/plain; charset=UTF-8");
                    $recording_status = $this->recorder->startRecording();
                    switch ($recording_status) {
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
                    $recording_status = $this->recorder->stopRecording();
                    switch ($recording_status) {
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
                        $this->recorder->setProjectMetadata(array("title" => $_REQUEST["rectitle"]));
                    }
                    echo "Title saved";
                    break;

                // Reboot / Shutdown the raspi
                case "shutdown":
                case "reboot":
                    if ( $this->recorder->isRecording() ) {
                        $this->recorder->stopRecording();
                        sleep(5);
                    }
                    if ("shutdown" == $action) {
                        $exec = "sudo /sbin/poweroff";
                        $message = "Shutdown in progress";
                    } else {
                        $exec = "sudo /sbin/reboot";
                        $message = "Reboot in progress";
                    }
                    exec($exec);
                    echo $message;
                    break;
                    
                // Get storage info
                case "storage":
                    $records = $this->recorder->getAllProjectsInfo();
                    
                    $this->render("storage", array(
                        "records" => $records
                    ));
                    break;
                
                // Get / set settings info
                case "settings":

                    $fields = array("widthheight" => "Video width and height",
                        "fps" => "Number of images per second",
                        "audiosource" => "Audio peripheral (may be empty for no audio track)",
                        "videobitrate" => "Video bitrate (in kbps)",
                        "audiobitrate" => "Audio bitrate (in kpbs)",
                    );
                    if (count($_POST) > 1) {
                        $settings = array();
                        foreach ($fields as $k => $v)
                            $settings[$k] = $_POST[$k];
                        list($settings["width"], $settings["height"]) = explode("x", $settings["widthheight"]);
                        unset($settings["widthheight"]);
                        $this->recorder->setSettings($settings);
                    }
                    $settings = $this->recorder->getSettings();
                    $settings["widthheight"] = $settings["width"] . "x" . $settings["height"];
                    $this->render("settings", array(
                        "aaudiobitrate" => array("64", "96", "128", "192", "256"),
                        "aaudiosource" => array("", "hw:1"),
                        "afps" => array("10", "12", "20", "25", "30", "50"),
                        "avideobitrate" => array("400", "500", "800", "1000", "2000", "4000", "8000", "9000", "10000", "15000", "20000"),
                        "awidthheight" => array("640x480", "800x600", "1024x768", "640x360", "1280x720", "1920x1080"),
                        "settings" => $settings,
                        "fields" => $fields
                    ));
                    break;

                // Get last image
                case "lastcap":
                    header("Content-Type: image/jpeg");

                    if (is_file(CAPTURE_FILE)) {
                        readfile(CAPTURE_FILE);
                    } else {
                        readfile("assets/mire.jpg");
                    }

                    break;
                    
                // Default / home
                case "home":
                default:
                    $metadata = $this->recorder->getProjectMetadata();
                    $this->render('home', array("metadata" => $metadata));
                    break;
            }
        } catch (\Exception $exception) {
            die("Fatal Error : " . $exception->getMessage());
        }
    }

    /**
     * 
     * @param string $view
     * @param array $variables
     * @throws \Exception
     */
    function render($view, $variables = array()) {

        ob_start();
        $filename = APP_ROOT . "/views/scripts/${view}.php";
        if (!is_readable($filename)) {
            throw new \Exception("Could not render view ${view}, ${filename} does not exist or is not readable.");
        }
        extract($variables, EXTR_SKIP); // Extract the variables to a local namespace
        include($filename);
        $__action_output = ob_get_contents();
        ob_end_clean();
        if ($this->noLayout) {
            die($__action_output);
        }
        $layout = APP_ROOT . "/views/layout/" . $this->layout;
        if (!is_readable($layout)) {
            throw new \Exception("Could not render layout, ${layout} does not exist or is not readable.");
        }
        include $layout;
    }

    /**
     * 
     * @param array $response
     */
    function ajax(array $response) {
        AjaxResponse::send($response);
    }

}
