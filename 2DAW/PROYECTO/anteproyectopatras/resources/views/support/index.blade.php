@extends('layouts.app')

@section('content')
<div id="app">
    <support-index 
        :tickets="{{ json_encode($tickets) }}"
    ></support-index>
</div>
@endsection
