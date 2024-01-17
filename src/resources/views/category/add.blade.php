@extends('layouts.app')
@section('title', 'Add Course Category')
@section('content')
    <div class="container">

        <h1 class="text-black-40 main-title">Add Course Category</h1>
        <form id="form" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="container white-bg">
            <div class="row">
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <label>Category Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           maxlength="125"
                           value="{{old('name')}}"
                           required
                    />
                    @error('name')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                <label>Category Visibility</label>
                <label class="c-switch c-switch-label c-switch-pill c-switch-primary">
                    <input class="c-switch-input" type="checkbox" id="active" value="true" name="active" checked="" {{old('active') ? 'checked' : ''}}>
                    <span class="c-switch-slider" data-checked="ON" data-unchecked="OFF"></span>
                </label>
                
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <label>Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description"
                              name="description"
                              maxlength="255"
                              rows="9"
                    >{{old('description', '')}}</textarea>
                    @error('description')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <label>Image *</label>
                    <div class="dropzone-wrapper">
                        <div class="dropzone-desc">
                            <i class="glyphicon glyphicon-download-alt"></i>
                            <p>Choose an image file or drag it here.</p>
                        </div>
                        <input type="file" class="file dropzone @error('image') is-invalid @enderror"
                           name="image" id="image" accept=".jpg, .jpeg, .png" name="img_logo" required>
                    </div>
                    @error('image')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                    <div class="preview-zone hidden">
                        <div class="box box-solid">
                            <div class="box-header with-border">
                            <div><b>Preview</b></div>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-danger btn-xs remove-preview">
                                <i class="fa fa-times"></i> Reset The Field
                                </button>
                            </div>
                            </div>
                            <div class="box-body"></div>
                        </div>
                    </div>
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
        function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
            var htmlPreview =
                '<img width="100%" src="' + e.target.result + '" />' +
                '<p>' + input.files[0].name + '</p>';
            var wrapperZone = $(input).parent();
            var previewZone = $(input).parent().parent().find('.preview-zone');
            var boxZone = $(input).parent().parent().find('.preview-zone').find('.box').find('.box-body');

            wrapperZone.removeClass('dragover');
            previewZone.removeClass('hidden');
            boxZone.empty();
            boxZone.append(htmlPreview);
            };

            reader.readAsDataURL(input.files[0]);
        }
        }

        function reset(e) {
            e.wrap('<form>').closest('form').get(0).reset();
            e.unwrap();
        }

        $(".dropzone").change(function() {
         readFile(this);
        });

        $('.dropzone-wrapper').on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });

        $('.dropzone-wrapper').on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });

        $('.remove-preview').on('click', function() {
            var boxZone = $(this).parents('.preview-zone').find('.box-body');
            var previewZone = $(this).parents('.preview-zone');
            var dropzone = $(this).parents('.form-group').find('.dropzone');
            boxZone.empty();
            previewZone.addClass('hidden');
            reset(dropzone);
        });
        $('#image').change(function () {
            if (this.files[0] && this.files[0].size > (1024 * 1024 * 2)) {
                this.value = "";
                $('#modalErrorAlert .modal-body').html("Max file size allowed is 2 MB");
                $('#modalErrorAlert').modal();
            }
        });

        $('#btnSubmit').click(function () {
            $('#form').submit(function (e) {
                $('#btnSubmit').attr('disabled', true);
            });
        });
    </script>
@endpush
