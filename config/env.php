<?php
// Reads settings from the .env file in the project root.
if (!function_exists('env')) {
    function env($key, $default = '')
    {
        static $values = null;

        if ($values === null) {
            $values = [];
            $path = dirname(__DIR__) . '/.env';

            if (is_readable($path)) {
                foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $line = trim($line);
                    if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                        continue;
                    }
                    [$name, $value] = explode('=', $line, 2);
                    $values[trim($name)] = trim(trim($value), "\"'");
                }
            }
        }

        if (array_key_exists($key, $values)) {
            return $values[$key];
        }

        $fromServer = getenv($key);
        return $fromServer !== false ? $fromServer : $default;
    }
}

ini_set('display_errors', env('APP_DEBUG') === 'true' ? '1' : '0');
ini_set('log_errors', '1');
