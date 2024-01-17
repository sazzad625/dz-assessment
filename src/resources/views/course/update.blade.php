@extends('layouts.app')
@section('title', 'Edit Course')
@section('third_party_stylesheets')
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css">
@endsection

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-sm-12 col-md-4">
                <h1 class="text-black-40 main-title">Update Course</h1>
            </div>
            <div class="col-sm-12 col-md-8">
                @hasPermission(App\Models\Permission::MANAGE_COURSE_CONTENT_PERMISSION)
                <a href="{{route('course.content.manage', $course->id)}}" class="btn btn-primary float-right ml-1"><i class="cil-people"></i>&nbsp;Manage Contents</a>
                @endHasPermission
                <a href="{{route('course.participant.search', $course->id)}}" class="btn btn-primary float-right ml-1"><i class="cil-people"></i>&nbsp;Participants</a>
                <a href="{{route('quiz.search', $course->id)}}" class="btn btn-primary float-right ml-1"><i class="fa fa-question-circle"></i>&nbsp;Course Quizzes</a>
            </div>
        </div>

        <form id="form" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="container white-bg">
            <div class="row">
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <label>Course Full Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           maxlength="125"
                           value="{{old('name', $course->name)}}"
                           required
                    />
                    @error('name')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <label>Course Short Name *</label>
                    <input type="text" class="form-control @error('short_name') is-invalid @enderror"
                           id="short_name"
                           name="short_name"
                           maxlength="125"
                           value="{{old('short_name', $course->short_name)}}"
                           required
                    />
                    @error('short_name')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <label>Course Category *</label>
                    <select class="form-control @error('category') is-invalid @enderror" id="short_name" name="category" required>
                        <option {{old('category', null) == null ? 'selected': ''}} value="">Select Category</option>
                        @foreach(App\Models\CourseCategory::all() as $category)
                            <option
                                value="{{$category->id}}" {{old('category', $course->fk_course_categories_id) == $category->id ? "selected" : ""}}>
                                {{$category->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                <label>Course Visibility</label>
                <label class="c-switch c-switch-label c-switch-pill c-switch-primary">
                    <input class="c-switch-input" type="checkbox" id="active" value="true" name="active" {{old('active', $course->is_active) ? 'checked' : ''}}>
                    <span class="c-switch-slider" data-checked="ON" data-unchecked="OFF"></span>
                </label>

                </div>
                <div class="form-group col-12 col-sm-3 col-md-3">
                    <label>Course Start Date</label>
                    <input class="form-control @error('start_date') is-invalid @enderror datepicker"
                              id="start_date"
                              name="start_date"
                              value="{{old('start_date', $course->start_date)}}"
                              type="text"
                              autocomplete="off"
                              required
                    />
                    @error('start_date')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-3 col-md-3">
                    <label>Course End Date</label>
                    <input class="form-control @error('end_date') is-invalid @enderror datepicker"
                              id="end_date"
                              name="end_date"
                              value="{{old('end_date', $course->end_date)}}"
                              type="text"
                              autocomplete="off"
                              required
                    />
                    @error('end_date')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
            </div>
            </div>

            <div class="row">
                <div class="form-group col-md-2 col-sm-12">
                    <label>&nbsp;</label>
                    <button type="submit" id="btnSubmit" class="btn btn-primary btn-block">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="modalErrorAlert">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Error!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Modal body text goes here.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $('#btnSubmit').click(function () {
            $('#form').submit(function (e) {
                $('#btnSubmit').attr('disabled', true);
            });
        });
        $('.datepicker').datepicker({
            clearBtn: true,
            format: "yyyy-mm-dd"
        });
    </script>
@endpush

@section('third_party_scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
@endsection
