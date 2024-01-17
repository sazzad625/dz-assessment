@extends('layouts.app')
@section('title', 'Add Teacher')
@section('content')
    <div class="container">
        <h1 class="text-black-40 main-title">Add Teacher</h1>
        <form id="form" method="POST">
            @csrf
            <div class="container white-bg">
            <div class="row">
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <label>Employee Id *</label>
                    <input type="text" class="form-control @error('employeeId') is-invalid @enderror"
                           id="employeeId"
                           name="employeeId"
                           maxlength="20"
                           value="{{old('employeeId')}}"

                    />
                    @error('employeeId')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <label>First Name *</label>
                    <input type="text" class="form-control @error('firstName') is-invalid @enderror"
                           id="firstName"
                           name="firstName"
                           maxlength="50"
                           value="{{old('firstName')}}"
                           required
                    />
                    @error('firstName')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <label>Last Name *</label>
                    <input type="text" class="form-control @error('lastName') is-invalid @enderror"
                           id="lastName"
                           name="lastName"
                           maxlength="50"
                           value="{{old('lastName')}}"
                           required
                    />
                    @error('lastName')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <label>User Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           maxlength="60"
                           value="{{old('name')}}"
                           required
                    />
                    @error('name')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <label>Email *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           maxlength="60"
                           value="{{old('email')}}"
                           required
                    />
                    @error('email')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <label>Venture *</label>
                    <select class="form-control" name="country" required>
                        <option name="country" {{old('country', null) == null ? 'selected': ''}} value="">Select
                            Country
                        </option>
                        @foreach(App\Models\Country::all() as $country)
                            <option value="{{$country->id}}" {{old('country') == $country->id ? "selected" : ""}}>
                                {{$country->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('country')
                    <span class="help-block">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <label>Role *</label>
                    <select class="form-control" name="role" required>
                        <option name="role" {{old('role', null) == null ? 'selected': ''}} value="">Select
                            Role
                        </option>
                        @foreach(App\Models\Role::all() as $role)
                            <option value="{{$role->id}}" {{old('role') == $role->id ? "selected" : ""}}>
                                {{$role->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
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
    </script>
@endpush
