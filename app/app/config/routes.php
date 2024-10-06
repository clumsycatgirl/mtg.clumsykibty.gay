<?php

use App\Commands\MeowCommand;
use App\Commands\WebSocketCommand;
use App\Controllers\IndexController;
use App\Controllers\WebSockets\WebSocketController;
use Lib\Systems\Router\CliRouter;
use Lib\Systems\Router\WebRouter;
use Lib\WebSockets\WebSockets;

WebRouter::get('/', [IndexController::class, 'index']);
WebRouter::get('/draw', [IndexController::class, 'draw']);
WebRouter::get('/reset', [IndexController::class, 'reset']);
WebRouter::post('/readdeck', [IndexController::class, 'read_deck']);
WebRouter::get('/shuffle', [IndexController::class, 'shuffle']);

WebRouter::get('/token', [IndexController::class, 'token']);

WebRouter::get('/life/decrease', [IndexController::class, 'life_decrease']);
WebRouter::get('/life/increase', [IndexController::class, 'life_increase']);

WebRouter::get('/deck/to/top/{uuid}', [IndexController::class, 'to_top_deck']);
WebRouter::get('/deck/to/bottom/{uuid}', [IndexController::class, 'to_bottom_deck']);

WebRouter::get('/search', [IndexController::class, 'search']);
WebRouter::post('/remove', [IndexController::class, 'remove_card_from_deck']);

WebSockets::add('/ws', WebSocketController::class);
CliRouter::add('startwebsocket', WebSocketCommand::class);

CliRouter::add('meow', MeowCommand::class);
 