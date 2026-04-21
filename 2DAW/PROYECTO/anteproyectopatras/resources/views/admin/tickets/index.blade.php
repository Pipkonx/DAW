@extends('layouts.app')

@section('content')
<div id="app">
    <admin-tickets-index 
        :tickets-data="{{ json_encode($tickets) }}"
        :filters="{{ json_encode($filters) }}"
    ></admin-tickets-index>
</div>
@endsection
