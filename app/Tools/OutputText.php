<?php

namespace Ushahidi\App\Tools;

class OutputText
{
    private static $text_color = [
        'success' => '0;32',
        'error' => '0;31',
        'warn' => '1;33',
        'info' => '1;36'
    ];
    // Returns warning
    public static function warn($text)
    {
        $output = "\033[" . self::$text_color['warn'] . "m";
        // Add string and end warning color
        $output .=  $text . "\033[0m" . PHP_EOL;
        return $output;
    }

    // Returns warning
    public static function error($text)
    {
        $output = "\033[" . self::$text_color['error'] . "m";
        // Add string and end warning color
        $output .=  $text . "\033[0m" . PHP_EOL;
        return $output;
    }

    // Returns warning
    public static function success($text)
    {
        $output = "\033[" . self::$text_color['success'] . "m";
        // Add string and end warning color
        $output .=  $text . "\033[0m" . PHP_EOL;
        return $output;
    }

    // Returns warning
    public static function info($text)
    {
        $output = "\033[" . self::$text_color['info'] . "m";
        // Add string and end warning color
        $output .=  $text . "\033[0m" . PHP_EOL;
        return $output;
    }
}
