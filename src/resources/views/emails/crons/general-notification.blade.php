<div>
    <h4>{{$title}}</h4>
    <div>
        @foreach($messages as $message)
            {!! $message !!} <br/>
        @endforeach
    </div>
</div>
