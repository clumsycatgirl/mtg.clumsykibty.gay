<?php

namespace App\Commands;
use Lib\Cli\Command;
use Lib\WebSockets\WebSockets;

class WebSocketCommand extends Command {
    public function call(): void {
        $server = WebSockets::generate_server();

        $routes_count = WebSockets::routes_count();

        log_info("WebSocket Server Started with {$routes_count} route(s) on port " . websocket_port);

        @$server->run();
    }
}
