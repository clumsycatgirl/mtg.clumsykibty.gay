<?php
use Lib\Handlers\SessionHandler;
use Lib\Systems\Router\Permits;
use Lib\RequestMethod;

Permits::add('/meow', function (RequestMethod $method, string $url): bool {
    return SessionHandler::get_instance()->is_set('meow');
});
