<?php

namespace Lib\Systems\Auth\Classes;

/**
 * Class AuthGroup
 *
 * Represents a user group in the authentication system.
 */
class AuthGroup {
    private int $id;
    private string $name;

    /**
     * AuthGroup constructor.
     *
     * @param int $id The ID of the group.
     * @param string $name The name of the group.
     */
    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Get the ID of the group.
     *
     * @return int
     */
    public function id(): int {
        return $this->id;
    }

    /**
     * Get the name of the group.
     *
     * @return string
     */
    public function name(): string {
        return $this->name;
    }
}
