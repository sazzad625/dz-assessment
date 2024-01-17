@extends('layouts.app')

@section('title', 'Search Courses Participant')

@section('content')
<style>
        .btn-primary {
            padding: 5px 24px;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1 class="text-black-40 main-title">Search Course Participant</h1>
            </div>
            <div class="col-6">
                <a href="#!" class="btn btn-primary btn-sm modal-enroll-participant float-right ml-2">
                    <i class="fas fa-fw fa-user-plus fa-sm"></i>
                    Enroll
                </a>
                <a href="#!" class="btn btn-primary btn-sm modal-upload-participant float-right">
                    <i class="fas fa-fw fa-user-plus fa-sm"></i>
                    Upload
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body col-12">
                        <form role="form" method="get">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="employeeId">Employee id</label>
                                        <input type="text" class="form-control" id="employeeId"
                                               name="employeeId" placeholder="Enter employee id"
                                               value="{{request('employeeId')}}"
                                        />
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="wfmId">WFM id</label>
                                        <input type="text" class="form-control" id="wfmId"
                                               name="wfmId" placeholder="Enter WFM id"
                                               value="{{request('wfmId')}}"
                                        />
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name"
                                               name="name" placeholder="Enter Participant name"
                                               value="{{request('name')}}"
                                        />
                                        <div id="roleList"></div>
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
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tblUsers" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Employee id</th>
                                <th>WFM id</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Venture</th>
                                <th>Department</th>
                                <th title="Last access to course date">Last access</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($search as $row)
                                <tr id="tr-row-{{$row->id}}">
                                    <td>{{$row->employee_id}}</td>
                                    <td>{{$row->wfm_id}}</td>
                                    <td>{{$row->first_name . ' ' . $row->last_name}}</td>
                                    <td><a href="#!">{{$row->name}}</a></td>
                                    <td>{{$row->venture}}</td>
                                    <td>{{$row->department}}</td>
                                    <td>{{$row->lastAccess}}</td>
                                    <td>
                                        <li class="list-inline-item">
                                            <button class="btn btn-danger btn-sm btn-remove" type="button"
                                                    data-id="{{$row->id}}"
                                                    data-name="{{$row->name}}"
                                                    data-toggle="tooltip" data-placement="top" title="Remove">
                                                <i class="fas fa-fw fa-user-times fa-sm"></i> Remove
                                            </button>
                                        </li>

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
    <div class="modal fade" id="enrollParticipant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">User Enroll</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <select class="js-example-data-ajax form-control"></select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <button type="button" id="btn-enroll" class="btn btn-success">Enroll</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="uploadParticipant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form id="form" method="POST" action="{{route('course.participant.upload', request('id'))}}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="file" class="custom-file-input" name="file" id="customFile"
                                               accept=".csv">
                                        <label class="custom-file-label" for="customFile">Choose file for Upload</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-success btn-block"><i
                                            class="fas fa-1x fa-fw fa-upload "></i> <b>Upload</b></button>
                                </div>
                            </div>
                            <div class="row">
                                <a href="{{route('course.participant.template')}}">Click here</a>
                                &nbsp;to download the template file
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="removeParticipant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                        <input type="hidden" id="modalRemoveParticipantId"/>
                        <p>Are you sure you want to remove participant <b><span id="modalRemoveParticipantName"></span></b>
                        </p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-danger " id="btnSubmitRemoveParticipant">Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('page_scripts')
    <link href="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <script type="text/javascript">

        $(document).ready(function () {
            $(".js-example-data-ajax").select2({
                multiple: true,
                minimumInputLength: 3,
                placeholder: 'Search User',
                ajax: {
                    url: '{{route('user.search.ajax', [strtolower(App\Models\User::USER_TYPE_STUDENT)])}}',
                    dataType: "json",
                    type: "GET",
                    data: function (params) {
                        return {
                            q: params.term,
                            courseId: {{request('id')}},
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: data.items,
                        };
                    },
                },
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });
        });

        function formatRepo(repo) {
            if (repo.loading) {
                return repo.text;
            }

            var $container = $(
                "<option>" + repo.name + "</option>"
            );

            return $container;
        }

        function formatRepoSelection(repo) {
            $(".js-example-data-ajax").prop('disabled', true);
            return repo.name;
        }

        $('.modal-enroll-participant').click(function () {
            $('#enrollParticipant').modal('show');
        });

        $('.btn-remove').click(function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#modalRemoveParticipantId').val(id);
            $('#modalRemoveParticipantName').html(name);
            $('#removeParticipant').modal('show');
        });

        $('#btnSubmitRemoveParticipant').click(function () {
            $(this).attr('disabled', true);
            var id = $('#modalRemoveParticipantId').val();
            fetch('{{route('course.participant.remove', request('id'))}}', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json',
                    'accept': 'application/json'
                },
                body: JSON.stringify({
                    userId: id,
                    _token: "{{ csrf_token() }}",
                })
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw 'error';
                    }
                    $('#tr-row-' + id).remove();
                    alertModal('Success', 'Record Remove');
                })
                .catch(function (ex) {
                    console.log(ex);
                    alertModal('error', 'Unable to Remove');
                    $('#btnSubmitRemoveParticipant').attr('disabled', false);
                });
            $('#removeParticipant').modal('hide');
        });

        $('#btn-enroll').click(function () {
            $(this).attr('disabled', true);
            $('#enrollParticipant').modal('hide');
            var id = $('.js-example-data-ajax').val();
            fetch('{{route('course.participant.enroll', request('id'))}}', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json',
                    'accept': 'application/json'
                },
                body: JSON.stringify({
                    userId: id,
                    _token: "{{ csrf_token() }}",
                })
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw 'error';
                    }
                    alertModal('Success', 'Successfully Enroll');
                    location.reload(true);
                })
                .catch(function (ex) {
                    console.log(ex);
                    alertModal('error', 'Unable to Enroll');
                    $('#btn-enroll').attr('disabled', false);
                    $('.js-example-data-ajax').val('');
                });
        });

        $('.modal-upload-participant').click(function () {
            $('#uploadParticipant').modal('show');
        });

        $(".custom-file-input").on("change", function () {
            var fileName = $(this).val().split("\\").pop();
            if (this.files[0] && this.files[0].size > (1024 * 1024 * 2)) {
                this.value = "";
                alertModal('Error', 'Max file size allowed is 2 MB');
            } else if (!fileName.toLowerCase().endsWith(".csv")) {
                this.value = "";
                alertModal('Error', 'only CSV file allowed');
            } else {
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            }
        });
    </script>
@endpush
