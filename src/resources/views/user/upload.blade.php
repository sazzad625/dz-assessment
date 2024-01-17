@extends('layouts.app')
@section('title', 'Upload')

@section('content')
    <div class="container">

        <h1 class="text-black-40 main-title">Upload  {{ucfirst(strtolower($type)) . 's'}}</h1>
        <form id="form" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="form-group col-12 col-sm-12 col-md-12">
                    <div class="dropzone-wrapper custom-file">
                        <div class="dropzone-desc">
                        <i class="cil-cloud-upload"></i>
                            <p>You can drag and drop files here to add them.</p>
                        </div>
                        <input type="file" class="custom-file-input file dropzone @error('image') is-invalid @enderror"
                           name="file" id="file" accept=".csv" name="img_logo">
                    </div>
                    @error('name')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                    <a href="{{route('user.upload.template.download')}}">Click here</a> to download the Template file
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-2 col-sm-12">
                    <label>&nbsp;</label>
                    <button type="submit" id="btnSubmit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            @if ($msg = Session::get('success'))
            <div class="alert alert-success">
                <strong>{{ $msg }}</strong>
            </div>
          @endif

          @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
          @endif
        </form>
    </div>
@endsection

@push('page_scripts')
<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        if (this.files[0] && this.files[0].size > (1024 * 1024 * 2)) {
            this.value = "";
            alertModal('Error', 'Max file size allowed is 2 MB');
        }
        else if(!fileName.toLowerCase().endsWith(".csv")){
            this.value = "";
            alertModal('Error', 'only CSV file allowed');
        }
        else{
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        }
    });
</script>
@endpush
