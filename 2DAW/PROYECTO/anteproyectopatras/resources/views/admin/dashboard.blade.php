@extends('layouts.app')

@section('content')
<div id="app">
    <admin-dashboard 
        :backups="{{ json_encode($backups) }}"
        :users="{{ json_encode($users) }}"
        :stats="{{ json_encode($stats) }}"
        :api-consumption="{{ json_encode($api_consumption) }}"
        :global-activity="{{ json_encode($global_activity) }}"
        :reports="{{ json_encode($reports) }}"
        :support-tickets-count="{{ $support_tickets_count }}"
    ></admin-dashboard>
</div>
@endsection
