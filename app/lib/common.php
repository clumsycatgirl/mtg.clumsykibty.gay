<?php

if (!function_exists('assets')) {
    /**
     * Returns the full path to an asset.
     *
     * This function checks if the specified asset exists in the application assets directory.
     * If not, it checks the library assets directory. If the asset is found, it returns the path,
     * otherwise, it returns the input URL.
     *
     * @param string $url The relative URL of the asset.
     *
     * @return string The full path to the asset if it exists, otherwise the input URL.
     */
    function assets(string $url = ''): string {
        $path = array_reduce(
            [app_assets_url . $url, lib_assets_url . $url],
            fn(?string $result, string $path): ?string => file_exists($path) ? $path : $result,
            null
        );

        return $path === null ? $url : str_replace(fc_url, '/', $path);
    }
}

if (!function_exists('base_url')) {
    /**
     * Returns the base URL of the application.
     *
     * This function constructs the base URL of the application using the server's protocol
     * and name. It can also append a relative URL to the base URL if provided.
     *
     * @param string $url The relative URL to append to the base URL.
     *
     * @return string The full base URL, optionally with the relative URL appended.
     */
    function base_url(string $url = ''): string {
        $base_url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . '/';
        return "$base_url$url";
    }
}

if (!function_exists('sanitize_input')) {
    /**
     * Sanitizes input by trimming whitespace and converting special characters to HTML entities.
     *
     * This function takes a string or an array of strings as input. If the input is an array,
     * it recursively sanitizes each element of the array. If the input is a string, it trims
     * whitespace and converts special characters to their HTML entity equivalents to protect
     * against XSS attacks.
     *
     * @param string|array $input The input to sanitize, either a string or an array of strings.
     *
     * @return array|string The sanitized input, either a string or an array of strings.
     */
    function sanitize_input(string|array $input): array|string {
        return is_array($input)
            ? array_map('sanitize_input', $input)
            : htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('error_handler')) {
    function error_handler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno) || $errno === E_DEPRECATED || $errno === E_USER_DEPRECATED)
            return;

        log_error("\{$errno\} at $errfile:$errline - $errstr");
    }
}

if (!function_exists('log_error') && !function_exists('log_info') && !function_exists('log_warn') && !function_exists('log_nlcr') && !function_exists('log_debug')) {
    /**
     * Logs a message to the log file.
     *
     * This function appends a message prefixed with a log level and timestamp to the log file.
     *
     * @param string $level The log level (e.g., ERROR, INFO, WARN, DEBUG).
     * @param string $message The message to log.
     *
     * @return void
     */
    function log_message(string $level, string $message): void {
        $log_file = get_log_file_name();
        create_log_file_if_not_exists($log_file);
        $log_message = "[$level] " . date('Y-m-d H:i:s') . ": " . $message;
        if (app_environment === 'development')
            $log_message .= PHP_EOL;
        error_log($log_message, 3, $log_file);
        if (is_cli) {
            echo "[$level] " . date('Y-m-d H:i:s') . ": " . $message . PHP_EOL;
        }
    }

    /**
     * Logs a newline character to the log file.
     *
     * This function appends a newline character to the log file specified by 'get_log_file_name()'.
     *
     * @return void
     */
    function log_nlcr(): void {
        $log_file = get_log_file_name();
        create_log_file_if_not_exists($log_file);
        error_log(PHP_EOL, 3, $log_file);
    }

    /**
     * Logs an error message to the log file.
     *
     * @param string $message The error message to log.
     *
     * @return void
     */
    function log_error(string $message): void {
        log_message('ERROR', $message);
    }

    /**
     * Logs an information message to the log file.
     *
     * @param string $message The information message to log.
     *
     * @return void
     */
    function log_info(string $message): void {
        log_message('INFO', $message);
    }

    /**
     * Logs a warning message to the log file.
     *
     * @param string $message The warning message to log.
     *
     * @return void
     */
    function log_warn(string $message): void {
        log_message('WARN', $message);
    }

    /**
     * Logs a debug message to the log file.
     *
     * @param string $message The debug message to log.
     *
     * @return void
     */
    function log_debug(string $message): void {
        log_message('DEBUG', $message);
    }

    /**
     * Creates the log file if it does not exist.
     *
     * This function checks if the log file specified by $log_file exists. If not, it creates
     * the necessary directory structure and touches the file.
     *
     * @param string $log_file The path to the log file.
     *
     * @return void
     */
    function create_log_file_if_not_exists(string $log_file): void {
        if (!file_exists($log_file)) {
            $log_directory = dirname($log_file);
            if (!is_dir($log_directory)) {
                mkdir($log_directory, 0777, true);
            }
            touch($log_file);
        }
    }

    /**
     * Returns the log file name based on the current date.
     *
     * This function returns the full path to the log file, which includes the current date.
     *
     * @return string The log file name.
     */
    function get_log_file_name(): string {
        return app_url . '.logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_logs.log';
    }
}


if (!function_exists('chash')) {
    /**
     * Computes the hash of a value.
     *
     * This function computes the hash of the provided value.
     *
     * @param mixed $value The value to hash.
     * @param bool $binary Whether to return the binary representation of the hash (default is false).
     *
     * @return string The hash of the value.
     */
    function chash(mixed $value, bool $binary = false): string {
        return md5($value, $binary);
    }

    /**
     * Checks if a hashed value matches a given value after hashing.
     *
     * This function hashes the provided value and compares it with the hashed value.
     *
     * @param string $hashed The hashed value to compare against.
     * @param string $value The value to hash and compare.
     * @param bool $binary Whether to use binary comparison (default is false).
     *
     * @return bool True if the hashed value matches the hashed result of the given value, false otherwise.
     */
    function check_chash(string $hashed, string $value, bool $binary = false): bool {
        return chash($value, $binary) === $hashed;
    }
}

if (!function_exists('encrypt') && !function_exists('decrypt')) {
    /**
     * URL to the encryption key file.
     */
    define('app_enckey_url', app_cfg_url . '.enc-key');

    /**
     * Cipher method used for encryption.
     */
    define('app_cipher', 'aes-128-cbc');

    // Generate a new encryption key if the key file does not exist
    if (!file_exists(app_enckey_url)) {
        log_nlcr();
        $msg = 'Generating new OpenSSL encryption key (128-bit)';
        $msg .= "\n\t\tUrl: " . app_enckey_url;
        log_info($msg);
        $key = openssl_random_pseudo_bytes(16);
        file_put_contents(app_enckey_url, $key);
    }

    /**
     * Encrypts data using app_cipher encryption.
     *
     * This function encrypts the provided data using the encryption key
     * stored in the file defined by 'app_enckey_url'.
     *
     * @param string $data The data to encrypt.
     *
     * @return string The encrypted data in base64-encoded format.
     */
    function encrypt(string $data): string {
        $key = file_get_contents(app_enckey_url);
        $ivlen = openssl_cipher_iv_length(app_cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encrypted = openssl_encrypt($data, app_cipher, $key, 0, $iv);
        return base64_encode("$encrypted::$iv");
    }

    /**
     * Decrypts data encrypted with app_cipher.
     *
     * This function decrypts the encrypted data using the encryption key
     * stored in the file defined by 'app_enckey_url'.
     *
     * @param string $data The base64-encoded encrypted data.
     *
     * @return string|false The decrypted data, or false on decryption failure.
     */
    function decrypt(string $data): string|false {
        $key = file_get_contents(app_enckey_url);
        $data = base64_decode($data);
        [$encrypted_data, $iv] = explode('::', $data, 2);
        return openssl_decrypt($encrypted_data, app_cipher, $key, 0, $iv);
    }
}

if (!function_exists('dbarray_to_object')) {
    /**
     * Converts a database array to an object.
     *
     * This function converts keys of the input associative array (typically from a database result set)
     * into object properties. It converts keys in the format 'ABC_KeyName' to 'abc_key_name' and
     * returns an object with these properties.
     *
     * @param array $array The associative array to convert.
     *
     * @return object An object with properties corresponding to the converted keys.
     */
    function dbarray_to_object(array $array): object {
        $transformed_keys = array_reduce(
            array_keys($array),
            function ($acc, $key) use ($array) {
                $new_key = $key;
                if (preg_match('/^[A-Z]{3}_/', $key)) {
                    $new_key = substr($key, 4);
                }
                $new_key = preg_replace('/(?<=\\w)(?=[A-Z])/', "_", $new_key);
                $new_key = strtolower($new_key);
                $acc[$new_key] = $array[$key];
                return $acc;
            },
            []
        );

        return (object) $transformed_keys;
    }
}

if (!function_exists('is_htmx_request')) {
    /**
     * Checks if the request is an HTMX request.
     *
     * This function checks if the current HTTP request is an HTMX request
     * by inspecting the 'HTTP_HX_REQUEST' header.
     *
     * @return bool True if the request is an HTMX request, false otherwise.
     */
    function is_htmx_request(): bool {
        return isset($_SERVER['HTTP_HX_REQUEST']) && $_SERVER['HTTP_HX_REQUEST'] === 'true';
    }
}

if (!function_exists('is_ajax_request')) {
    /**
     * Checks if the request is an AJAX request.
     *
     * This function checks if the current HTTP request is an AJAX request
     * by inspecting the 'HTTP_X_REQUESTED_WITH' header.
     *
     * @return bool True if the request is an AJAX request, false otherwise.
     */
    function is_ajax_request(): bool {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}

if (!function_exists('getdef')) {
    /**
     * Retrieve the value of a defined constant or return a default value if the constant is not defined.
     *
     * @param string $key The name of the constant to retrieve.
     * @param mixed $default The default value to return if the constant is not defined. Default is null.
     *
     * @return mixed The value of the constant if defined, otherwise the default value.
     */
    function getdef(string $key, mixed $default = null): mixed {
        return defined($key) ? constant($key) : $default;
    }
}

if (!function_exists('authenticate')) {
    /**
     * Authenticate a user based on provided username and password.
     *
     * This function calls the authenticate method of \Lib\Handlers\Authenticator singleton instance
     * to verify the credentials against the stored data. It returns true if authentication succeeds,
     * and false otherwise.
     *
     * @param string $username The username to authenticate.
     * @param string $password The password associated with the username.
     * @return bool True if authentication succeeds, false otherwise.
     */
    function authenticate(string $username, string $password): bool {
        return (\Lib\Handlers\Authenticator::get_instance())->authenticate($username, $password);
    }
}
