<?php

namespace core\engine;


class Logger {

  function logger($message,$fileName)
    {
        $logPath = __DIR__ . '/logs';
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }

        $errorMsg = date('Y/m/d H:i:s') . ": $message" . PHP_EOL;
        error_log($errorMsg, 3, $logPath . '/'.$fileName.'.log');
    }

}
