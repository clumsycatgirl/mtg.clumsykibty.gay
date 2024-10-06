<?php

namespace Lib\Systems\Auth\Classes;

/**
 * Class AuthPermission
 *
 * Represents a permission in the authentication system.
 */
class AuthPermission {
    private int $id;
    private string $name;

    /**
     * AuthPermission constructor.
     *
     * @param int $id The ID of the permission.
     * @param string $name The name of the permission.
     */
    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Get the ID of the permission.
     *
     * @return int
     */
    public function id(): int {
        return $this->id;
    }

    /**
     * Get the name of the permission.
     *
     * @return string
     */
    public function name(): string {
        return $this->name;
    }
}
