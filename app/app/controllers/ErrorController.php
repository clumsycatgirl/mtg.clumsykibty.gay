<?php

namespace App\Controllers;

use Lib\Systems\Controllers\Controller;
use Lib\Systems\Controllers\ErrorControllerInterface;
use Lib\Systems\Traits\ViewTrait;

class ErrorController extends Controller implements ErrorControllerInterface {
    use ViewTrait;

    public function error(): void {
        $error = $this->request->get_get('error');
        $this->view('errors/error', ['error' => $error]);
    }

    public function not_found(): void {
        $this->view('errors/404');
    }

    public function forbidden(): void {
        $this->view('errors/403');
    }
}
