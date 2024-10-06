<?php

namespace Lib\Systems\Auth\Classes;

/**
 * Class AuthGroupPermission
 *
 * Represents a permission associated with a user group in the authentication system.
 */
class AuthGroupPermission {
    private int $id;
    private int $group_id;
    private int $permission_id;

    /**
     * AuthGroupPermission constructor.
     *
     * @param int $id The ID of the group permission.
     * @param int $group_id The ID of the group.
     * @param int $permission_id The ID of the permission.
     */
    public function __construct(int $id, int $group_id, int $permission_id) {
        $this->id = $id;
        $this->group_id = $group_id;
        $this->permission_id = $permission_id;
    }

    /**
     * Get the ID of the group permission.
     *
     * @return int
     */
    public function id(): int {
        return $this->id;
    }

    /**
     * Get the ID of the group.
     *
     * @return int
     */
    public function group_id(): int {
        return $this->group_id;
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
