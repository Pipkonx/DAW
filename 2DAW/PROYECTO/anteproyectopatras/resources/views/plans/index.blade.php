@extends('layouts.app')

@section('content')
<div id="app">
    <plans-index 
        :plans="{{ json_encode($plans) }}"
        :intent="{{ json_encode($intent) }}"
    ></plans-index>
</div>
@endsection
