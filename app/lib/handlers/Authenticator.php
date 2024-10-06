<?php

namespace Lib\Handlers;

use Lib\Database\QueryOperators;
use Lib\Systems\Auth\AuthPermissionModel;
use Lib\Systems\Auth\AuthUserGroupModel;
use Lib\Systems\Auth\AuthUserModel;
use Lib\Systems\Auth\Classes\AuthGroup;
use Lib\Systems\Auth\Classes\AuthPermission;
use Lib\Systems\Auth\Classes\AuthUser;
use Lib\Systems\Traits\SessionTrait;
use Lib\Systems\Traits\SingletonTrait;

/**
 * Class Authenticator
 *
 * Responsible for handling authentication-related operations.
 */
class Authenticator {
    use SessionTrait, SingletonTrait;

    /**
     * Authenticate a user with the provided username and password.
     *
     * @param string $username The username of the user.
     * @param string $password The password of the user.
     * @return bool True if authentication succeeds, false otherwise.
     */
    public function authenticate(string $username, string $password): bool {
        /** @var AuthUser|false $user */
        $user = (new AuthUserModel())->first_where(['username' => $username, 'password' => chash($password)]);
        if (!$user)
            return false;

        if (!check_chash($user->password(), $password)) {
            return false;
        }

        $this->log_auth_request('Authenticate', $username);

        SessionHandler::get_instance()->set('auth.user', $user);
        return true;
    }

    /**
     * Register a new user with the provided username and password.
     *
     * @param string $username The username of the new user.
     * @param string $password The password of the new user.
     * @return bool True if registration succeeds, false otherwise.
     */
    public function register(string $username, string $password): bool {
        $model = new AuthUserModel();
        /** @var AuthUser|false $user */
        $user = $model->first_where(['username' => $username]);
        if ($user)
            return false;

        $inserted_id = $model->insert(['username' => $username, 'password' => chash($password)]);
        $user = $model->find($inserted_id);

        $this->log_auth_request('Register', $username);

        SessionHandler::get_instance()->set('auth.user', $user);

        return true;
    }

    /**
     * Log out the currently authenticated user.
     *
     * @return void
     */
    public function logout(): void {
        $this->log_auth_request('Logout', SessionHandler::get_instance()->get('auth.user')->username());
        SessionHandler::get_instance()->remove('auth.user');
    }

    /**
     * Retrieve the currently authenticated user.
     *
     * @return AuthUser|false The authenticated user object, or false if no user is authenticated.
     */
    public function user(): AuthUser|false {
        $user = SessionHandler::get_instance()->get('auth.user', false);
        if ($user === false)
            return false;
        $this->log_auth_request('Get-User', $user->username());
        return $user;
    }

    /**
     * Check if a user is authenticated.
     *
     * @return bool True if a user is authenticated, false otherwise.
     */
    public function check(): bool {
        $user = SessionHandler::get_instance()->get('auth.user');
        $this->log_auth_request('Check-Auth', $user ? $user->username() : 'None');
        return SessionHandler::get_instance()->is_set('auth.user');
    }

    /**
     * Retrieve permissions associated with the currently authenticated user.
     *
     * @return array|false Array of AuthPermission objects representing user permissions, or false if user is not authenticated.
     */
    public function get_permissions(): array|false {
        $user = $this->user();
        if ($user === false)
            return false;

        $permissions = (new AuthPermissionModel())
            ->query()
            ->select(['auth_permissions.*'])
            ->join('auth_user_permissions', 'auth_permissions.id', QueryOperators::Equals, 'auth_user_permissions.permission_id')
            ->where('auth_user_permissions.user_id', QueryOperators::Equals, $user->id())
            ->fetch_all();

        $permissions = array_map(
            fn(array $permission): AuthPermission => new AuthPermission($permission['id'], $permission['name']),
            $permissions);

        $this->log_auth_request('Get-Permissions', $user->username());

        return $permissions;
    }

    /**
     * Retrieve groups associated with the currently authenticated user.
     *
     * @return array|false Array of AuthGroup objects representing user groups, or false if user is not authenticated.
     */
    public function get_groups(): array|false {
        $user = $this->user();
        if ($user === false)
            return false;

        $groups = (new AuthUserGroupModel())
            ->query()
            ->select(['auth_user_groups.*'])
            ->join('auth_user_user_groups', 'auth_user_groups.id', QueryOperators::Equals, 'auth_user_user_groups.group_id')
            ->where('auth_user_user_groups.user_id', QueryOperators::Equals, $user->id())
            ->fetch_all();

        $groups = array_map(
            fn(string $group): AuthGroup => new AuthGroup($group['id'], $group['name']),
            $groups);

        $this->log_auth_request('Get-Groups', $user->username());

        return $groups;
    }

    /**
     * Check if the currently authenticated user has a specific permission.
     *
     * @param string $permission_name The name of the permission to check.
     * @return bool True if the user has the permission, false otherwise.
     */
    public function has_permission(string $permission_name): bool {
        $permissions = $this->get_permissions();
        if ($permissions === false)
            return false;

        return count(array_filter($permissions, fn(AuthPermission $permission): bool => $permission->name() === $permission_name)) > 0;
    }

    /**
     * Check if the currently authenticated user belongs to a specific group.
     *
     * @param string $group_name The name of the group to check.
     * @return bool True if the user belongs to the group, false otherwise.
     */
    public function has_group(string $group_name): bool {
        $groups = $this->get_groups();
        if ($groups === false)
            return false;

        return count(array_filter($groups, fn(AuthGroup $group): bool => $group->name() === $group_name)) > 0;
    }

    /**
     * Log an authentication-related request.
     *
     * @param string $action The action performed (e.g., Authenticate, Register, Logout).
     * @param string $username The username associated with the action.
     * @return void
     */
    private function log_auth_request(string $action, string $username): void {
        $content = "Auth request";
        $content .= "\n\t\tAction: '$action'";
        $content .= "\n\t\tUsername: '$username'";
        log_info($content);
    }
}
