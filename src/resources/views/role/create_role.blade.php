@extends('layouts.app')

@section('title', 'Create Role')
@push('page_css')

<link rel="stylesheet" href="//cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"  />
@endpush
@section('content')
<div class="container">
    <div class="row">
        <div class="col-6">
            <h1 class="text-black-40 main-title">Create Role</h1>
        </div>
    </div>
    <div class="main-box">
        <form id="form" role="form" action="{{route('role.create')}}" method="post">
            @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="name">Name of the Role</label>
                            <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="role_name"
                                    name="name" placeholder="Enter a new role name"
                                    required="required"
                                    value="{{old('role_name')}}"
                            />
                            @error('name')
                            <span class="help-block">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <hr>
                        <table id="tblBookingDetails" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Permission Name</th>
                                <th>Access</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($permissions as $per)
                                <tr>
                                    <td>{{$per->id}}</td>
                                    <td>{{$per->name}}</td>
                                    <td>
                                        <input id="per{{$per->id}}"
                                                class="@error('permissions') is-invalid @enderror"
                                                name="permissions[]" type="checkbox"
                                                value="{{$per->id}}">
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            @error('permissions')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </table>
                        
                    <div class="row">
                        
                    <div class="col-3"> 
                        <div class="form-group">
                            <label for="">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block"><i></i> Create
                                    Role
                            </button>
                        </div>
                    </div>
                    </div>
        </form>
    </div>
</div>
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            let table = $('#tblBookingDetails').DataTable({
            });

            $('#form').on('submit', function (e) {
                var form = $(this);

                // Iterate over all checkboxes in the table
                table.$('input[type="checkbox"]').each(function () {
                    // If checkbox doesn't exist in DOM
                    if (!$.contains(document, this)) {
                        // If checkbox is checked
                        if (this.checked) {
                            // Create a hidden element
                            form.append(
                                $('<input>')
                                    .attr('type', 'hidden')
                                    .attr('name', this.name)
                                    .val(this.value)
                            );
                        }
                    }
                });
            });
        });
    </script>
    
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" defer></script>
@endpush
