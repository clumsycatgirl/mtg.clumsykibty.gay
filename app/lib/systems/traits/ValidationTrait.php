<?php

namespace Lib\Systems\Traits;

/**
 * Trait ValidationTrait
 *
 * This trait provides access to a Validator instance for performing data validation.
 */
trait ValidationTrait {
    /**
     * @var \Lib\Utils\Validator The Validator instance.
     */
    protected \Lib\Utils\Validator $validator;

    /**
     * Retrieves the Validator instance.
     *
     * @return \Lib\Utils\Validator The Validator instance.
     */
    private function validator(): \Lib\Utils\Validator {
        include_once lib_url . 'handlers' . DIRECTORY_SEPARATOR . 'Validator.php';
        return \Lib\Utils\Validator::get_instance();
    }

    /**
     * Initializes the validator property with the Validator instance.
     *
     * @return void
     */
    protected function __validation_init(): void {
        $this->validator = $this->validator();
    }
}
