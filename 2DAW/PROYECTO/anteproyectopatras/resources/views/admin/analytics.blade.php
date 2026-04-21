@extends('layouts.app')

@section('content')
<div id="app">
    <admin-analytics 
        :metrics="{{ json_encode($metrics) }}"
    ></admin-analytics>
</div>
@endsection
