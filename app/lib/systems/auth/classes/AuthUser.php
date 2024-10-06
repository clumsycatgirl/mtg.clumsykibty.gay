<?php

namespace Lib\Systems\Auth\Classes;

/**
 * Class AuthUser
 *
 * Represents a user in the authentication system.
 */
class AuthUser {
    private int $id;
    private string $username;
    private string $password;

    /**
     * AuthUser constructor.
     *
     * @param int $id The ID of the user.
     * @param string $username The username of the user.
     * @param string $password The password of the user.
     */
    public function __construct(int $id, string $username, string $password) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get the ID of the user.
     *
     * @return int
     */
    public function id(): int {
        return $this->id;
    }

    /**
     * Get the username of the user.
     *
     * @return string
     */
    public function username(): string {
        return $this->username;
    }

    /**
     * Get the password of the user.
     *
     * @return string
     */
    public function password(): string {
        return $this->password;
    }
}
