@extends('layouts.app')

@section('content')
<div id="app">
    <profile-edit 
        :must-verify-email="{{ json_encode($mustVerifyEmail) }}"
        :status="'{{ $status }}'"
        :blocked-users="{{ json_encode($blockedUsers) }}"
        :subscription="{{ json_encode($subscription) }}"
    ></profile-edit>
</div>
@endsection
