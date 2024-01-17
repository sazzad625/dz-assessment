@extends('layouts.app')

@section('title', 'Individual Performance Report')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1 class="text-black-40 main-title">Individual Performance Report</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(empty($user))
                            No Record found
                        @else
                            <h2 class="text-dark">{{$user[0]['name']}}</h2>
                            <table id="tblUsers" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Course Name</th>
                                    <th>Grade</th>
                                    <th>Course Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($user as $row)
                                    <tr>
                                        <td>{{$row['courses']}}</td>
                                        <td>{{!empty($row['percentage'])?$row['percentage']:'N/A'}}</td>
                                        <td>{{$row['status']}}</td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" href="{{route('quiz.review.list', [$row['course_id'], $row['user_id']])}}">
                                                <i class="fas fa-fw fa-eye"></i>
                                                Quiz Review
                                            </a>
                                        </td>
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
@endsection

