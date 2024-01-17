@extends('layouts.app')
@section('title', 'Course Categories')
@push('page_css')

<link rel="stylesheet" href="//cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"  />
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css"  />
@endpush
@section('content')
    <div class="container">
        <h1 class="text-black-40 main-title">Course Categories</h1>
        @if ($msg = Session::get('success'))
            <div class="alert alert-success">
                <strong>{{ $msg }}</strong>
            </div>
        @elseif ($msg = Session::get('failed'))
            <div class="alert alert-danger">
                <strong>{{ $msg }}</strong>
            </div>
        @endif
        <div class="main-box">
        <table id="datatable" style="width:100%" class="table table-bordered">
              <thead>
                <tr>
                  <th width="20">No.</th>
                  <th>Name</th>
                  <th width="150" class="actions text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($categories as $key=>$category)
                <tr id='tr{{$category->id}}'>
                  <td>{{++$key}}</td>
                  <td>{{$category->name}}</td>
                  <td class="text-right">
                    <ul class="list-inline m-0">
                      @hasPermission(App\Models\Permission::UPDATE_COURSE_CATEGORY_PERMISSION)
                        <li class="list-inline-item">
                            <a href="{{route('category.update', $category->id)}}" class="btn btn-primary btn-sm" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i> Edit</a>
                        </li>
                      @endHasPermission
                      @hasPermission(App\Models\Permission::DELETE_COURSE_CATEGORY_PERMISSION)
                        <li class="list-inline-item">
                            <button class="btn btn-danger btn-sm btn-delete" type="button" data-toggle="tooltip" data-placement="top" title="Delete" data-id="{{$category->id}}"><i class="cil-trash"></i> Delete</button>
                        </li>
                      @endHasPermission
                    </ul>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
        </div>
    </div>
    
    <div class="modal" tabindex="-1" role="dialog" id="deleteConfigModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this category?</p>
                </div>
                <div class="modal-footer">
                    <button id='btnDelete' data-id='' type="button" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@push('page_scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    $(function() {
        $(document).ready(function() {
            $('#datatable').DataTable();
        });
    });
    $('.btn-delete').click(function(){
      var id = $(this).data('id');
      $('#btnDelete').data('id', id);
      $('#deleteConfigModal').modal('show');
    });
    $('#btnDelete').click(function(){
      id = $(this).data('id');
      fetch('{{route("category.delete")}}',{
              method: "POST",
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
              },
              body: JSON.stringify({id: id, _token : '{{ csrf_token() }}'})
            })
            .then(function (response) {
                if (response.ok) {
                    $('#deleteConfigModal').modal('hide');
                    $('#tr'+id).remove();
                }
                else {
                    $('#deleteConfigModal').modal('hide');
                }
                return response.json();
            })
            .then(function (response) {
                alertModal('Success', 'Record deleted');
            }) 
            .catch((error) => {
                console.error('Error:', error);
            });
    }); 
</script>
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" defer></script>
    
@endpush