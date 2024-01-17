@extends('layouts.app')
@section('title', 'Update Department')
@section('content')
    <div class="container">

        <h1 class="text-black-40 main-title">Update Department</h1>
        <form id="form" method="POST">
            @csrf
            <div class="container white-bg">
            <div class="row">
                <div class="form-group col-12 col-sm-12 col-md-12">
                    <label> Departments Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           maxlength="125"
                           value="{{old('name', $row->name)}}"
                           required
                    />
                    @error('name')
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
@endsection

@push('page_scripts')
    <script>
        $('#btnSubmit').click(function () {
            $('#form').submit(function (e) {
                $('#btnSubmit').attr('disabled', true);
            });
        });
    </script>
@endpush
