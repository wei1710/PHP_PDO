<?php

/**
 * Logs the information it receives as a parameter to a log file in the log folder.
 * @param $info An undefined series of strings or arrays to log
 * @author  Arturo Mora-Rioja
 * @version 1.0.0, December 2022
 */

define('LOG_DIRECTORY', __DIR__ . '/../log'); 
define('LOG_FILE_NAME',  LOG_DIRECTORY . '/log' . date('Ymd') . '.htm');

function logText(string|array ...$info): void 
{
    // If the logging directory does not exist, it is created
    if (!is_dir(LOG_DIRECTORY)) {
        if (!mkdir(LOG_DIRECTORY)) {
            return;
        }
    }

    $text = '';
    if (!file_exists(LOG_FILE_NAME)) {
        $text .= '<pre>';
    }
    $text .= '--- ' . date('Y-m-d h:i:s A', time()) . ' ---<br>';

    // The name of the invoking file is displayed
    if (count($bt = debug_backtrace()) > 1) {
        $text .= 'FILE ' . $bt[1]['file'] . '<br>';
    };        
    
    foreach ($info as $pieceOfInfo) {            
        if (gettype($pieceOfInfo) === 'array') {
            $text .= print_r($pieceOfInfo, true);
        } else {
            $text .= $pieceOfInfo . '<br>';
        }
    }

    $logFile = fopen(LOG_FILE_NAME, 'a');
    fwrite($logFile, $text);
    fclose($logFile);
}