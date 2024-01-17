@extends('layouts.app')

@section('title', 'Edit Role')


@section('content')
<div class="container">
    <div class="row">
        <div class="col-6">
            <h1 class="text-black-40 main-title">Edit Role</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="main-box">
                <form id="form" role="form" method="post">
                    @csrf
                        <div class="row">
                            <div class="col-12">
                                    <label for="name">Name of the Role</label>
                                    <input type="text" class="form-control" id="role_name"
                                           name="role_name"
                                           value="{{old('role_name', $role->name)}}"
                                           disabled
                                    />
                            </div>
                            
                        </div>
                
                    <hr>
                                <table id="tblBookingDetails" class="table table-bordered table-hover">
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
                                            <td>{{$per['id']}}</td>
                                            <td>{{$per['name']}}</td>
                                            <td>
                                                @if(!in_array($per['id'], $rolePermission))
                                                    <input class="@error('permissions') is-invalid @enderror"
                                                           name="permissions[]" type="checkbox"
                                                           value="{{$per['id']}}">
                                                @else
                                                    <input class="@error('permissions') is-invalid @enderror"
                                                           name="permissions[]" type="checkbox"
                                                           value="{{$per['id']}}" checked>
                                                @endif
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
                                        <button type="submit" class="btn btn-primary btn-block"><i></i> Update
                                                Role</button>
                                    </div>
                                </div>
                     </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            let table = $('#tblBookingDetails').DataTable({
                bPaginate: true,
                bInfo: false,
                autoWidth: false,
                responsive: true,
                columnDefs: [
                    {width: "5%", targets: 0}]
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
@endpush
