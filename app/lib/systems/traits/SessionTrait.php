<?php

namespace Lib\Systems\Traits;

/**
 * Trait SessionTrait
 *
 * This trait provides access to a singleton instance of SessionHandler for managing sessions.
 */
trait SessionTrait {
    /**
     * @var \Lib\Handlers\SessionHandler The singleton instance of the SessionHandler.
     */
    protected \Lib\Handlers\SessionHandler $session;

    /**
     * Returns the singleton instance of the SessionHandler.
     *
     * This function ensures that only one instance of the SessionHandler is created
     * and used throughout the application. It calls the get_instance method of SessionHandler
     * to retrieve the instance.
     *
     * @return \Lib\Handlers\SessionHandler The singleton instance of the SessionHandler.
     */
    private function session(): \Lib\Handlers\SessionHandler {
        include_once lib_url . 'handlers' . DIRECTORY_SEPARATOR . 'SessionHandler.php';
        return \Lib\Handlers\SessionHandler::get_instance();
    }

    /**
     * Initializes the session property with the singleton instance of SessionHandler.
     *
     * @return void
     */
    protected function __session_init(): void {
        $this->session = $this->session();
    }
}
