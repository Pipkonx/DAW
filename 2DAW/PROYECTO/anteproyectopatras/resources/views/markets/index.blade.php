@extends('layouts.app')

@section('content')
<div id="app">
    <markets-index 
        :stocks="{{ json_encode($stocks) }}"
        :crypto="{{ json_encode($crypto) }}"
        :etfs="{{ json_encode($etfs) }}"
        :funds="{{ json_encode($funds) }}"
    ></markets-index>
</div>
@endsection
