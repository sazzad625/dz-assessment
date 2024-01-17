@extends('layouts.app')

@section('title', 'Course Performance')

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
        #table-card{
            overflow: auto;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1 class="text-black-40 main-title">Course Performance Report</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body col-12">
                        <div class="row">
                            <div class="col-12">
                                <select id="selectCourse" class="form-control">
                                    <option id="">Please Select Course</option>
                                    @foreach(\App\Models\Course::all() as $course)
                                        <option
                                            value="{{$course->id}}" {{$course->id == request()->courseId ? 'selected':''}}>{{$course->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(isset($search))
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
                                    <div class="form-group col">
                                        <label>Venture</label>
                                        <select class="form-control" name="country">
                                            <option name="country"
                                                    {{request('country', null) == null ? 'selected': ''}} value="">
                                                Select
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
                                                    {{request('department', null) == null ? 'selected': ''}} value="">
                                                Select
                                                Department
                                            </option>
                                            @foreach(App\Models\Department::all() as $department)
                                                <option
                                                    value="{{$department->id}}" {{request('department') == $department->id ? "selected" : ""}}>
                                                    {{$department->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block"><i
                                                    class="fas fa-1x fa-fw fa-search"></i> <b>Search</b></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row justify-content-end">
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

                    @if(count($search)>0)
                        <button class="btn btn-success pull-right" id='btnExportAll' style="margin-right: 5px;">
                            <i class="fas fa-fw fa-download"></i> Export All
                        </button>
                    @else
                        <button disabled='true' class="btn btn-success pull-right" id='btnExportAll' style="margin-right: 5px;">
                            <i class="fas fa-fw fa-download"></i> Export All
                        </button>
                    @endif

                </div>
                <div class="col-12">
                    <div class="card">
                        <div id="table-card"  class="card-body">
                        @if(empty($search))
                            No Record found
                        @else
                            <table id="tblUsers" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" class="select-checkbox-head"></th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>User Name</th>
                                    <th>Employee ID</th>
                                    <th>Department</th>
                                    <th>Venture</th>
                                    @foreach($columns as $column)
                                    <th title="{{$column->name}}">{{substr($column->name,0,15)}}</th>
                                    @endforeach
                                    <th>Avg%</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($search as $row)
                                    <tr id="tr-row-{{$row['id']}}" data-student-id="{{$row['id']}}">
                                        <td><input type="checkbox" class="select-checkbox td-checkbox"></td>
                                        <td>{{$row['first_name']}}</td>
                                        <td>{{$row['last_name']}}</td>
                                        <td>{{$row['userName']}}</td>
                                        <td>{{$row['employee_id']}}</td>
                                        <td>{{$row['department']}}</td>
                                        <td>{{$row['venture']}}</td>
                                        @foreach($columns as $column)
                                            <td>{{isset($row[$column['name']]) ? $row[$column['name']]: 'N/A'}}</td>
                                        @endforeach
                                        <td>{{(!empty($row['totalPercentage']) ?
                                                    round($row['totalPercentage']/count($columns), 2): 0)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $pagination->appends(request()->all())->links() }}
                        @endif
                        </div>
                    </div>
                </div>

                {{--Quiz Report Section--}}
                <div class="col-12">
                        <h1 class="text-black-40 main-title">Quiz Performance Report</h1>
                        <hr>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="">
                            <div class="">
                                <div id="table-card"  class="card-body">
                                    @if(empty($quizPercentageAvg))
                                        No Record found
                                    @else
                                        <table id="tblUsers" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Quiz Name</th>
                                                <th>Avg attempts taken to pass</th>
                                                <th>Avg passing grade (%)</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($quizPercentageAvg as $row)
                                                <tr>
                                                    <td>{{ $row->quiz->name ?? ''}}</td>
                                                    <td>{{ $row->avg_attempt ?? ''}}</td>
                                                    <td>{{ $row->avg_grade ?? ''}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Quiz Report Section Close--}}

                {{--                Updated Dynamic Code start --}}
                <div class="col-12">
                    <div class="">
                        <div class="row">
                            <div class="form-group col">
                                <label>Select a Quiz</label>
                                <select id="quizSelect" class="form-control">
                                    <option name="quiz_id"
                                            {{request('quiz_id', null) == null ? 'selected': ''}} value="">
                                        Select
                                        Quiz
                                    </option>
                                    @foreach(\App\Models\Quiz::where('fk_course_id',request()->courseId)->get() as $quiz)
                                        <option
                                            value="{{$quiz->id}}" {{request('quiz_id') == $quiz->id ? "selected" : ""}}>
                                            {{$quiz->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="">
                        <div id="quizFileList">
                            {{--                            @isset($totalAttempts, $questions, $optionsCounts, $correctOptions)--}}
                            {{--                                @include('layouts.partials.quiz-list', [--}}
                            {{--//                                    'totalAttempts' => $totalAttempts,--}}
                            {{--//                                    'questions' => $questions,--}}
                            {{--//                                    'optionsCounts' => $optionsCounts,--}}
                            {{--//                                    'correctOptions' => $correctOptions,--}}
                            {{--                                ])--}}
                            {{--                            @endisset--}}
                        </div>
                    </div>
                </div>
                {{--                Updated Dynamic Code End --}}

            </div>
        @endif
    </div>
@endsection

@push('page_scripts')
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        $('#selectCourse').change(function () {
            let url = '{{route('report.course-performance', '')}}';
            document.location.href = url + '/' + $(this).val();
        });

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
            fetch('{{route("report.course-performance.generate-file")}}', {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ids: id, {{!empty(request()->courseId) ? 'courseId:'.request()->courseId.',': ''}} _token: '{{ csrf_token() }}', filters_used:'{{ $filter }}'  })
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
            $param['courseId'] = request()->courseId;
            $param['_token'] = csrf_token();
            $param['filters_used'] = $filter;
        @endphp
        $('#btnExportAll').click(function () {
            fetch('{{route("report.course-performance.generate-file-for-all-records")}}', {
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
        });

        $('#btnReload').click(function () {
            fetch('{{route("report.course-performance.get-generate-file")}}')
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

        $('#quizSelect').on('change', function() {
            var selectedQuizValue = $(this).val();
            var courseId = {{ request()->courseId }};

            fetch(`/report/course-performance-report/get-quiz-analytics/${courseId}/${selectedQuizValue}`)
                .then(function(response) {
                    if (response.ok) {
                        response.text().then(response => {
                            $('#quizFileList').html(response);
                        });
                    } else {
                        alertModal('Error', 'Error Occurred');
                    }
                });
        });
    </script>
{{--    <script type="text/javascript">--}}
{{--        $(document).ready(function () {--}}
{{--            google.charts.load('current', {'packages':['corechart']});--}}
{{--            google.charts.setOnLoadCallback(drawStuff);--}}

{{--            function drawStuff() {--}}
{{--                var correctOptions = {!! json_encode($correctOptions) !!};--}}
{{--                console.log(correctOptions);--}}
{{--                var data = new google.visualization.arrayToDataTable([--}}
{{--                    ['', '', { role: 'style' }],--}}

{{--                    //Updated Code--}}
{{--                        @php $i = -1;@endphp--}}
{{--                        @foreach($optionsCounts as $option)--}}
{{--                        @php $i++;  @endphp--}}
{{--                        @php $questionName = $option['question_name'] @endphp--}}
{{--                    ['Q# {{ $questionName }}', null, 'red'],--}}
{{--                        @foreach($option['option_counts'] as $optionName => $optionPercentage)--}}
{{--                        @php $isCorrectOption = in_array($optionName, $correctOptions[$i]); @endphp--}}
{{--                    ["{!! html_entity_decode(strlen($optionName) > 20 ? substr($optionName, 0, 20). '..': $optionName) !!}", parseFloat('{{ $optionPercentage }}'), '{{ $isCorrectOption ? "green" : "red" }}'],--}}
{{--                    @endforeach--}}
{{--                    @endforeach--}}

{{--                ]);--}}

{{--                var options = {--}}
{{--                    title: 'Options Percentage',--}}
{{--                    width: '1000',--}}
{{--                    theme: 'material',--}}
{{--                    legend: { position: 'none' },--}}
{{--                    chart: { title: 'Options Percentage',--}}
{{--                        subtitle: 'Options Percentage for each question ' },--}}
{{--                    bars: 'horizontal',--}}
{{--                    axes: {--}}
{{--                        x: {--}}
{{--                            0: { side: 'top', label: 'Percentage'} // Top x-axis.--}}
{{--                        }--}}
{{--                    },--}}
{{--                    bar: { groupWidth: "50%" }--}}
{{--                };--}}
{{--                var chart = new google.visualization.BarChart(document.getElementById('options_percentage_div'));--}}
{{--                chart.draw(data, options);--}}
{{--            };--}}
{{--        })--}}
{{--    </script>--}}

@endpush
