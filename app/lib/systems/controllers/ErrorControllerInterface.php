<?php

namespace Lib\Systems\Controllers;

/**
 * Interface for handling error responses.
 */
interface ErrorControllerInterface {

    /**
     * Handles a generic error response.
     *
     * @return void
     */
    public function error(): void;

    /**
     * Handles a 404 Not Found error response.
     *
     * @return void
     */
    public function not_found(): void;

    /**
     * Handles a 403 Forbidden error response.
     *
     * @return void
     */
    public function forbidden(): void;
}
