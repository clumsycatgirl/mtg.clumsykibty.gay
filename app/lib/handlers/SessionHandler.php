<?php

namespace Lib\Handlers;

use Lib\Systems\Traits\SingletonTrait;

/**
 * Class SessionHandler
 *
 * A class for managing PHP sessions and flash data.
 */
class SessionHandler {
    use SingletonTrait;

    private function init() {
        // Set session garbager collector lifetime to 1 week (604800 seconds)
        ini_set('session.gc_maxlifetime', 604800);
        // Set session cookie lifetime to 1 week (604800 seconds)
        ini_set('session.cookie_lifetime', 604800);

        session_start();
        $_SESSION['__flash_vars__'] = [];
    }

    /**
     * Sets a session variable.
     *
     * @param string $key The key of the session variable.
     * @param mixed $value The value to be stored.
     */
    public function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieves a session variable.
     *
     * @param string $key The key of the session variable to retrieve.
     * @param mixed $default Default value if key does not exist.
     * @return mixed The value of the session variable or default value.
     */
    public function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Removes a session variable.
     *
     * @param string $key The key of the session variable to remove.
     */
    public function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    /**
     * Destroys the current session.
     */
    public function destroy(): void {
        session_destroy();
    }

    /**
     * Regenerates the session ID.
     */
    public function regenerate(): void {
        session_regenerate_id(true);
    }

    /**
     * Sets flash data that is available only until the next request.
     *
     * @param string $key The key of the flash data.
     * @param string $value The value of the flash data.
     */
    public function set_flash_data(string $key, string $value): void {
        $_SESSION['__flash_vars__'][$key] = $value;
    }

    /**
     * Retrieves and clears flash data from the previous request.
     *
     * @param string $key The key of the flash data to retrieve.
     * @param mixed $default Default value if key does not exist.
     * @return mixed The value of the flash data or default value.
     */
    public function get_flash_data(string $key, mixed $default = null): mixed {
        $value = $default;
        if ($this->is_set($key)) {
            $value = $_SESSION['__flash_vars__'][$key];
            unset($_SESSION['__flash_vars__'][$key]);
        }

        return $value;
    }

    /**
     * Checks if a session or flash variable is set.
     *
     * @param string $key The key to check.
     * @return bool True if the key is set in session or flash data, false otherwise.
     */
    public function is_set(string $key): bool {
        return isset($_SESSION[$key]) || isset($_SESSION['__flash_vars__'][$key]);
    }

    /**
     * Unsets a session variable.
     *
     * @param string $key The key of the session variable to unset.
     * @return bool True if the variable was successfully unset, false otherwise.
     */
    public function unset(string $key): bool {
        if (!$this->is_set($key)) {
            return false;
        }
        unset($_SESSION[$key]);
        return true;
    }
}
