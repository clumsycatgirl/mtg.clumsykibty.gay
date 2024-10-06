<?php

namespace Lib\Systems\Auth\Classes;

/**
 * Class AuthUserPermission
 *
 * Represents the relationship between a user and a permission in the authentication system.
 */
class AuthUserPermission {
    private int $id;
    private int $user_id;
    private int $permission_id;

    /**
     * AuthUserPermission constructor.
     *
     * @param int $id The ID of the user permission relationship.
     * @param int $user_id The ID of the user.
     * @param int $permission_id The ID of the permission.
     */
    public function __construct(int $id, int $user_id, int $permission_id) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->permission_id = $permission_id;
    }

    /**
     * Get the ID of the user permission relationship.
     *
     * @return int
     */
    public function id(): int {
        return $this->id;
    }

    /**
     * Get the ID of the user.
     *
     * @return int
     */
    public function user_id(): int {
        return $this->user_id;
    }

    /**
     * Get the ID of the permission.
     *
     * @return int
     */
    public function permission_id(): int {
        return $this->permission_id;
    }
}
