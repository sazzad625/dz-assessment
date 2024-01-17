<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>Login | {{ config('app.name') }}</title>
    <meta name="description" content="CoreUI Template - InfyOm Laravel Generator">
    <meta name="keyword" content="CoreUI,Bootstrap,Admin,Template,InfyOm,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">

    <!-- CoreUI CSS -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
          integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
          crossorigin="anonymous"/>

</head>

<body class="plain-white-bg home">
<div class="container">
    <div class="row">
        <div class="col-12 mt-3 mb-3">
            <a href="#"><img src="{{asset('images/ops-new-logo.png')}}" width="150" class="logo"/></a>
        </div>
    </div>
    <div class="row home-box">
        <div class="col">
            <img src="{{asset('images/home-banner.png')}}" class="main-banner" />
        </div>
        <div class="col">
            <span>OPS ACADEMY</span>
            <h1>Start Learning Today! <br> Anywhere, anytime!</h1>
            <form method="post" action="{{ route('login') }}">
                            @csrf
                            <p class="text-muted">Sign In to your account</p>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}"
                                       placeholder="Username or Email">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="input-group mb-4">
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Password" name="password">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <button class="btn btn-primary px-4" type="submit">Login</button>
                                </div>
                                <div class="col-8 text-right">
                                    <a class="btn btn-link px-0" href="{{ route('password.request') }}">
                                        Forgot password?
                                    </a>
                                </div>
                            </div>
                        </form>
        </div>
    </div>
</div>
<div class="primary-bg bottom-section">
    <div class="container">
        
    <h2>What Does Ops Academy Provide</h2>
        <div class="row">
            <div class="col-4">
                <img src="{{asset('images/icon-01.png')}}" class="home-icon" />
                <span>Efficient Learning</span>
            </div>
            <div class="col-4">
                <img src="{{asset('images/icon-02.png')}}" class="home-icon" />
                <span>Convenient & Flexible Access</span>
            </div>
            <div class="col-4">
                <img src="{{asset('images/icon-03.png')}}" class="home-icon" />
                <span>Accelerated Training</span>
            </div>
        </div>
    </div>
</div>
<div class="home-footer">
    <div class="container">
         <img src="{{asset('images/ops_new-logo.png')}}" width="150"/>
         <span>{{config('app.name')}} © {{date("Y")}}</span>
    </div>
</div>

<!-- CoreUI -->
<script src="{{ mix('js/app.js') }}" defer></script>

</body>
</html>
