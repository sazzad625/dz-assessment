@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <style>

.tile {
	margin-bottom: 15px;
	border-radius: 3px;
	color: #FFFFFF;
	transition: all 1s;
}
.tile-primary {
	background-color: #303c54;
}
.tile-success {
	background-color: #53b953;
}
.tile-warning {
	background-color: #f3aa41;
}
.tile-danger {
	background-color: #e45847;
}
.tile:hover {
	opacity: 0.95;
}
.tile a {
	color: #FFFFFF;
}
.tile-heading {
	padding: 5px 8px;
	text-transform: uppercase;
	color: #FFF;
	text-shadow: 0 -1px 0 rgba(0,0,0,.4);
	background-color: rgba(255,255,255,0.1);
}
.tile .tile-heading .pull-right {
	transition: all 1s;
	opacity: 0.7;
}
.tile:hover .tile-heading .pull-right {
	opacity: 1;
}
.tile-body {
	padding: 15px;
	color: #FFFFFF;
	line-height: 48px;
	text-shadow: 0 -1px 0 rgba(0,0,0,.4);
}
.tile .tile-body i {
	font-size: 50px;
	opacity: 0.3;
	transition: all 1s;
}
.tile:hover .tile-body i {
	color: #FFFFFF;
	opacity: 1;
}
.tile .tile-body h2 {
	font-size: 42px;
}
.pull-right {
    float: right!important;
}
.tile-footer {
	padding: 5px 8px;
	background-color: rgba(0,0,0,0.1);
}
.panel {
    margin-bottom: 18px;
    background-color: #fff;
    border: 1px solid transparent;
    border-radius: 3px;
    -webkit-box-shadow: 0 1px 1px rgb(0 0 0 / 5%);
    box-shadow: 0 1px 1px rgb(0 0 0 / 5%);
}
.panel-title {
    margin-top: 0;
    margin-bottom: 0;
    font-size: 15px;
    color: inherit;
}
.panel-default {
	border: 1px solid #dcdcdc;
	border-top: 1px solid #dcdcdc;
}
.panel-default .panel-heading {
	color: #4c4d5a;
	border-color: #dcdcdc;
	background: #f6f6f6;
	text-shadow: 0 -1px 0 rgba(50,50,50,0);
}
.panel-body {
    padding: 15px;
    background#fff;
}

.panel {
	border-radius: 0px;
}
.panel .panel-heading {
	position: relative;
}
.panel-heading h3 i {
	margin-right: 5px;
	-webkit-tap-highlight-color: rgba(0,0,0,0);
}
.panel-heading h3 {
	font-weight: 500;
	display: inline-block;
}
.panel-heading {
    padding: 12px 15px;
    border-bottom: 1px solid transparent;
    border-top-right-radius: 2px;
    border-top-left-radius: 2px;
}
h1 {
    font-family: 'Open Sans', sans-serif;
    font-weight: 300;
    font-size: 30px;
    color: #4c4d5a;
    display: inline-block;
    margin-bottom: 15px;
    text-shadow: 0 1px #fff;
}
.tile .tile-body h2 span {
    font-size: 10px;
    float: right;
}

    </style>
    <div class="container-fluid">
        <h1 class="text-black-50">Dashboard</h1>

        <div id="CountApp" v-cloak>
            <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6"><div class="tile tile-primary bg-primary">
                <div class="tile-heading">Courses </div>
                <div class="tile-body"><i class="fa fa-book-open"></i>
                <h2 class="pull-right text-right">@{{courseCount}}<br><span>Attempted Quizzes: @{{attemptedQuizzesCount}}</span></h2>
                </div>
                <div class="tile-footer"><a href="course/search">View more...</a></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6"><div class="tile tile-primary bg-info">
                <div class="tile-heading">Categories </div>
                <div class="tile-body"><i class="fa fa-th-large"></i>
                <h2 class="pull-right">@{{categoryCount}}</h2>
                </div>
                <div class="tile-footer"><a href="category/view">View more...</a></div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6"><div class="tile tile-primary bg-warning">
                <div class="tile-heading">Teachers </div>
                <div class="tile-body"><i class="fa fa-user-graduate"></i>
                <h2 class="pull-right">@{{teacherCount}}</h2>
                </div>
                <div class="tile-footer"><a href="user/search/teacher">View more...</a></div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12"><div class="panel panel-default">
                <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-user"></i> Students </h3>
                </div>
                <div class="panel-body">
                <div class="row" >
                    <div class="col-lg-4 col-md-4 col-sm-6" v-for="row in participantCount">
                        <div class="tile tile-primary">
                        <div class="tile-heading">@{{row.name}} </div>
                        <div class="tile-body"><i class="fa fa-users"></i>
                        <h2 class="pull-right">@{{row.count}}</h2>
                        </div>
                        <div class="tile-footer"></div>
                        </div>
                    </div>
                </div>
                </div>
                </div> </div>
            </div>

        </div>
    </div>
@endsection
@push('page_scripts')
    <script src="//unpkg.com/vue@3.0.11"></script>
    <script src="{{asset('/js/vue/admin-dashboard.js')}}"></script>
@endpush
