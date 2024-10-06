<?php

namespace Lib\Systems\Traits;

include_once lib_views_url . 'View.php';

trait ViewTrait {
    /**
     * Renders a view with the given name and data.
     *
     * This function initializes a View object with the specified name and data,
     * then renders the view. If the view name changes during rendering (due to
     * extending another layout), it re-renders the view with the new name.
     *
     * @param string $name The name of the view to render.
     * @param array $data Optional associative array of data to pass to the view.
     *
     * @return void
     */
    public static function view(string $name, array $data = []): void {
        // Create a new View object with the given name and data
        $view = new \Lib\Systems\Views\View($name, $data);

        // Check for changes in the layout name when extending
        loop_start:
        $prev_name = $view->get_name();
        $view->render();

        // If the view name has changed, re-render the view with the new name
        if ($view->get_name() !== $prev_name) {
            goto loop_start;
        }
    }

    protected function _init_view() {
        log_info('ViewTrait initialized');
    }
}
