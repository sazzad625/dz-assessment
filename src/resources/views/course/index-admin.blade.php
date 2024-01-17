@extends('layouts.app')
@section('title', 'Courses')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-6">
            <h1 class="text-black-40 main-title">Courses</h1>
        </div>
        <div class="col-6">
            <form id='searchForm' Method='GET'>
                <div class="input-prepend input-group search-box">
                    <div class="input-group-prepend"><span class="input-group-text">
                            <i class="cil-search"></i></span></div>
                    <input class="form-control" name="search" id="searchText" size="16" type="text"
                           placeholder="Search Course" value="{{request('search')}}"/><span class="input-group-append">
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        @foreach ($courses as $course)
        <div class="col-sm-6 col-md-4 course-box">
            <div class="card card-accent-primary">
                <div class="card-header">{{$course->name}}</div>
                <div class="cat-link"><a href="{{route('course.view', $course->id)}}">View Detail <i class="cil-arrow-right"></i></a></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
