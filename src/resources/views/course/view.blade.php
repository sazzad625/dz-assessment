@extends('layouts.app')
@section('title', 'Course')
@section('content')
    <style>
        .modal-backdrop.show {
            display: none;
        }

        .modal-open .modal:before {
            content: '';
            background: #000;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: .5;
        }
    </style>
    <div class="row top-heading">
        <div class="container">

            <h2>Welcome to Course {{ $course->name }}</h2>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col col-8 course-content">
                {!! $htmlContent !!}
            </div>
            <div class="col col-4 ">
                <div class="attachments attachment-doc">
                    <h2>Attached Document</h2>
                    @if (!empty($attachments))
                        @foreach ($attachments as $attachment)
                            @if ($attachment['ext'] == 'pdf' || $attachment['ext'] == 'txt')
                                <a href="#file-{{$attachment['id']}}" data-toggle="modal">{{$attachment['name']}}</a>
                                <div class="modal fade" id="file-{{$attachment['id']}}" tabindex="1" role="dialog"
                                     aria-labelledby="exampleModalLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="exampleModalLabel">{{$attachment['name']}}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <iframe src="{{$attachment['path']}}#toolbar=0" width="100%"
                                                            height="500px"></iframe>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="#file-{{$attachment['id']}}" data-toggle="modal">{{$attachment['name']}}</a>
                                <div class="modal fade" id="file-{{$attachment['id']}}" tabindex="-1" role="dialog"
                                     aria-labelledby="exampleModalLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="exampleModalLabel">{{$attachment['name']}}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <iframe
                                                        src='https://view.officeapps.live.com/op/embed.aspx?src={{urlencode($attachment['path'])}}'
                                                        width="100%" height="650px"></iframe>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Quiz</h2>
        <div class="row">
            <div class="col-sm-12">
                <table id="tblUsers" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Quiz name</th>
                        <th>Attempted / Total Attempt</th>
                        <th>Time limit</th>
                        <th>Grades</th>
                        <th>Result</th>
                        <th>Actions</th>
                        <th>Results</th>
                    </tr>
                    </thead>

                    {{--                    @dd($quizzes)--}}
                    <tbody>
                    @foreach($quizzes as $quiz)
                            <?php $attemptsCount = $quiz->attempt ? $quiz->attempt->total_attempts : 0 ?>
                        <tr>
                            <td>
                                {{$quiz->name}}
                            </td>
                            <td>
                                {{$attemptsCount}}
                                /
                                {{$quiz->attempts_allowed}}
                            </td>
                            <td>
                                @if($quiz->time_limit)
                                    {{$quiz->time_limit}} minutes
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $quiz->attempt->grading_percentage ?? '0' }}</td>
                            <td>
                                @if($quiz->attempt != null)
                                    {{ $quiz->attempt->grading_final_result == 'PASS' ? 'Passed' : 'Failed' }}
                                @else
                                    {{ $quiz->attempt->grading_final_result ?? 'Unattempted' }}
                                @endif
                            </td>
                            <td>
                            @if($attemptsCount < $quiz->attempts_allowed && \Illuminate\Support\Facades\Auth::user()->isStudent())
                                <button type="button" class="btn btn-sm btn-primary confirmQuizAttempt"
                                   data-quiz-id="{{$quiz->id}}"
                                   data-quiz-title="{{$quiz->name}}"
                                >Attempt</button>
                                @endif
                                </td>
                                <td>
                                    @if(\Illuminate\Support\Facades\Auth::user()->isStudent())
                                        {{--                                    <a href="#!" class="btn btn-sm btn-primary"--}}
                                        {{--                                       data-quiz-option="{{$quiz->quiz}}"--}}
                                        {{--                                    >Results</a>--}}

                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#resultQuizModal{{$quiz->id}}">
                                            Results
                                        </button>
                                    @endif
                                </td>

                                <div class="modal fade" id="resultQuizModal{{$quiz->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Results from the last attempt<span
                                                        id="quizResultModal"></span></h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        <div class="modal-body">
                                            <ul>
                                                @if(empty($quiz->quiz->quizResult))
                                                    No attempts has been taken
                                                @else
                                                    @foreach($quiz->quiz->quizResult as $row)
                                                        <li>
                                                            {{ $row['question_name'] }}
                                                            <span class="{{ $row['result'] === 'Incorrect' ? 'text-danger' : 'text-success' }}">
                                                            {{ $row['result'] }}
                                                                </span>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <div class="modal fade" id="attemptQuizModal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="quizTitleModal"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <input type="hidden"/>
                            <p>Your quiz is about to start, please click on the start button to continue?</p>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-success" id="btnInitiateQuiz">
                            Start
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function () {
            let currentQuizId = 0;
            $('.confirmQuizAttempt').click(function () {
                currentQuizId = $(this).data('quiz-id');
                $("#quizTitleModal").html($(this).data('quiz-title'));
                $("#attemptQuizModal").modal('show');
            });

            $('#btnInitiateQuiz').click(async function () {
                let btn = $(this);
                btn.prop('disabled', true);
                let serverUrl = "/quiz/" + currentQuizId + "/initiate";

                let request = {
                    method: "post",
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        _token: "{{csrf_token()}}"
                    })
                };

                let response;

                try {
                    response = await fetch(serverUrl, request);
                } catch (e) {
                    $("#attemptQuizModal").modal('hide');
                    alertModal('Error', 'Unable to reach server');
                    btn.prop('disabled', false);
                    return;
                }

                let responseJson = await response.json();

                if (!response.ok) {
                    $("#attemptQuizModal").modal('show');
                    alertModal("Error", responseJson.message);
                    btn.prop('disabled', false);
                    return;
                }

                window.location = responseJson.link;
            });
        });

    </script>
@endpush
