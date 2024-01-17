@extends('layouts.app')
@section('title', 'Quiz')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12 col-md-4">
                <h1 class="text-black-40 main-title">Quiz Progress</h1>
            </div>
            <div class="col-sm-12 col-md-8">
                <a href="{{route('category')}}" class="btn btn-primary float-right ml-1"><i class="fa fa-home" aria-hidden="true"></i> Homepage</a>
                <a href="{{route('course.view', $courseId)}}" class="btn btn-primary float-right ml-1"><i class="fa fa-arrow-left" aria-hidden="true"></i> Course</a>
            </div>
        </div>
    </div>

    <div class="container white-bg">
        @if($bool)
        <h1 style="color:green; text-align: center">
            <i class="fa fa-check"></i> Congratulations</h1> <br>
            <h4 style="text-align: center">{{$message}}</h4>
        @else
        <h1 style="color:red; text-align: center">
            <i class="fa fa-times"></i> Failed</h1> <br>
            <h4 style="text-align: center">{{$message}}</h4>
        @endif

    </div>

@endsection
