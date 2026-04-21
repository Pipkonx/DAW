@extends('layouts.app')

@section('content')
<div id="app">
    <financial-planning-index 
        :bank-accounts="{{ json_encode($bankAccounts) }}"
        :projections="{{ json_encode($projections) }}"
        :aggregated="{{ json_encode($aggregated) }}"
        :settings="{{ json_encode($settings) }}"
    ></financial-planning-index>
</div>
@endsection
