<?php

namespace Lib\Systems\Router;

use Lib\RequestMethod;

/**
 * Class Permits
 *
 * A simple permissions management class for route-based access control.
 */
final class Permits {
    /**
     * Array to store route permissions
     * @var array
     */
    protected static array $permissions = [];

    /**
     * Adds a permission callback for a specific route.
     *
     * @param string $route The route pattern to add permission for.
     * @param callable $callback The callback function that checks permission.
     */
    public static function add(string $route, callable $callback): void {
        self::$permissions[$route] = $callback;
    }

    /**
     * Checks if access is permitted for a specific route and HTTP method.
     *
     * @param RequestMethod $method The HTTP method of the request.
     * @param string $route The route pattern to check permission for.
     * @return bool Returns true if access is permitted, false otherwise.
     */
    public static function check(RequestMethod $method, string $route): bool {
        if (!isset(self::$permissions[$route])) {
            return true; // No permission callback registered; assume permission granted.
        }

        $callback = self::$permissions[$route];
        return $callback($method, $route) ?? false; // Execute callback to check permission.
    }
}
