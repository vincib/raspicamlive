<?php

/**
 * Encapsulates the standard AJAX response object
 *
 * @author alban
 */
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