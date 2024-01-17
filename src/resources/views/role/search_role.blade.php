@extends('layouts.app')

@section('title', 'Role Management')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-6">
            <h1 class="text-black-40 main-title">Role Management</h1>
        </div>
        @hasPermission(App\Models\Permission::CREATE_ROLE_PERMISSION)
            <div class="col-6">
                <a href="{{route('role.create')}}" class="btn btn-primary float-sm-right"><i
                            class="fas fa-fw fa-plus"></i> Create</a>
            </div>
        @endHasPermission
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form role="form" action="{{route('role.view')}}" method="get">
                    <div class="card-body col-12">
                        <div class="row">
                            <div class="col-9">
                                    <input type="text" class="form-control" id="name"
                                           name="name" placeholder="Search Role By Name"
                                           value="{{old('name')}}"
                                    />
                                    <div id="roleList"></div>
                            </div>
                            <div class="col-3">
                                    <button type="submit" class="btn btn-primary btn-block"><i
                                                class="fas fa-1x fa-fw fa-search"></i> Search</button>
                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="tblUsers" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Role</th>
                            <th class="text-right">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{$role->name}}</td>
                                <td class="text-right">
                                    @hasPermission(App\Models\Permission::UPDATE_ROLE_PERMISSION)
                                        <a href="{{route('role.update', $role->id)}}" class="btn btn-primary btn-sm"><i
                                                    class="fas fa-fw fa-pen fa-sm"></i> Edit</a>
                                    @endHasPermission
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $roles->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            let table = $('#tblUsers').DataTable({
            });
        });

        $('#name').keyup(function () {
            let query = $(this).val();
            if ((query !== '')) {
                $.ajax({
                    url: "{{route('role.fetchRoles')}}",
                    method: "GET",
                    data: {query: query},
                    success: function (data) {
                        $('#roleList').fadeIn();
                        $('#roleList').html(data);
                    }
                });
            }
        });

        $(document).on('click', '#roleList li', function(){
            $('#name').val($(this).text());
            $('#roleList').fadeOut();
        });
    </script>
@endpush
