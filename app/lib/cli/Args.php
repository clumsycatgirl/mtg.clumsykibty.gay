<?php

namespace Lib\Cli;

class Args {
    /**
     * @var array Stores the parsed CLI arguments.
     */
    private static array $args = [];

    /**
     * Parses the command-line arguments and stores them in the static `$args` property.
     * It supports key-value pairs in the format `--key=value` or `--key value`, and positional arguments.
     * Keys without values are assigned `true`.
     *
     * @return void
     */
    public static function parse(): void {
        global $argv, $argc;

        $current_key = null;

        for ($i = 1; $i < $argc; $i++) {
            $arg = $argv[$i];

            if (substr($arg, 0, 2) === '--') {
                $equals_position = strpos($arg, '=');

                if ($equals_position !== false) {
                    // --key=value format
                    $key = substr($arg, 2, $equals_position - 2);
                    $value = substr($arg, $equals_position + 1);
                    self::$args[$key] = $value;
                } else {
                    // --key without value (set to true)
                    $current_key = substr($arg, 2);
                    self::$args[$current_key] = true;
                }
            } elseif ($current_key) {
                self::$args[$current_key] = $arg;
                $current_key = null;
            } else {
                self::$args[$arg] = true;
            }
        }
    }

    public static function first(): array {
        $key = array_keys(self::$args)[0];
        $value = self::$args[$key];
        return [$key, $value];
    }

    /**
     * Returns all parsed arguments as an associative array.
     *
     * @return array Parsed CLI arguments.
     */
    public static function all(): array {
        return self::$args;
    }

    /**
     * Retrieves a specific argument by key.
     *
     * @param string $key The key of the argument to retrieve.
     * @return string|array The value of the argument if it exists, or throws an error if the key does not exist.
     */
    public static function get(string $key): string|array {
        return self::$args[$key] ?? null;
    }

    /**
     * Checks if a specific argument exists in the parsed arguments.
     *
     * @param string $key The key to check for in the arguments.
     * @return bool Returns true if the argument exists, false otherwise.
     */
    public static function has(string $key): bool {
        return array_key_exists($key, self::$args);
    }
}
