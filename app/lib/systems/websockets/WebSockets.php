<?php

namespace Lib\WebSockets;

use Lib\Systems\Controllers\WebSocketControllerInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Http\Router;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use WebSocketServer;

class WebSockets {
    private static array $routes = [];

    /**
     * Adds a route for WebSocket connections
     *
     * @param string $uri The WebSocket URI path
     * @param string $class The class responsible for handling connections to this URI
     * @throws \InvalidArgumentException if the class does not exist or does not implement WebSocketControllerInterface
     */
    public static function add(string $uri, string $class): void {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException("Invalid class argument; class '$class' does not exist");
        }

        $reflection = new \ReflectionClass($class);
        if (!$reflection->implementsInterface(WebSocketControllerInterface::class)) {
            throw new \InvalidArgumentException("Class must implement WebSocketControllerInterface");
        }

        self::$routes[$uri] = new $class;
    }

    /**
     * Generates and returns an IoServer that matches the routes to WebSocket handlers
     *
     * @return IoServer The generated server
     */
    public static function generate_server(): IoServer {
        return IoServer::factory(
            new HttpServer(
                new WsServer(new class (self::$routes) implements MessageComponentInterface {
            /** @var array<WebSocketControllerInterface> $routes */
            private array $routes;

            public function __construct(array $routes) {
                $this->routes = $routes;
            }

            function onClose(\Ratchet\ConnectionInterface $conn) {
                $request_uri = $conn->httpRequest->getRequestTarget();
                $this->routes[$request_uri]->onClose($conn);
            }

            function onError(\Ratchet\ConnectionInterface $conn, \Exception $e) {
                $request_uri = $conn->httpRequest->getRequestTarget();
                $this->routes[$request_uri]->onError($conn, $e);
            }

            function onOpen(\Ratchet\ConnectionInterface $conn) {
                $request_uri = $conn->httpRequest->getRequestTarget();
                $this->routes[$request_uri]->onOpen($conn);
            }

            function onMessage(\Ratchet\ConnectionInterface $conn, \Ratchet\RFC6455\Messaging\MessageInterface $msg) {
                $request_uri = $conn->httpRequest->getRequestTarget();
                $this->routes[$request_uri]->onMessage($conn, $msg);
            }
                }),
            ),
            websocket_port,
            '0.0.0.0'
        );
    }

    public static function routes_count(): int {
        return count(self::$routes);
    }
}
