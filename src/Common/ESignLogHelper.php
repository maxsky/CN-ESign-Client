<?php

namespace MaxSky\ESign\Common;

/**
 * ESign 日志类
 *
 * @author   澄泓
 * @date     2022/08/18 15:10
 *
 * @modifier Max Sky
 * @date     2024/04/28 4:44
 */
class ESignLogHelper {

    /**
     * @param array|object|string $msg
     * @param string              $location Log location
     *
     * @return void
     */
    public static function writeLog($msg, string $location) {
        if (is_array($msg) || is_object($msg)) {
            $text = json_encode($msg);
        } else {
            $text = $msg;
        }

        file_put_contents(
            $location . DIRECTORY_SEPARATOR
            . date('Y-m-d') . '.log', date('Y-m-d H:i:s') . '  ' . $text . "\r\n",
            FILE_APPEND
        );
    }

    /**
     * @param array|object|string $msg
     *
     * @return void
     */
    public static function printMsg($msg) {
        echo "<pre/>";

        if (is_array($msg) || is_object($msg)) {
            var_dump($msg);
        } else {
            echo $msg . "\n";
        }
    }
}
