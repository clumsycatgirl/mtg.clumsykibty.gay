<?php

namespace Lib\Handlers;

use Lib\Systems\Traits\SessionTrait;
use Lib\Systems\Traits\SingletonTrait;

class CsrfHandler {
    use SessionTrait, SingletonTrait;

    /**
     * Generates a CSRF token and stores it in session data.
     *
     * This function generates a random CSRF token using random_bytes() and stores it in session data
     * under the key 'csrf-token'. It returns the generated token as a string.
     *
     * @return string The generated CSRF token.
     */
    function generate(): string {
        $token = bin2hex(random_bytes(32));
        $this->session()->set('csrf-token', $token);
        return $token;
    }

    /**
     * Verifies if a given CSRF token matches the stored token in session data.
     *
     * This function retrieves the stored CSRF token from session data and compares it
     * with the provided token. It returns true if the tokens match, and false otherwise.
     *
     * @param string $token The CSRF token to verify.
     *
     * @return bool True if the CSRF token is valid and matches the stored token, false otherwise.
     */
    function verify(string $token): bool {
        return $this->session()->get('csrf-token', false) === $token;
    }
}
