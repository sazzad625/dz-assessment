@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-black-40 main-title">
            403, {{!empty($exception->getMessage()) ? $exception->getMessage() : 'Forbidden'}}
        </h1>
    </div>
@endsection
