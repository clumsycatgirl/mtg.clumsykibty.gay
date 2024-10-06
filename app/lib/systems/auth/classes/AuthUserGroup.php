<?php

namespace Lib\Systems\Auth\Classes;

/**
 * Class AuthUserGroup
 *
 * Represents the relationship between a user and a group in the authentication system.
 */
class AuthUserGroup {
    private int $id;
    private int $user_id;
    private int $group_id;

    /**
     * AuthUserGroup constructor.
     *
     * @param int $id The ID of the user group relationship.
     * @param int $user_id The ID of the user.
     * @param int $group_id The ID of the group.
     */
    public function __construct(int $id, int $user_id, int $group_id) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->group_id = $group_id;
    }

    /**
     * Get the ID of the user group relationship.
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
     * Get the ID of the group.
     *
     * @return int
     */
    public function group_id(): int {
        return $this->group_id;
    }
}
