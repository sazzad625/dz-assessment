@extends('layouts.app')

@section('title', 'Individual Performance Report')

@section('content')
    <style>

        div#downloadFileList {
            max-height: 150px;
            overflow-y: scroll;
            overflow-x: hidden;
        }

        div#downloadFileList th {
            background: #f8f8f8;
            border-bottom: 2px solid #e6e6e6;
            text-transform: capitalize;
        }

        div#downloadFileList td {
            border: 1px solid #e6e6e6;
            color: #333;
        }

        button#btnReload {
            text-transform: uppercase;
            float: right;
            margin-top: -39px;
            margin-bottom: 10px;
        }

        .report-box {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e6e6e6;
            box-sizing: border-box;
        }

        .report-box h2 {
            font-size: 20px;
            margin-bottom: 13px;
        }

        .pull-right {
            float: right !important;
        }

        .btn-success.disabled, .btn-success:disabled {
            color: #fff;
            background-color: #999;
            border-color: #999;
            cursor: not-allowed;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1 class="text-black-40 main-title">Individual Performance Report</h1>
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
                                        <label for="name">User name</label>
                                        <input type="text" class="form-control" id="name"
                                               name="name" placeholder="Enter user name"
                                               value="{{request('name')}}"
                                        />
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="name">Employee Id</label>
                                        <input type="text" class="form-control" id="employeeId"
                                               name="employeeId" placeholder="Enter Employee Id"
                                               value="{{request('employeeId')}}"
                                        />
                                    </div>
                                </div>
                                <div class="col">
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
                                <div class="col">
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
                                <div class="col-3">
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
        <div class="row justify-content-end" >
            <div class="col-12">
                <div class="report-box">
                <h2>Export Files</h2>
                <button id="btnReload" class="btn btn-primary btn-sm"><i class="cil-reload"></i></button>
                <div id="downloadFileList">
                    @if(!$downloadFiles->isEmpty())
                        @include('layouts.partials.download-list', ['list' => $downloadFiles,
                                    'downloadRoute' => route('report.course-performance.download','')] )
                    @endif
                </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-2">
                <button disabled='true' class="btn btn-success pull-right" id='btnExport'>
                    <i class="fas fa-fw fa-download"></i> Export Excel
                </button>
                <button class="btn btn-success pull-right" id='btnExportAll' style="margin-right: 5px;">
                        <i class="fas fa-fw fa-download"></i> Export All
                </button>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tblUsers" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><input type="checkbox" class="select-checkbox-head"></th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>User Name</th>
                                <th>Employee ID</th>
                                <th>Venture</th>
                                <th>Department</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($search as $row)
                                <tr id="tr-row-{{$row->id}}" data-student-id="{{$row->id}}">
                                    <td><input type="checkbox" class="select-checkbox td-checkbox"></td>
                                    <td>{{$row->first_name}}</td>
                                    <td>{{$row->last_name}}</td>
                                    <td><a href="{{route('report.individual-student-performance', $row->id)}}"
                                           title="Performance Report">{{$row->name}}</a></td>
                                    <td>{{$row->employee_id}}</td>
                                    <td>{{$row->country->name}}</td>
                                    <td>{{$row->department->name}}</td>
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
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $('.select-checkbox-head').click(function () {
            if ($(this).prop("checked") == true) {
                $("#btnExport").prop('disabled', false);
                $('.select-checkbox').prop('checked', true);
            } else {
                $("#btnExport").prop('disabled', true);
                $('.select-checkbox').prop('checked', false);
            }
        });

        $('.select-checkbox').click(function () {
            var count = 0
            $('.td-checkbox:checked').each(function () {
                count++;
            });
            if (count > 0) {
                $("#btnExport").prop('disabled', false);
            } else {
                $("#btnExport").prop('disabled', true);
            }
        });

        $('#btnExport').click(function () {
            let id = [];
            var count = 0
            $('.td-checkbox:checked').each(function () {
                var tr = $(this).closest('tr');
                var studentId = $(tr).data('student-id');
                id.push(studentId);
                count++;
            });
            if (count > 0) {
                sendRequest(id);
            }
        });

        function sendRequest(id) {
            fetch('{{route("report.individual-performance.generate-file")}}', {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({id: id, _token: '{{ csrf_token() }}'})
            })
                .then(function (response) {
                    if (response.ok) {
                        response.json().then(response => {
                            alertModal('Success', response.message);
                        })

                    } else {
                        alertModal('Error', 'Request Failed');
                    }
                })
        }

        @php
            $param = request()->post();
            $param['_token'] = csrf_token();
        @endphp
        $('#btnExportAll').click(function () {
            fetch('{{route("report.individual-performance.generate-file-for-all-records")}}', {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({!!json_encode($param)!!})
            })
                .then(function (response) {
                    if (response.ok) {
                        response.json().then(response => {
                            alertModal('Success', response.message);
                        })

                    } else {
                        alertModal('Error', 'Request Failed');
                    }
                })
        })

        $('#btnReload').click(function () {
            fetch('{{route("report.individual-performance.get-generate-file")}}')
                .then(function (response) {
                    if (response.ok) {
                        response.text().then(response => {
                            $('#downloadFileList').html(response);
                        })

                    } else {
                        alertModal('Error', 'Error Occurred');
                    }
                })
        })
    </script>
@endpush
