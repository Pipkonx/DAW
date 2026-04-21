@extends('layouts.app')

@section('content')
<div id="app">
    <profile-security 
        :activities="{{ json_encode($activities) }}"
        :current-session-id="'{{ $currentSessionId }}'"
        :two-factor-enabled="{{ json_encode($twoFactorEnabled) }}"
    ></profile-security>
</div>
@endsection
