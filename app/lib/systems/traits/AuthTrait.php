<?php

namespace Lib\Systems\Traits;

/**
 * Trait AuthTrait
 *
 * Trait for integrating authentication and authorization features.
 */
trait AuthTrait {
    /**
     * @var \Lib\Handlers\Authenticator The singleton instance of the Authenticator.
     */
    protected \Lib\Handlers\Authenticator $auth;

    /**
     * Get an instance of the Authenticator.
     *
     * @return \Lib\Handlers\Authenticator Returns an instance of Authenticator.
     */
    private function auth(): \Lib\Handlers\Authenticator {
        include_once lib_url . 'handlers' . DIRECTORY_SEPARATOR . 'Authenticator.php';
        return \Lib\Handlers\Authenticator::get_instance();
    }

    /**
     * Initializes the auth property with the singleton instance of Authenticator.
     *
     * @return void
     */
    protected function __auth_init(): void {
        $this->auth = $this->auth();
    }
}
