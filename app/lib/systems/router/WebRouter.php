<?php

namespace Lib\Systems\Router;

use Lib\RequestMethod;
use Lib\Systems\Traits\ViewTrait;

/**
 * Class Router
 *
 * A simple router class that maps HTTP methods and URLs to callback functions or views.
 */
final class WebRouter {
    use ViewTrait {
        ViewTrait::view as private _view;
    }

    /**
     * Array to store defined routes
     * @var array
     */
    private static array $routes = [];
    /**
     * Array to store default routes
     * @var array
     */
    private static array $default_route = [];

    /**
     * Defines a route for GET requests.
     *
     * @param string $url The URL pattern for the route.
     * @param string|callable|array $callback The callback function or view name.
     */
    public static function get(string $url, string|callable|array $callback): void {
        self::add_route(RequestMethod::GET, $url, $callback);
    }

    /**
     * Defines a route for POST requests.
     *
     * @param string $url The URL pattern for the route.
     * @param string|callable|array $callback The callback function or view name.
     */
    public static function post(string $url, string|callable|array $callback): void {
        self::add_route(RequestMethod::POST, $url, $callback);
    }

    /**
     * Defines a route for specific HTTP methods.
     *
     * @param RequestMethod|array $methods The HTTP method(s) for the route.
     * @param string $url The URL pattern for the route.
     * @param string|callable|array $callback The callback function or view name.
     */
    public static function match(RequestMethod|array $methods, string $url, string|callable|array $callback): void {
        if (!is_array($methods)) {
            $methods = [$methods];
        }

        foreach ($methods as $method) {
            self::add_route($method, $url, $callback);
        }
    }

    /**
     * Defines a route that renders a view for GET requests.
     *
     * @param string $url The URL pattern for the route.
     * @param string|callable|array $callback The callback function or view name.
     */
    public static function view(string $url, string|callable|array $callback, array $data = []): void {
        if (is_callable($callback)) {
            self::add_route(RequestMethod::GET, $url, $callback);
            return;
        }

        self::add_route(RequestMethod::GET, $url, fn() => self::_view($callback, $data));
    }

    /**
     * Defines a default route for specific HTTP methods.
     *
     * @param RequestMethod|array $method The HTTP method(s) for the default route.
     * @param string|callable|array $callback The callback function or view name for the default route.
     * @throws \Exception If an invalid method is specified for the default route.
     */
    public static function default(RequestMethod|array $method, string|callable|array $callback): void {
        if (!is_array($method)) {
            $method = [$method];
        }

        $valid_methods = [RequestMethod::GET, RequestMethod::POST];
        $methods_names = array_map(fn(RequestMethod $method): string => $method->to_string(), $method);
        $invalid_methods_names = array_diff($methods_names, array_map(fn(RequestMethod $method): string => $method->to_string(), $valid_methods));

        if (count($invalid_methods_names) > 0) {
            throw new \Exception('Invalid method(s) for default route: ' . print_r($invalid_methods_names, true));
        }

        foreach ($method as $m) {
            self::$default_route[$m->to_string()] = $callback;
        }
    }

    /**
     * Adds a route to the router.
     *
     * @param RequestMethod $method The HTTP method for the route.
     * @param string $url The URL pattern for the route.
     * @param string|callable|array $callback The callback function or view name for the route.
     */
    private static function add_route(RequestMethod $method, string $url, string|callable|array $callback): void {
        self::$routes[] = [
            'method' => $method,
            'url' => $url,
            'callback' => $callback,
        ];
    }

    /**
     * Matches a URL against defined routes and default routes.
     *
     * @param RequestMethod $method The HTTP method of the request.
     * @param string $url The URL of the request.
     * @return array|null An array containing the matched callback and parameters, or null if no match is found.
     */
    public static function get_match(RequestMethod $method, string $url): null|array {
        $url_parts = parse_url($url);
        $path = $url_parts['path'] ?? '';
        $params = [];

        foreach (self::$routes as $route) {
            if ($route['method'] === $method) {
                $pattern = self::build_pattern($route['url']);
                $path_without_query = strtok($path, '?');

                if (preg_match($pattern, $path_without_query, $matches)) {
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) {
                            $params[$key] = $value;
                        }
                    }
                    return ['match' => $route['callback'], 'params' => $params];
                }
            }
        }

        // Check default routes if no explicit route matches
        if (!preg_match('/\.\w+$/', $path)) {
            $method = $method->to_string();
            if (isset(self::$default_route[$method])) {
                return ['match' => self::$default_route[$method], 'params' => $params];
            }
        }

        return null;
    }

    /**
     * Builds a regex pattern from a URL pattern.
     *
     * @param string $url The URL pattern containing placeholders.
     * @return string The regex pattern for matching the URL.
     */
    private static function build_pattern($url): string {
        // Escape slashes
        $pattern = preg_replace('/\//', '\/', $url);
        // Convert {param} to named capturing groups in regex
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^\/]+)', $pattern);
        return '/^' . $pattern . '$/';
    }
}
