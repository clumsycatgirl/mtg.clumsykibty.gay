<?php

namespace Lib\Systems\Controllers;

/**
 * Base class for handling error responses in JSON format.
 */
class JsonErrorControllerBase extends Controller implements ErrorControllerInterface {
    /**
     * Handles a generic error response.
     *
     * Retrieves the error message from the request, if available, and sends a JSON response with a 500 status code.
     *
     * @return void
     */
    public function error(): void {
        $error = $this->request->get_get('error');
        if ($error instanceof \Throwable) {
            $error = $error->getMessage();
        }
        $this->json_response(['error' => $error], 500);
    }

    /**
     * Handles a 404 Not Found error response.
     *
     * Sends a JSON response indicating that the requested resource was not found, with a 404 status code.
     *
     * @return void
     */
    public function not_found(): void {
        $this->json_response(['error' => "'{$this->request->url()}'; resource not found"], 404);
    }

    /**
     * Handles a 403 Forbidden error response.
     *
     * Sends a JSON response indicating that access to the requested resource is forbidden, with a 403 status code.
     *
     * @return void
     */
    public function forbidden(): void {
        $this->json_response(['error' => "'{$this->request->url()}'; access is forbidden"], 403);
    }
}
