<?php

namespace Lib\Systems\Router;

final class CliRouter {
    /**
     * Array to store defined routes
     * @var array<\Lib\Cli\Command>
     */
    private static array $routes = [];

    public static function add(string $command, string|callable $callback): void {
        if (is_string($callback)) {
            $callback = explode('\\', $callback);
            $callback = $callback[max(count($callback) - 1, 0)];
            $command_path = app_commands_url . $callback . '.php';
            include_once $command_path;
            $command_class = "App\\Commands\\$callback";
            $command_instance = new $command_class();
            $callback = fn() => $command_instance->call();
        }

        self::$routes[] = [
            'command' => $command,
            'callback' => $callback,
        ];
    }

    /**
     * Matches a URL against defined routes and default routes.
     *
     * @param string $command The command to execute.
     * @return null|callable An array containing the matched callback and parameters, or null if no match is found.
     */
    public static function get_match(string $command): null|callable {
        $path = $url_parts['path'] ?? '';
        $params = [];

        foreach (self::$routes as $route) {
            if ($route['command'] === $command) {
                return $route['callback'];
            }
        }

        return null;
    }
}
