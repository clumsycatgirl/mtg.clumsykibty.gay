<?php

namespace Lib\Systems\Traits;

/**
 * Trait CookieTrait
 *
 * This trait provides access to a singleton instance of CookieHandler for managing cookies.
 */
trait CookieTrait {
    /**
     * @var \Lib\Handlers\CookieHandler The singleton instance of the CookieHandler.
     */
    protected \Lib\Handlers\CookieHandler $cookie;

    /**
     * Returns the singleton instance of the CookieHandler.
     *
     * This function ensures that only one instance of the CookieHandler is created
     * and used throughout the application. It calls the get_instance method of CookieHandler
     * to retrieve the instance.
     *
     * @return \Lib\Handlers\CookieHandler The singleton instance of the CookieHandler.
     */
    private function cookie(): \Lib\Handlers\CookieHandler {
        include_once lib_url . 'handlers' . DIRECTORY_SEPARATOR . 'CookieHandler.php';
        return \Lib\Handlers\CookieHandler::get_instance();
    }

    /**
     * Initializes the cookie property with the singleton instance of CookieHandler.
     *
     * @return void
     */
    protected function __cookie_init(): void {
        $this->cookie = $this->cookie();
    }
}
