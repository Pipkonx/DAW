@extends('layouts.app')

@section('content')
<div id="app">
    <admin-reports 
        :reports-data="{{ json_encode($reports) }}"
    ></admin-reports>
</div>
@endsection
