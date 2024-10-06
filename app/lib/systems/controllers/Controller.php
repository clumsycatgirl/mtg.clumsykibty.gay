<?php

namespace Lib\Systems\Controllers;

require_once lib_traits_url . 'HttpResponseTrait.php';

/**
 * Class Controller
 *
 * Base controller class providing access to the request object.
 */
class Controller {
    use \Lib\Systems\Traits\HttpResponseTrait;

    /**
     * The Request object for handling HTTP requests
     * @var \Lib\Request
     */
    protected \Lib\Request $request;

    /**
     * Constructor to inject the Request object into the controller.
     *
     * @param \Lib\Request $request The Request object containing request details.
     */
    public function __construct(\Lib\Request $request) {
        $this->request = $request;
        $this->initialize_traits();
    }

    /**
     * Initialize all traits that have an initialization method.
     */
    private function initialize_traits(): void {
        $traits = $this->get_traits();
        foreach ($traits as $trait) {
            $class_name = (new \ReflectionClass($trait))->getShortName();
            $class_name = preg_replace_callback('/[A-Z]/', fn(array $str): string => '_' . strtolower($str[0]), $class_name);
            $method = '_' . str_replace('trait', 'init', $class_name);
            if (method_exists($this, $method)) {
                $this->$method();
            }
        }
    }

    /**
     * Get all traits used in the current class and its parents.
     *
     * @return array Array of trait names used in the current class.
     */
    private function get_traits(): array {
        $traits = [];
        $class = new \ReflectionClass($this);
        while ($class) {
            $traits = array_merge($traits, $class->getTraitNames());
            $class = $class->getParentClass();
        }
        return $traits;
    }
}
