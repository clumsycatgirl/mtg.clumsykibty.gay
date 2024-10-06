<?php
/** @var \Lib\Systems\Views\View $this */
array_map(
    fn(string $name) => $this->$name = $$name ?? '',
    array_keys(array_filter(
        get_defined_vars(),
        fn(string $name): bool => $name !== 'this')));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{ isset($this->title) ? $this->title : 'App' }}
    </title>

    <!-- jquery -->
    @js('jquery/jquery.min.js')

    <!-- waves -->
    @js('waves/waves.min.js' defer)
    @css('waves/waves.min.css')

    <!-- tailwind -->
    @dist('css/app.css')

    <!-- htmx -->
    @js('htmx/htmx.min.js' defer)

    <!-- hyperscript -->
    @js('_hyperscript/_hyperscript.min.js')

    <!-- sweetalert -->
    @js('swal/sweetalert2.min.js')
    @css('swal/sweetalert2.min.css')
    @css('swal/dark.min.css')

    <!-- register methods -->
    @render('include')
</head>

<body class="transition-colors duration-200 dark:bg-gray-950" oncontextmenu="return false;" hx-boost="true">
    <div id="header" class="header dark:text-white text-black">
        @render('header')
    </div>

    <div id="content" class="content dark:text-white text-black">
        @render('content')
    </div>

    <div id="footer" class="footer dark:text-white text-black">
        @render('footer')
    </div>
</body>

</html>
