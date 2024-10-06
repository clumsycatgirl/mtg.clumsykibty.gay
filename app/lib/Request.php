<?php

namespace Lib;

class Request {
    /**
     * Stores the request method (GET, POST, etc.)
     * @var RequestMethod
     */
    protected RequestMethod $method;

    /**
     * Stores the request URL
     * @var string
     */
    protected string $url;

    /**
     * Stores HTTP headers received with the request
     * @var array
     */
    protected array $headers;

    /**
     * Stores HTTP cookies received with the request
     * @var array
     */
    protected array $cookies;

    /**
     * Stores GET parameters from the request
     * @var array
     */
    protected array $get_params;

    /**
     * Stores POST parameters from the request
     * @var array
     */
    protected array $post_params;

    /**
     * Stores files received with the request
     * @var array
     */
    protected array $files;

    /**
     * Constructor for the Request class.
     * Initializes the object with request method, URL, parameters, headers, cookies, and files.
     *
     * @param RequestMethod $method The request method (GET, POST, etc.)
     * @param string $url The request URL
     * @param array $get_params GET parameters
     * @param array $post_params POST parameters
     * @param array $headers HTTP headers
     * @param array $cookies HTTP cookies
     * @param array $files Uploaded files
     */
    public function __construct(RequestMethod $method, string $url, array $get_params, array $post_params, array $headers, array $cookies, array $files) {
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->get_params = $get_params;
        $this->post_params = $post_params;
        $this->files = $files;
    }

    /**
     * Returns the request method.
     *
     * @return RequestMethod The request method object
     */
    public function method(): RequestMethod {
        return $this->method;
    }

    /**
     * Returns the request URL.
     *
     * @return string The request URL
     */
    public function url(): string {
        return $this->url;
    }

    /**
     * Retrieves a GET parameter by key.
     *
     * @param string $key The key of the GET parameter
     * @param mixed $default Default value if key does not exist
     * @return string|null|object The value of the GET parameter or default value
     */
    public function get_get(string $key, mixed $default = null): string|null|object {
        return $this->get_params[$key] ?? $default;
    }

    /**
     * Retrieves a POST parameter by key.
     *
     * @param string $key The key of the POST parameter
     * @param mixed $default Default value if key does not exist
     * @return string|null|object The value of the POST parameter or default value
     */
    public function get_post(string $key, mixed $default = null): string|null|object {
        return $this->post_params[$key] ?? $default;
    }

    /**
     * Retrieves all GET parameters.
     *
     * @return array All GET parameters
     */
    public function get_params(): array {
        return $this->get_params;
    }

    /**
     * Retrieves all POST parameters.
     *
     * @return array All POST parameters
     */
    public function post_params(): array {
        return $this->post_params;
    }

    /**
     * Retrieves all HTTP headers.
     *
     * @return array All HTTP headers
     */
    public function headers(): array {
        return $this->headers;
    }

    /**
     * Retrieves all HTTP cookies.
     *
     * @return array All HTTP cookies
     */
    public function cookies(): array {
        return $this->cookies;
    }

    /**
     * Retrieves an uploaded file by key.
     *
     * @param string $key The key of the file
     * @return array|null The file information or null if the file does not exist
     */
    public function get_file(string $key): ?array {
        return $this->files[$key] ?? null;
    }

    /**
     * Retrieves all uploaded files.
     *
     * @return array All uploaded files
     */
    public function files(): array {
        return $this->files;
    }

    /**
     * Retrieves the content of an uploaded file by key.
     *
     * @param string $key The key of the file
     * @return string|null The content of the file or null if the file does not exist
     */
    public function get_file_content(string $key): ?string {
        $file = $this->get_file($key);

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            return file_get_contents($file['tmp_name']);
        }

        return null;
    }
}
