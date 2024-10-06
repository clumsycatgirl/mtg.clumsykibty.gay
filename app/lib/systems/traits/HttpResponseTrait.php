<?php

namespace Lib\Systems\Traits;

trait HttpResponseTrait {
    /**
     * Sends a JSON response with the specified data and HTTP status code.
     *
     * This function sets the appropriate headers for a JSON response, sets the HTTP status code,
     * encodes the provided data as JSON, and terminates the script.
     *
     * @param mixed $data The data to encode as JSON.
     * @param int $status Optional HTTP status code for the response (default is 200).
     *
     * @return never This function terminates the script execution.
     */
    function json_response(mixed $data, int $status = 200): never {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    /**
     * Redirects to a specified URL with optional query parameters and status code.
     *
     * This function constructs a redirection URL with optional query parameters,
     * sets the appropriate HTTP header, and terminates the script. If the request is
     * an HTMX request, it uses the 'HX-Redirect' header instead of the standard 'Location' header.
     *
     * @param string $url The URL to redirect to.
     * @param array $params Optional associative array of query parameters to append to the URL.
     * @param int $statusCode Optional HTTP status code for the redirection. Defaults to 0 (no status code).
     *
     * @return never This function does not return as it terminates the script using exit.
     */
    function redirect(string $url, array $params = [], int $statusCode = 0): never {
        $cmd = 'Location:';
        if (is_htmx_request()) {
            $url = base_url() . $url;
            $cmd = 'HX-Redirect:';
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        log_debug("redirect\n\t\tHeader: '$cmd'\n\t\tUrl: '$url'");
        header("$cmd $url", true, $statusCode);
        exit;
    }

    /**
     * Sends a plain text response with the specified content and HTTP status code.
     *
     * This function sets the appropriate headers for a plain text response, sets the HTTP status code,
     * and terminates the script.
     *
     * @param string $content The text content to send.
     * @param int $status Optional HTTP status code for the response (default is 200).
     *
     * @return never This function terminates the script execution.
     */
    function text_response(string $content, int $status = 200): never {
        header('Content-Type: text/plain');
        http_response_code($status);
        echo $content;
        exit;
    }

    /**
     * Sends an HTML response with the specified content and HTTP status code.
     *
     * This function sets the appropriate headers for an HTML response, sets the HTTP status code,
     * and terminates the script.
     *
     * @param string $content The HTML content to send.
     * @param int $status Optional HTTP status code for the response (default is 200).
     *
     * @return never This function terminates the script execution.
     */
    function html_response(string $content, int $status = 200): never {
        header('Content-Type: text/html');
        http_response_code($status);
        echo $content;
        exit;
    }
}
