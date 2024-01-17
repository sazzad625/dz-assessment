@if(!$list->isEmpty())
<table class="table">
    <thead>
    <tr>
        <th>Status</th>
        <th>Datetime</th>
        <th>Filters Used</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($list as $row)
        <tr>
            <td> {{ $row['status']}} </td>
            <td> {{ $row['created_at']}} </td>
            <td>{{ $row['filters_used'] }}</td>
            <td>@if($row['status'] == \App\Models\ReportExport::STATUS_COMPLETE)
                    <a class="btn btn-success btn-sm" href="{{$downloadRoute . '/' . $row['id']}}">Download</a>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@else
    No record found
@endif
