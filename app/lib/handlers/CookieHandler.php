<?php

namespace Lib\Handlers;

use Lib\Systems\Traits\SingletonTrait;

/**
 * Class CookieHandler
 *
 * A class for managing HTTP cookies.
 */
class CookieHandler {
    use SingletonTrait;

    /**
     * Sets a cookie.
     *
     * @param string $name The name of the cookie.
     * @param string $value The value to be stored in the cookie.
     * @param int $expire The time the cookie expires (default is 0, which means "until the browser is closed").
     * @param string $path The path on the server in which the cookie will be available (default is "/").
     * @param string $domain The (sub)domain that the cookie is available to (default is "").
     * @param bool $secure Indicates that the cookie should only be transmitted over a secure HTTPS connection (default is false).
     * @param bool $httponly When true the cookie will be made accessible only through the HTTP protocol (default is true).
     */
    public function set(
        string $name,
        string $value,
        int $expire = 0,
        string $path = "/",
        string $domain = "",
        bool $secure = false,
        bool $httponly = true
    ): void {
        setcookie($name, $value, [
            'expires' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => 'Lax' // Default SameSite attribute for better security
        ]);
    }

    /**
     * Retrieves a cookie value.
     *
     * @param string $name The name of the cookie to retrieve.
     * @param mixed $default Default value if cookie does not exist.
     * @return mixed The value of the cookie or default value.
     */
    public function get(string $name, mixed $default = null): mixed {
        return $_COOKIE[$name] ?? $default;
    }

    /**
     * Checks if a cookie is set.
     *
     * @param string $name The name of the cookie to check.
     * @return bool True if the cookie is set, false otherwise.
     */
    public function is_set(string $name): bool {
        return isset($_COOKIE[$name]);
    }

    /**
     * Removes a cookie by setting its expiration date in the past.
     *
     * @param string $name The name of the cookie to remove.
     * @param string $path The path on the server in which the cookie was available.
     * @param string $domain The (sub)domain that the cookie was available to.
     * @param bool $secure Indicates that the cookie should only be transmitted over a secure HTTPS connection.
     * @param bool $httponly When true the cookie will be made accessible only through the HTTP protocol.
     */
    public function remove(
        string $name,
        string $path = "/",
        string $domain = "",
        bool $secure = false,
        bool $httponly = true
    ): void {
        setcookie($name, '', time() - 3600, $path, $domain, $secure, $httponly);
        unset($_COOKIE[$name]);
    }
}
