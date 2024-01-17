@extends('layouts.app')

@section('title', 'Quizzes')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1 class="text-black-40 main-title">Quizzes</h1>
            </div>
            <div class="col-6">
                <a href="{{route('quiz.add', $courseId)}}" class="btn btn-primary float-right">Add New Quiz</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tblUsers" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Grading</th>
                                <th>Date</th>
                                <th>Active</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="{{!$quizzes->isEmpty() ? 'hidden': ''}}">
                                <td colspan="6" class="text-center">No record(s) found.</td>
                            </tr>
                            @foreach($quizzes as $row)
                                <tr id='row-{{$row->id}}'>
                                    <td>{{$row->id}}</td>
                                    <td>{{$row->name}}</td>
                                    <td>{{$row->grading->name}}</td>
                                    <td>{{$row->created_at->toDefaultDayMonthYearFormat()}}</td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input active_switch"
                                                   id="active_{{$row->id}}" {{$row->is_active ? 'checked' : ''}}
                                                   data-quiz-id="{{$row->id}}"
                                                {{!\App\Helpers\AuthHelper::hasPermission(\App\Models\Permission::UPDATE_QUIZ_PERMISSION) ? 'disabled' : ''}}
                                            />
                                            <label class="custom-control-label" for="active_{{$row->id}}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        @hasPermission(App\Models\Permission::UPDATE_QUIZ_PERMISSION)
                                        <a href="{{route('quiz.update', $row->id)}}"
                                           class="btn btn-primary btn-sm"><i class="fas fa-fw fa-pen fa-sm"></i>
                                            edit
                                        </a>
                                        @endHasPermission
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script type="text/javascript">
        (function () {
            $(document).on('change', '.active_switch', async function () {
                let isChecked = $(this).prop('checked');
                $(this).attr('disabled', 'disabled');
                let quizId = $(this).data('quiz-id');
                let uri = `/course/quiz/${quizId}/active-inactive`;

                let request = {
                    method: 'post',
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        "_token": "{{csrf_token()}}",
                        "active": isChecked
                    })
                };

                let response = null;

                try {
                    response = await fetch(uri, request);
                } catch (e) {
                    alertModal('Error', 'Unable to reach server.');
                    $(this).prop('checked', !isChecked)
                    $(this).removeAttr('disabled');
                    return;
                }

                if (response.ok) {
                    alertModal('Success', 'Quiz updated.');
                    $(this).removeAttr('disabled');
                } else {
                    $(this).prop('checked', !isChecked);
                    $(this).removeAttr('disabled');
                    alertModal('Error', 'Unable to update quiz.');
                }
            });
        })();
    </script>
@endpush
