@extends('layouts.app')

@section('title', 'Search Student')

@section('content')
    <style>
        .btn-primary {
            padding: 5px 24px;
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1 class="text-black-40 main-title">Search Student</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form role="form" method="get">
                        <div class="card-body col-12">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="name">Employee id</label>
                                        <input type="text" class="form-control" id="employeeId"
                                               name="employeeId" placeholder="Enter employee id"
                                               value="{{request('employeeId')}}"
                                        />
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="name">WFM id</label>
                                        <input type="text" class="form-control" id="wfmId"
                                               name="wfmId" placeholder="Enter WFM id"
                                               value="{{request('wfmId')}}"
                                        />
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="name">User name</label>
                                        <input type="text" class="form-control" id="name"
                                               name="name" placeholder="Enter user name"
                                               value="{{request('name')}}"
                                        />
                                    </div>
                                </div>
                                <div class="form-group col">
                                    <label>Venture</label>
                                    <select class="form-control" name="country">
                                        <option name="country"
                                                {{request('country', null) == null ? 'selected': ''}} value="">Select
                                            Venture
                                        </option>
                                        @foreach(App\Models\Country::all() as $country)
                                            <option
                                                value="{{$country->id}}" {{request('country') == $country->id ? "selected" : ""}}>
                                                {{$country->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country')
                                    <span class="help-block">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="form-group col">
                                    <label>Department</label>
                                    <select class="form-control" name="department">
                                        <option name="department"
                                                {{request('department', null) == null ? 'selected': ''}} value="">Select
                                            Department
                                        </option>
                                        @foreach(App\Models\Department::all() as $department)
                                            <option
                                                value="{{$department->id}}" {{request('department') == $department->id ? "selected" : ""}}>
                                                {{$department->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department')
                                    <span class="help-block">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block"><i
                                                class="fas fa-1x fa-fw fa-search"></i> Search</button>
                                    </div>
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
                                <th>Employee Id</th>
                                <th>WFM Id</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>User Name</th>
                                <th>Venture</th>
                                <th>Department</th>
                                <th class="text-right" width="30%">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($search as $row)
                                <tr id="tr-row-{{$row->id}}">
                                    <td>{{$row->employee_id}}</td>
                                    <td>{{$row->wfm_id}}</td>
                                    <td>{{$row->first_name}}</td>
                                    <td>{{$row->last_name}}</td>
                                    <td><a href="#" title="Performance Report">{{$row->name}}</a></td>
                                    <td>{{$row->country->name}}</td>
                                    <td>{{$row->department->name}}</td>
                                    <td style="width: 320px;" class="text-right">
                                        @hasPermission(App\Models\Permission::UPDATE_STUDENT_PERMISSION)
                                        <a href="{{route('user.update', [strtolower(App\Models\User::USER_TYPE_STUDENT), $row->id])}}"
                                           class="btn btn-primary btn-sm"><i class="fas fa-fw fa-pen fa-sm"></i>
                                            edit
                                        </a>
                                        @endHasPermission
                                        @hasPermission(App\Models\Permission::DELETE_STUDENT_PERMISSION)
                                        <a href="#!"
                                           class="btn btn-primary btn-danger btn-sm modal-delete-student"
                                           data-id="{{$row->id}}"
                                           data-name="{{$row->name}}"
                                        >
                                            <i class="fas fa-fw fa-trash fa-sm"></i>
                                            delete
                                        </a>
                                        @endHasPermission
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $search->appends(request()->all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteStudent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="modalDeleteStudentId"/>
                        <p>Are you sure you want to delete student <b><span id="modalDeleteStudentName"></span></b></p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-danger" id="btnSubmitDeleteStudent">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $('.modal-delete-student').click(function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#modalDeleteStudentId').val(id);
            $('#modalDeleteStudentName').html(name);
            $('#deleteStudent').modal('show');
        });

        $('#btnSubmitDeleteStudent').click(function () {
            var id = $('#modalDeleteStudentId').val();
            fetch('{{route('user.delete', strtolower(App\Models\User::USER_TYPE_STUDENT))}}', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json',
                    'accept': 'application/json'
                },
                body: JSON.stringify({
                    id: id,
                    _token: "{{ csrf_token() }}",
                })
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw 'error';
                    }
                    $('#tr-row-' + id).remove();
                    alertModal('Success', 'Record deleted');
                })
                .catch(function (ex) {
                    console.log(ex);
                    alertModal('error', 'Unable to delete');
                });
            $('#deleteStudent').modal('hide');
        });
    </script>
@endpush
