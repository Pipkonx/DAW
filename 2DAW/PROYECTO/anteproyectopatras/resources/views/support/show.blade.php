@extends('layouts.app')

@section('content')
<div id="app">
    <support-show 
        :ticket="{{ json_encode($ticket) }}"
        :auth-user-id="{{ auth()->id() }}"
    ></support-show>
</div>
@endsection
