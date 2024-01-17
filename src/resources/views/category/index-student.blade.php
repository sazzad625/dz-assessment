@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-6">
            <h1 class="text-black-40 main-title">Course Categories</h1>
        </div>
        <div class="col-6">
            <form id='searchForm' Method='GET'>
                <div class="input-prepend input-group search-box">
                    <div class="input-group-prepend"><span class="input-group-text">
                            <i class="cil-search"></i></span></div>
                    <input class="form-control" name="search" id="searchText" size="16" type="text"
                           placeholder="Search Category" value="{{request('search')}}" /><span class="input-group-append">
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        @foreach ($categories as $category)
        <div class="col-12 col-sm-6 col-md-4">
            <div class="category-box {{empty($category->user_id) ? 'lock' : ''}}">
                <div class="cat-img">
                    <a href="{{route('course.category', $category->id)}}"><img src="{{\App\Helpers\OssStorageHelper::getStoragePathForAssets($category->image_path, $category->image_name)}}" /></a>
                </div>
                <div class="cat-name"><a href="{{route('course.category', $category->id)}}">{{$category->name}}</a></div>
                <div class="cat-link"><a href="{{route('course.category', $category->id)}}">View Courses <i class="cil-arrow-right"></i></a></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('page_scripts')
    <script>
        $('#searchText').keydown(function(event){
            if ( event.which === 13 ) {
                $("#searchForm").submit();
            }
        })
    </script>
@endpush
