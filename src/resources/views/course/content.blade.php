@extends('layouts.app')

@section('title', 'Add Course Content')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-6">
            <h1 class="text-black-40 main-title">Add Course Content *</h1>
        </div>
        <div class="col-6 text-right">
            <a href="#" class="btn btn-primary"><i class="cil-user"></i> Participants</a>
            <a href="#" class="btn btn-primary"><i class="cil-list-rich"></i> Course Quizes</a>
        </div>
        <div class="col-12">
            <form method="post" action="" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <textarea class="ckeditor form-control" name="wysiwyg-editor"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
</div>
    <div class="row mt-5 mb-5">
    <div class="col-12">
            <h1 class="text-black-40 main-title">Add Course Attachments</h1>
        </div>
        <div class="col-6">
        <div class="field_wrapper">
            <div class="input-group mb-3 add-field">
                <div class="custom-file file-upload">
                    <input type="file" class="custom-file-input" id="inputGroupFile02">
                    <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                </div>
                <div class="input-group-append">
                    <span class="input-group-text" id="">Upload</span>
                </div>
                <span class="remove_button"></span>
            </div>
            <a href="javascript:void(0);" class="add_button btn btn-primary" title="Add field">ADD NEW</a>
        </div>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });
</script>
<script type="text/javascript">
$(document).ready(function() {
    $("body").on("click",".add_button",function(){ 
        var html = $(".add-field").first().clone();
        $(html).find(".remove_button").html("<a href='javascript:void(0);' class='btn btn-danger remove'>Remove</a>");
        $(".add_button").last().before(html);
    });
    $("body").on("click",".remove",function(){ 
        $(this).parents(".add-field").remove();
    });
});
</script>
@endpush