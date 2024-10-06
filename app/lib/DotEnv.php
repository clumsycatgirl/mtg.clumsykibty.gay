<?php

namespace Lib;

class DotEnv {
    /**
     * Path of the .env file.
     * @var string
     */
    protected string $path;

    /**
     * DotEnv constructor.
     *
     * @param string $path The path to the .env file.
     * @throws \InvalidArgumentException if the .env file does not exist.
     */
    public function __construct(string $path) {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }
        $this->path = $path;
    }

    /**
     * Load the .env file and set environment variables.
     *
     * @throws \RuntimeException if the .env file is not readable.
     */
    public function load(): void {
        if (!is_readable($this->path)) {
            throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    /**
     * Generate PHP files with defined constants based on the .env values.
     *
     * @return void
     */
    public function generate_env_files(): void {
        $database_config = [];
        $paths_config = [];
        $app_config = [];

        foreach ($_ENV as $name => $value) {
            if (strpos($name, 'database.') === 0) {
                $database_key = str_replace('database.', '', $name);
                $database_config[$database_key] = $value;
            } else if (strpos($name, 'app.') === 0) {
                $app_key = str_replace('app.', '', $name);
                $app_config[$app_key] = $value;
            } else if (strpos($name, 'paths.') === 0) {
                $path_key = str_replace('paths.', '', $name);
                $paths_config[$path_key] = $value;
            }
        }

        $this->generate_database_config($database_config);
        $this->generate_paths_config($paths_config);
        $this->generate_app_config($app_config);
    }

    /**
     * Generate the database configuration file.
     *
     * @param array $database_config The database configuration.
     * @return void
     */
    protected function generate_database_config(array $database_config): void {
        $content = "<?php\n\n";
        foreach ($database_config as $key => $value) {
            $const_name = 'database_' . $key;
            $content .= sprintf("const %s = '%s';\n", $const_name, $value);
        }

        file_put_contents(base_url . '/app/config/database.php', $content);
    }

    /**
     * Generate the paths configuration file.
     *
     * @param array $paths_config The paths configuration.
     * @return void
     */
    protected function generate_paths_config(array $paths_config): void {
        $content = "<?php\n\n";

        foreach ($paths_config as $key => $value) {
            if ($value === '')
                continue;
            $const_name = str_replace('.', '_', $key);
            $content .= sprintf("const %s = base_url . '%s' . DIRECTORY_SEPARATOR;\n", $const_name, $value);
        }

        $content .= "\n";
        $content .= "const app_url = base_url . 'app' . DIRECTORY_SEPARATOR;\n";
        $content .= "const app_cfg_url = app_url . 'config' . DIRECTORY_SEPARATOR;\n";
        $content .= "const app_controllers_url = app_url . 'controllers' . DIRECTORY_SEPARATOR;\n";
        $content .= "const app_models_url = app_url . 'models' . DIRECTORY_SEPARATOR;\n";
        $content .= "const app_views_url = app_url . 'views' . DIRECTORY_SEPARATOR;\n";
        $content .= "const app_assets_url = app_url . 'assets' . DIRECTORY_SEPARATOR;\n";

        file_put_contents(base_url . '/app/config/paths.php', $content);
    }

    /**
     * Generate the app configuration file.
     * 
     * @param array $app_config The app configuration.
     * @return void
     */
    protected function generate_app_config(array $app_config): void {
        $content = "<?php\n\n";
        foreach ($app_config as $key => $value) {
            $const_name = 'app_' . $key;
            $content .= sprintf("const %s = '%s';\n", $const_name, $value);
        }

        file_put_contents(base_url . '/app/config/config.php', $content);
    }
}
