@if(!empty($quiz_id))

        <div class="card" style="width: 15%;">
            <a href="{{ route('export',[$courseId, $quiz_id]) }}" class="btn btn-success pull-left"><i class="fas fa-fw fa-download"></i> Export Excel</a>
        </div>
@endif

<div class="card">
    <div id="table-card"  class="card-body">
        @if(empty($totalAttempts))
            No Record found
        @else
            <table id="tblUsers" class="table table-bordered table-striped" style="width: 100%">
                <thead>
                <tr>
                    <th>Sl</th>
                    <th>Questions</th>
                    <th>Total Attempts</th>
                    <th>Correct Attempts</th>
                    <th>Incorrect Attempts</th>
                </tr>
                </thead>
                <tbody>

                @php
                    $i = 0;
                    $z = 1;
                @endphp
                @foreach($totalAttempts as $rows)
                    <tr>
                        <td>{{ $z++ }}</td>
                        <td>{{ $questions[$i++] }}</td>
                        @foreach($rows as $row)
                            <td>{{ $row }}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@if(!empty($optionsCounts))
    <div class="card">
            @foreach($optionsCounts as $index => $option)
                    <div id="options_percentage_div_{{ $index }}" style="width: 1000px; height: 400px;"></div>

            @endforeach
    </div>
@endif


{{--@push('page_scripts')--}}
<script type="text/javascript">
    $(document).ready(function () {
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            var correctOptions = {!! json_encode($correctOptions) !!};

            @foreach($optionsCounts as $index => $option)
            var questionName = {!! json_encode($option['question_name']) !!};
            var optionCounts = {!! json_encode($option['option_counts']) !!};
            var chartDiv = document.getElementById('options_percentage_div_{{ $index }}');

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Option');
            data.addColumn('number', 'Percentage');
            data.addColumn({type: 'string', role: 'style'});


            for (var optionName in optionCounts) {
                var optionPercentage = parseFloat(optionCounts[optionName]);
                var isCorrectOption = correctOptions[{{ $index }}].includes(optionName);
                var color = isCorrectOption ? 'green' : 'red';

                data.addRow([
                    optionName,
                    optionPercentage,
                    color
                ]);
            }

            var options = {
                title: 'Q:' + questionName,
                width: 1000,
                height: 400,
                theme: 'material',
                legend: { position: 'none' },
                chart: {
                    title: 'Options Percentage',
                    subtitle: 'Options Percentage for each question'
                },
                bars: 'horizontal',
                axes: {
                    x: {
                        0: { side: 'top', label: 'Percentage' }
                    }
                },
                bar: { groupWidth: "50%" }
            };

            var chart = new google.visualization.BarChart(chartDiv);
            chart.draw(data, options);
            @endforeach
        }
    });


</script>
{{--@endpush--}}
