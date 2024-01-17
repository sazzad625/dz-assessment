<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>    
    <link rel="shortcut icon" href="//laz-img-cdn.alicdn.com/tfs/TB1dtCmrHZnBKNjSZFrXXaRLFXa-64-64.ico"> 
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    @stack('page_css')
    <!-- CoreUI CSS -->
    
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" crossorigin="anonymous">
    

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
          integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
          crossorigin="anonymous"/>
    
    @yield('third_party_stylesheets')
    <style>
        [v-cloak] { display: none; }
    </style>

</head>

<body class="c-app">
@isAdmin
    @include('layouts.sidebar')
@endIsAdmin

@isTeacher
    @include('layouts.sidebar')
@endIsTeacher

<style>
    .header-toast {
        position: fixed;
        right: 10px;
        top: 80px;
        z-index: 99999;
    }
    .toast.header-toast.Success {
        background: #28a745;
        color: #fff;
    }
    .toast.header-toast.Success .toast-header {
        background: #5ed078;
        color: #fff;
    }

    .toast.header-toast.success {
        background: #28a745;
        color: #fff;
    }
    .toast.header-toast.success .toast-header {
        background: #5ed078;
        color: #fff;
    }
    .toast.header-toast.error {
        background: #dc3545;
        color: #fff;
    }

    .toast.header-toast.error .toast-header {
        background: #e36d78;
        color: #fff;
    }

    .toast.header-toast.Error {
        background: #dc3545;
        color: #fff;
    }

    .toast.header-toast.Error .toast-header {
        background: #e36d78;
        color: #fff;
    }
</style>
<div class="c-wrapper">
    <header class="c-header c-header-light c-header-fixed">
        @include('layouts.header')
    </header>
    <div id="modalGeneralAlert" class="toast header-toast hide" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
        <strong class="mr-auto" id="modalAlertTitle">Bootstrap</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="toast-body" id="modalAlertMessage">
        Hello, world! This is a toast message.
    </div>
    </div>
    <div class="c-body">
        @if(session('status.success'))
            <div class="alert alert-success" role="alert">
                {{session('status.success')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session('status.error'))
            <div class="alert alert-danger" role="alert">
                {{session('status.error')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <main class="c-main pl-3 pr-3">
            @yield('content')
        </main>
    </div>
    
    <div class="modal fade" id="modalGeneralAlert" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAlertTitle">Alert!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="modalAlertMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="c-footer">
        <div><a href="{{config('app.url')}}">{{config('app.name')}}</a> © {{date("Y")}}</div>
    </footer>
</div>

<script src="{{ mix('js/app.js') }}" defer></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.4.0/perfect-scrollbar.js"></script>
<script src="{{asset('js/jquery-3.6.0.min.js')}}"></script>

@yield('third_party_scripts')

@stack('page_scripts')
<script>
    function alertModal(title, message) {
        $('#modalAlertTitle').html(title);
        if(title.toLowerCase()=='error') {
            $('#modalGeneralAlert').removeClass('success');
            $('#modalGeneralAlert').addClass('error');
        } else {
            $('#modalGeneralAlert').removeClass('error');
            $('#modalGeneralAlert').addClass('success');
        }
        $('#modalAlertMessage').html(message);
        $('#modalGeneralAlert').toast('show');
    }
</script>
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" defer></script>
</body>
</html>
