<?php


// minimum version check
$min_php_version = '8.0';
if (version_compare(PHP_VERSION, $min_php_version, '<')) {
    $message = sprintf('The PHP version must be %s or higher to run this project, your current version is %s', $min_php_version, PHP_VERSION);
    exit($message);
}

define('lib_start_time', microtime(true));

// path to project root
define('base_url', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// path the front controller (index.php)
define('fc_url', __DIR__ . DIRECTORY_SEPARATOR);


// ensure current directory points to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== fc_url) {
    chdir(fc_url);
}


// load composer autoload file
require_once base_url . 'vendor/autoload.php';


// Load the .env file and set the environment variables
require_once base_url . 'lib/DotEnv.php';
$dotenv = new \Lib\DotEnv(base_url . '.env');
$dotenv->load();


// Load config files
require_once base_url . 'app/config/config.php';

require_once base_url . 'lib/config/paths.php';
require_once base_url . 'app/config/paths.php';

define('assets_url', app_assets_url);


// common functions used throughout the entire project/library
require_once lib_url . 'common.php';

define('is_cli', php_sapi_name() === 'cli');


// error handling
error_reporting(constant(getdef('app_error_reporting', 'E_ALL')));
getenv('environment') === 'development' ? ini_set('display_errors', 1) : ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', get_log_file_name());
set_error_handler('error_handler');


// get app and handle request
require_once lib_url . 'App.php';

$app = new Lib\App();
$app->initialize();

$app->run();


exit;
