<?php

namespace Lib\Systems\Traits;

/**
 * Trait SingletonTrait
 *
 * Provides the singleton pattern functionality.
 */
trait SingletonTrait {
    /**
     * @var static|null Holds the single instance of the class.
     */
    private static ?self $instance = null;

    /**
     * Prevent direct object creation.
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Prevent object cloning.
     */
    private function __clone(): void {
    }

    /**
     * Prevent unserializing of the instance.
     */
    public function __wakeup(): never {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * Initialize the singleton instance.
     */
    private function init(): void {
    }

    /**
     * Get the single instance of the class.
     *
     * @return static Returns the single instance of the class.
     */
    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
