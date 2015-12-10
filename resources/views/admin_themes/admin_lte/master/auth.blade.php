<!DOCTYPE html>
<html lang="{{ $site_locale }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('site_meta')
    <title>{!! AdminTheme::title() !!}</title>
    <meta name="description" content="{!! AdminTheme::description() !!}">
    <meta name="author" content="{{ AdminTheme::author() }}">
    <meta name="keywords" content="{{ AdminTheme::keywords() }}">
@include('fav_icons')
    <link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400italic,600,600italic,700,700italic,300italic,300&subset=latin,vietnamese,latin-ext">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    @yield('lib_styles')
    <link rel="stylesheet" href="{{ AdminTheme::cssAsset('AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ AdminTheme::cssAsset('skins/skin-blue.min.css') }}">
    <link rel="stylesheet" href="{{ libraryAsset('iCheck/square/blue.css') }}">
    @yield('extended_styles')
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition @yield('auth_type')-page">
<div class="@yield('auth_type')-box">
    <div class="@yield('auth_type')-logo">
        <a href="{{ homeURL() }}"><strong>{{ $site_name }}</strong></a>
    </div>
    <div class="@yield('auth_type')-box-body">
        <p class="@yield('auth_type')-box-msg">@yield('box_message')</p>
        @yield('auth_form')
    </div>
</div>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="{{ libraryAsset('iCheck/icheck.min.js') }}"></script>
@yield('lib_scripts')
<script>
    {!! cdataOpen() !!}
    jQuery(document).ready(function () {
        jQuery('[type=checkbox]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
    {!! cdataClose() !!}
</script>
@yield('extended_scripts')
</body>
</html>