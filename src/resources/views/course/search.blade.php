@extends('layouts.app')

@section('title', 'Search Courses')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1 class="text-black-40 main-title">Search Courses</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form role="form" method="get">
                        <div class="card-body col-12">
                            <div class="row">
                                <div class="col-9">
                                        <input type="text" class="form-control" id="name"
                                               name="name" placeholder="Search Course by Name"
                                               value="{{request('name')}}"
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
                        <table id="tblUsers" class="table table-bordered table-hover    ">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created by</th>
                                <th>Participants</th>
                                <th class="text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($search as $row)
                                <tr id="tr-row-{{$row->id}}">
                                    <td>{{$row->name}}</td>
                                    <td>{{$row->createdBy->name}}</td>
                                    <td><a href="{{route('course.participant.search', $row->id)}}" style="color: #1e91cf;">{{$row->users->count()}}</a></td>
                                    <td class="text-right">
                                        @hasPermission(App\Models\Permission::CLONE_COURSE_PERMISSION)
                                        <a href="{{route('course.clone', $row->id)}}"
                                           onclick="return confirm('Are you sure you want to clone this course')"
                                           class="btn btn-info btn-sm" type="button" data-toggle="tooltip"
                                           data-placement="top" title="clone course"><i class="cil-copy"></i> Clone
                                        </a>
                                        @endHasPermission

                                        @hasPermission(App\Models\Permission::COURSE_PERFORMANCE_REPORT_PERMISSION)
                                        <a href="{{route('report.course-performance', $row->id)}}"
                                           class="btn btn-success btn-sm" type="button" data-toggle="tooltip"
                                           data-placement="top" title="Manage content"><i class="cil-pencil"></i> Analytics
                                        </a>
                                        @endHasPermission

                                        @hasPermission(App\Models\Permission::UPDATE_COURSE_PERMISSION)
                                        <a href="{{route('course.update', $row->id)}}"
                                           class="btn btn-primary btn-sm"><i class="fas fa-fw fa-pen fa-sm"></i>
                                            edit
                                        </a>
                                        @endHasPermission
                                        @hasPermission(App\Models\Permission::DELETE_COURSE_PERMISSION)
                                        <a href="#!"
                                           class="btn btn-primary btn-danger btn-sm modal-delete-course"
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
    <div class="modal fade" id="deleteCourse" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                        <input type="hidden" id="modalDeleteCourseId"/>
                        <p>Are you sure you want to delete Course <b><span id="modalDeleteCourseName"></span></b></p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-danger" id="btnSubmitDeleteCourse">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $('.modal-delete-course').click(function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#modalDeleteCourseId').val(id);
            $('#modalDeleteCourseName').html(name);
            $('#deleteCourse').modal('show');
        });

        $('#btnSubmitDeleteCourse').click(function () {
            var id = $('#modalDeleteCourseId').val();
            fetch('{{route('course.delete')}}', {
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
            $('#deleteCourse').modal('hide');
        });
    </script>

@endpush
