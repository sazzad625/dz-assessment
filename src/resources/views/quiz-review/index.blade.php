@extends('layouts.app')

@section('title', 'Quizzes')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tblUsers" class="table table-bordered table-striped">
                            <thead>
                            @foreach($course->quizzes as $quiz)
                                <thead>
                                    <th colspan="5" class="text-center">{{$quiz->name}}</th>
                                </thead>
                                @if(!$quiz->attempts->isEmpty())
                                    <thead>
                                        <th>Id</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Result</th>
                                        <th>Percentage</th>
                                    </thead>
                                    @foreach($quiz->attempts->first()->details as $detail)
                                        <tr>
                                            <td><a href="{{route('quiz.review', $detail->id)}}">{{$detail->id}}</a></td>
                                            <td>{{$detail->start_time}}</td>
                                            <td>{{$detail->end_time}}</td>
                                            <td>{{$detail->result}}</td>
                                            <td>{{$detail->percentage}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">No records found.</td>
                                    </tr>
                                @endif
                            @endforeach
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
@endpush
