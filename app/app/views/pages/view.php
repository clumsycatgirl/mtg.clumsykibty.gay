<?php /** @var Lib\Systems\Views\View $this */ ?>

@set(title, 'view')

@extend('layout/base')

@section('header')
@include('pages/partials/top')
@endsection

@section('content')
<div class="flex items-center justify-center flex-col h-[88vh]">
    <div class="align-middle self-center"><i>
            view pages
        </i></div>
</div>
@endsection

@section('footer')
@include('pages/partials/bottom')
@endsection