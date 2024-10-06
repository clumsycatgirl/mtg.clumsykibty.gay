<?php

namespace Lib;

use App\Controllers\ErrorController;
use Lib\Cli\Args;
use Lib\Systems\Controllers\JsonErrorControllerBase;
use Lib\Systems\Router\CliRouter;
use Lib\Systems\Router\WebRouter;
use Lib\Systems\Router\Permits;
use Lib\Request;
use Lib\WebSockets\WebSockets;

include_once 'Request.php';
include_once lib_sys_url . 'traits' . DIRECTORY_SEPARATOR . 'SessionTrait.php';

/**
 * Class App
 *
 * Main application class responsible for initializing and running the application,
 * handling requests, and managing error responses.
 */
class App {
    use \Lib\Systems\Traits\SessionTrait;

    /**
     * @var bool Indicates whether to include a custom error controller.
     */
    protected bool $include_error_controller = false;

    /**
     * @var ?string Class of the custom error controller if included, will only be used if $include_error_controller is set
     */
    protected ?string $error_controller_class = null;

    /**
     * Initialize the application by including necessary files and setting up the environment.
     *
     * @return void
     */
    public function initialize(): void {
        include_once 'RequestMethod.php';
        include_once lib_sys_url . 'traits/ViewTrait.php';

        include_once app_cfg_url . 'database.php';
        $this->include_files_from_directory(lib_url . 'database' . DIRECTORY_SEPARATOR);

        include_once lib_sys_url . 'traits' . DIRECTORY_SEPARATOR . 'SingletonTrait.php';

        $this->__session_init();

        // Include files from various directories
        array_map([$this, 'include_files_from_directory'], [
            lib_sys_url . 'handlers' . DIRECTORY_SEPARATOR,
            lib_models_url,
            app_types_url,
            app_models_url,
            // app_models_url . DIRECTORY_SEPARATOR . 'dataclasses' . DIRECTORY_SEPARATOR,
            lib_controllers_url,
            lib_traits_url,
            lib_utils_url,
            lib_sys_url . 'auth' . DIRECTORY_SEPARATOR,
            lib_sys_url . 'auth' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR,
        ]);

        // Include error controller if configured
        if (getdef('app_error_controller', false)) {
            $error_controller_path = app_controllers_url . app_error_controller . '.php';
            if (file_exists($error_controller_path)) {
                include_once $error_controller_path;
                $class_name = 'App\\Controllers\\' . app_error_controller;
                if (class_exists($class_name) && isset(class_implements($class_name)['Lib\Systems\Controllers\ErrorControllerInterface'])) {
                    $this->include_error_controller = true;
                    $this->error_controller_class = $class_name;
                }
            }
        }

        include_once lib_sys_url . 'websockets/WebSockets.php';

        if (!is_cli)
            include_once lib_sys_url . 'router/WebRouter.php';
        else
            include_once lib_sys_url . 'router/CliRouter.php';
        include_once app_cfg_url . 'routes.php';

        include_once lib_sys_url . 'router/Permits.php';
        include_once app_cfg_url . 'permits.php';

        log_nlcr();
    }

    public function run() {
        if (!is_cli) {
            $this->run_web();
        } else {
            $this->run_cli();
        }
    }

    /**
     * Run the application by processing the current request and invoking the appropriate controller.
     *
     * @return void
     */
    public function run_web(): void {
        $request_method = $_SERVER['REQUEST_METHOD'];
        $request_uri = $_SERVER['REQUEST_URI'];

        $request_method = RequestMethod::from($request_method);

        $request = self::prepare_request($request_method, $request_uri, $_GET, $_POST);

        $match = WebRouter::get_match($request_method, $request_uri);
        if ($match === null) {
            $this->not_found($request);
            goto end;
        }

        if (!Permits::check($request_method, $request_uri)) {
            $this->forbidden($request);
            goto end;
        }

        /** @var callable|string $callback */
        $callback = $match['match'];
        /** @var array|mixed|null $params */
        $params = $match['params'];

        if (is_string($callback)) {
            [$controller, $method] = explode('::', $callback);
            $controller_path = app_controllers_url . $controller . '.php';
            include_once $controller_path;
            $controller = "App\\Controllers\\$controller";
            $controller_instance = new $controller($request);

            $this->log_request($request, $controller, $method, $controller_path);
        } else if (is_array($callback)) {
            $controller = $callback[0];
            $method = $callback[1];

            $controller = explode('\\', $controller);
            $controller = $controller[max(count($controller) - 1, 0)];

            $controller_path = app_controllers_url . $controller . '.php';
            include_once $controller_path;
            $controller = "App\\Controllers\\$controller";
            $controller_instance = new $controller($request);

            $this->log_request($request, $controller, $method, $controller_path);
        } else {
            $this->log_request($request, 'Closure', 'Closure', '');
        }

        if (is_htmx_request() && $this->session->get_flash_data('__force_reload', false)) {
            $this->session->set_flash_data('__force_reload', false);
        }

        try {
            if (is_string($callback) || is_array($callback)) {
                echo call_user_func([$controller_instance, $method], ...$params);
            } else if (is_callable($callback)) {
                echo call_user_func($callback, ...$params);
            } else {
                $this->not_found($request);
            }
        } catch (\Throwable $th) {
            $this->error($request, $th);
        }

        end:
        if (app_environment === 'development') {
            $end_time = microtime(true);
            $execution_time = $end_time - lib_start_time;
            $execution_time = floor($execution_time * 1000);
            log_info("execution time: {$execution_time}ms");
        }

        // TODO: this is bad... fix it
        (\Lib\Database\Database::connect())->close();
    }

    public function run_cli(): void {
        Args::parse();

        [$command, $_] = Args::first();
        $match = CliRouter::get_match($command);

        if ($match === null) {
            log_error("Invalid command {$command}");
            return;
        }

        try {
            $match();
        } catch (\Throwable $th) {
            log_error("'{$command}' Error ({$th->getCode()}): '{$th->getMessage()}' at {$th->getFile()}:{$th->getLine()}");
        }
    }

    /**
     * Log request details.
     *
     * @param Request $request The request instance.
     * @param string $controller The name of the controller handling the request.
     * @param string $method The name of the method handling the request.
     * @param string $controller_path The file path of the controller.
     * @return void
     */
    protected function log_request(Request $request, string $controller, string $method, string $controller_path): void {
        if (app_environment === 'production') {
            log_info("Serving request to '{$request->url()}'");
            return;
        }

        $log_message = sprintf(
            "serving '%s'\n\t\tUri: %s\n\t\tRequest-Method: '%s'\n\t\tController: '%s'\n\t\tController-Method: '%s'",
            $request->url(),
            base_url(preg_replace('/\//', '', $request->url(), 1)),
            $request->method()->to_string(),
            $controller,
            $method
        );

        if (is_htmx_request()) {
            $log_message .= "\n\t\tHTMX-Request";
        }
        if (is_ajax_request()) {
            $log_message .= "\n\t\tAJAX-Request";
        }
        if (!empty($controller_path)) {
            $log_message .= "\n\t\tFile: file:///" . realpath($controller_path);
        }
        if ($request->method() === RequestMethod::GET && count($request->get_params())) {
            $log_message .= sprintf("\n\t\tGet-Params-Count %d", count($_GET));
        }
        if ($request->method() === RequestMethod::POST && count($request->post_params())) {
            $log_message .= sprintf("\n\t\tPost-Params-Count %d", count($_POST));
        }

        log_info($log_message);
    }

    /**
     * Handle 403 Forbidden response.
     *
     * @param Request $request The request instance.
     * @return void
     */
    protected function forbidden(Request $request): void {
        $log_message = sprintf(
            "403 - Forbidden\n\t\tRequest: %s\n\t\tUri: %s\n\t\tRequest-Method: '%s'",
            $request->url(),
            base_url(preg_replace('/\//', '', $request->url(), 1)),
            $request->method()->to_string()
        );

        if (is_htmx_request()) {
            $log_message .= "\n\t\tHTMX-Request";
        }
        if (is_ajax_request()) {
            $log_message .= "\n\t\tAJAX-Request";
        }
        if ($request->method() === RequestMethod::GET && count($_GET)) {
            $log_message .= sprintf("\n\t\tGet-Params-Count %d", count($_GET));
        }
        if ($request->method() === RequestMethod::POST && count($_POST)) {
            $log_message .= sprintf("\n\t\tPost-Params-Count %d", count($_POST));
        }

        log_error($log_message);

        if ($this->include_error_controller) {
            (new $this->error_controller_class($request))->forbidden();
        } else {
            (new JsonErrorControllerBase($request))->forbidden();
        }
    }

    /**
     * Handle 404 Not Found response.
     *
     * @param Request $request The request instance.
     * @return void
     */
    protected function not_found(Request $request): void {
        $log_message = sprintf(
            "404 - Not Found\n\t\tRequest: %s\n\t\tUri: %s\n\t\tRequest-Method: '%s'",
            $request->url(),
            base_url(preg_replace('/\//', '', $request->url(), 1)),
            $request->method()->to_string()
        );

        if (is_htmx_request()) {
            $log_message .= "\n\t\tHTMX-Request";
        }
        if (is_ajax_request()) {
            $log_message .= "\n\t\tAJAX-Request";
        }
        if ($request->method() === RequestMethod::GET && count($_GET)) {
            $log_message .= sprintf("\n\t\tGet-Params-Count %d", count($_GET));
        }
        if ($request->method() === RequestMethod::POST && count($_POST)) {
            $log_message .= sprintf("\n\t\tPost-Params-Count %d", count($_POST));
        }

        log_error($log_message);

        if ($this->include_error_controller) {
            (new $this->error_controller_class($request))->not_found();
        } else {
            (new JsonErrorControllerBase($request))->not_found();
        }
    }

    /**
     * Handle application errors.
     *
     * @param Request $request The request instance.
     * @param \Throwable $th The caught exception.
     * @return void
     */
    protected function error(Request $request, \Throwable $th): void {
        log_error("WebRequest '{$request->url()}' Error ({$th->getCode()}): '{$th->getMessage()}' at {$th->getFile()}:{$th->getLine()}");
        $error_request = self::prepare_request($request->method(), $request->url(), [...$request->get_params(), 'error' => $th], $request->post_params());
        if ($this->include_error_controller) {
            (new $this->error_controller_class($error_request))->error();
        } else {
            (new JsonErrorControllerBase($error_request))->error();
        }
    }

    /**
     * Prepare a request instance from the given parameters.
     *
     * @param string|RequestMethod $method The request method.
     * @param string $url The request URL.
     * @param array $get_params The GET parameters.
     * @param array $post_params The POST parameters.
     * @return Request The prepared request instance.
     */
    protected function prepare_request(string|RequestMethod $method, string $url, array $get_params, array $post_params): Request {
        if (!($method instanceof RequestMethod)) {
            $method = RequestMethod::from($method);
        }
        return new Request($method, $url, $get_params, $post_params, getallheaders(), $_COOKIE, $_FILES);
    }

    /**
     * Include all PHP files from the specified directory.
     *
     * @param string $directory The directory path to include PHP files from.
     * @return void
     */
    protected function include_files_from_directory(string $directory): void {
        foreach (glob("$directory*.php") as $filename) {
            try {
                include_once $filename;
            } catch (\Throwable $th) {
                log_message(LOG_ERR, $th->getMessage());
            }
        }
    }
}
