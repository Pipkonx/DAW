@extends('layouts.app')

@section('content')
<div id="app">
    <welcome 
        :can-login="{{ json_encode($canLogin) }}" 
        :can-register="{{ json_encode($canRegister) }}"
    ></welcome>
</div>
@endsection
