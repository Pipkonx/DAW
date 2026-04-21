@extends('layouts.app')

@section('content')
<div id="app">
    <categories-index 
        :categories="{{ json_encode($categories) }}"
    ></categories-index>
</div>
@endsection
