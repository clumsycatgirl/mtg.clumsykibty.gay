<?php

namespace Lib\Systems\Views;

require_once lib_traits_url . 'CsrfTrait.php';

/**
 * Class View
 *
 * A class for rendering views with support for template inheritance and custom directives.
 */
class View {
    use \Lib\Systems\Traits\CsrfTrait;

    /**
     * Data to be passed to the view
     * @var array
     */
    protected array $data;
    /**
     * The name of the view file
     * @var string
     */
    protected string $name;
    /**
     * Holds rendered sections
     * @var array
     */
    protected array $sections = [];
    /**
     * Stack for nested sections
     * @var array
     */
    protected array $section_stack = [];

    /**
     * Predefined filters for use in templates
     * @var array
     */
    protected static array $filters = [
        'uppercase' => 'strtoupper',
        'lowercase' => 'strtolower',
        'trim' => 'trim',
        'md5' => 'md5',
        'base64_encode' => 'base64_encode',
        'base64_decode' => 'base64_decode',
        'url_encode' => 'urlencode',
        'url_decode' => 'urldecode',
        'html_entities' => 'htmlentities',
        'strip_tags' => 'strip_tags',
        'encrypt' => 'encrypt',
        'decrypt' => 'decrypt',
    ];

    /**
     * Constructor.
     *
     * @param string $name The name of the view file.
     * @param array|null $data Data to be passed to the view.
     */
    public function __construct(string $name, ?array $data) {
        $this->name = $name;
        $this->data = $data ?? [];

        $this->__csrf_init();
    }

    /**
     * Renders the view.
     *
     * Parses directives and filters, then evaluates and outputs the processed content.
     *
     * @throws \Exception If the view file does not exist.
     */
    public function render(): void {
        // if ($this->data !== NULL)
        //     extract($this->data);

        $file_path = app_views_url . "{$this->name}.php";

        if (!file_exists($file_path)) {
            throw new \Exception("Trying to render file $file_path that doesn't exist");
        }

        $content = $this->get_content(app_views_url . "{$this->name}.php");

        // Parse @set
        $content = preg_replace('/@set\((.*?),\s*(.*?)\)/', '<?php $this->data[\'$1\'] = $2; ?>', $content);

        // Parse @if, @endif
        $content = preg_replace('/@if \((.*?)\)/', '<?php if ($1): ?>', $content);
        $content = str_replace('@endif', '<?php endif; ?>', $content);

        // Parse @elseif, @else
        $content = preg_replace('/@elseif \((.*?)\)/', '<?php elseif ($1): ?>', $content);
        $content = str_replace('@else', '<?php else: ?>', $content);

        // Parse @foreach, @endforeach
        $content = preg_replace('/@foreach \((.*?) as (.*?) => (.*?)\)/', '<?php foreach ($1 as $2 => $3): ?>', $content);
        $content = str_replace('@endforeach', '<?php endforeach; ?>', $content);

        // Parse @for, @endfor
        $content = preg_replace('/@for \((.*?)\)/', '<?php for ($1): ?>', $content);
        $content = str_replace('@endfor', '<?php endfor; ?>', $content);

        // Parse @while, @endwhile
        $content = preg_replace('/@while \((.*?)\)/', '<?php while ($1): ?>', $content);
        $content = str_replace('@endwhile', '<?php endwhile; ?>', $content);

        // Parse @switch, @case, @default, @endswitch
        $content = preg_replace('/@switch\((.*?)\)/', '<?php switch ($1): ?>', $content);
        $content = preg_replace('/@case\((.*?)\)/', '<?php case $1: ?>', $content);
        $content = str_replace('@default', '<?php default: ?>', $content);
        $content = str_replace('@endswitch', '<?php endswitch; ?>', $content);

        // Parse @isset, @endisset
        $content = preg_replace('/@isset\((.*?)\)/', '<?php if (isset($1)): ?>', $content);
        $content = str_replace('@endisset', '<?php endif; ?>', $content);

        // Parse @empty, @endempty
        $content = preg_replace('/@empty\((.*?)\)/', '<?php if (empty($1)): ?>', $content);
        $content = str_replace('@endempty', '<?php endif; ?>', $content);

        // Parse {{-- --}}
        $content = preg_replace('/{{--(.*?)--}}/', '', $content);

        // Parse @php, @endphp
        $content = preg_replace('/@php(.*?)@endphp/s', '<?php $1 ?>', $content);

        // Parse {{ | filters }}
        $content = preg_replace_callback('/{{(.*?)\|(.*?)}}/', function ($matches) {
            $variable = trim($matches[1]);
            $filters = array_map('trim', explode(' ', $matches[2]));

            $filtered_variable = $variable;
            foreach ($filters as $filter) {
                if (empty($filter))
                    continue;
                if (!isset(self::$filters[$filter]))
                    throw new \Exception("Unknown filter: $filter");
                $filtered_variable = self::$filters[$filter] . "($filtered_variable)";
            }

            return "<?= $filtered_variable; ?>";
        }, $content);

        // Parse {{ }}, {{{ }}}
        $content = preg_replace('/{{{(.*?)}}}/', '<?= htmlspecialchars($1, ENT_QUOTES, \'UTF-8\'); ?>', $content);
        $content = preg_replace('/{{(.*?)}}/', '<?= $1; ?>', $content);

        // Parse @section, @endsection
        $content = preg_replace('/@section\(\'(.*?)\'\)(.*?)@endsection/s', '<?php $this->section(\'$1\'); ?>$2<?php $this->end_section(); ?>', $content);

        // Parse @extend
        $content = preg_replace('/@extend\(\'(.*?)\'\)/', '<?php $this->extend(\'$1\'); ?>', $content);

        // Parse @include
        $content = preg_replace('/@include\(\'(.*?)\'\)/', '<?php $this->include(\'$1\'); ?>', $content);

        // Parse @dump
        $content = preg_replace_callback('/@dump\((.*?)\)/', function ($matches) {
            $expression = $matches[1];

            return '<?php
                $result = ' . $expression . ';
                if (is_array($result)) {
                    echo "<pre>";
                    print_r($result);
                    echo "</pre>";
                } else if (is_object($result)) {
                    echo "<pre>";
                    var_export($result);
                    echo "</pre>";
                } else {
                    echo $result;
                }
            ?>';
        }, $content);

        // Parse @render
        $content = preg_replace('/@render\(\'(.*?)\'\)/', '<?php $this->render_section(\'$1\'); ?>', $content);

        // Parse @css
        $content = preg_replace('/@css\(\'(.*?)\'\)/', '<link rel="stylesheet" href="<?= assets(\'css/$1\') ?>?<?= time() ?>">', $content);

        // Parse @dist
        $content = preg_replace('/@dist\(\'(.*?)\'\)/', '<link rel="stylesheet" href="<?= assets(\'dist/$1\') ?>?<?= time() ?>">', $content);

        // Parse @haxe
        $content = preg_replace('/@haxe\(\'(.*?)\'\)/', '<script src="<?= assets(\'dist/js/$1\') ?>"></script>', $content);
        $content = preg_replace('/@haxe\(\'(.*?)\' defer\)/', '<script src="<?= assets(\'dist/js/$1\') ?>?<?= time() ?>" defer></script>', $content);


        // Parse @js
        $content = preg_replace('/@js\(\'(.*?)\' defer\)/', '<script src="<?= assets(\'js/$1\') ?>?<?= time() ?>" defer></script>', $content);
        $content = preg_replace('/@js\(\'(.*?)\'\)/', '<script src="<?= assets(\'js/$1\') ?>?<?= time() ?>"></script>', $content);

        // Parse @csrf
        $content = str_replace('@csrf', '<input type="hidden" name="__csrf-token" value="<?= $this->csrf->generate(); ?>">', $content);

        // Evaluate and output the processed content
        eval ("?>$content");
    }

    /**
     * Retrieves the content of a file.
     *
     * @param string $file_path The path to the file.
     * @return false|string The file content, or false on failure.
     * @throws \Exception If the file does not exist.
     */
    private function get_content(string $file_path): false|string {
        if (!file_exists($file_path)) {
            throw new \Exception("File $file_path does not exist");
        }

        return file_get_contents($file_path);
    }

    /**
     * Starts a new section in the view.
     *
     * @param string $name The name of the section.
     */
    public function section(string $name): void {
        $this->section_stack[] = $name;
        ob_start();
    }

    /**
     * Ends the current section.
     */
    public function end_section(): void {
        $contents = ob_get_clean();

        if ($this->section_stack === []) {
            throw new \RuntimeException('View themes, no current section.');
        }

        $section = array_pop($this->section_stack);

        if (!array_key_exists($section, $this->sections)) {
            $this->sections[$section] = [];
        }

        $this->sections[$section][] = $contents;
    }

    /**
     * Renders a specific section.
     *
     * @param string $name The name of the section to render.
     */
    public function render_section(string $name): void {
        if (!isset($this->sections[$name])) {
            echo '';
            return;
        }

        foreach ($this->sections[$name] as $key => $contents) {
            echo $contents;
            unset($this->sections[$name][$key]);
        }
    }

    /**
     * Extends the current view with a layout.
     *
     * @param string $layout The name of the layout view.
     */
    public function extend(string $layout): void {
        $this->name = $layout;
    }

    /**
     * Includes another view inside the current view.
     *
     * @param string $layout The name of the view to include.
     */
    public function include(string $layout): void {
        $view = new View($layout, $this->data);
        $view->render();
    }

    /**
     * Retrieves the name of the view.
     *
     * @return string The name of the view.
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Magic method to set data in the view.
     *
     * @param string $name The name of the data variable.
     * @param mixed $value The value of the data variable.
     */
    public function __set(string $name, mixed $value): void {
        $this->data[$name] = $value;
    }

    /**
     * Magic method to get data from the view.
     *
     * @param string $name The name of the data variable.
     * @return mixed The value of the data variable, if set.
     */
    public function __get(string $name): mixed {
        return isset($this->data[$name])
            ? $this->data[$name]
            : null;
    }
}
