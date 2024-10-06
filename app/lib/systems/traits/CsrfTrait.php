<?php

namespace Lib\Systems\Traits;

/**
 * Trait CsrfTrait
 *
 * Provides methods for generating and validating CSRF tokens using a CsrfHandler instance.
 */
trait CsrfTrait {
    /**
     * @var \Lib\Handlers\CsrfHandler The CSRF handler instance.
     */
    protected \Lib\Handlers\CsrfHandler $csrf;

    /**
     * Get an instance of the CsrfHandler.
     *
     * @return \Lib\Handlers\CsrfHandler Returns an instance of CsrfHandler.
     */
    private function csrf(): \Lib\Handlers\CsrfHandler {
        include_once lib_url . 'handlers' . DIRECTORY_SEPARATOR . 'CsrfHandler.php';
        return \Lib\Handlers\CsrfHandler::get_instance();
    }

    /**
     * Initializes the csrf property with the singleton instance of CsrfHandler.
     *
     * @return void
     */
    protected function __csrf_init(): void {
        $this->csrf = $this->csrf();
    }
}
