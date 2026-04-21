@extends('layouts.app')

@section('content')
<div id="app">
    <asset-show 
        :market-asset="{{ json_encode($marketAsset) }}"
        :chart-data="{{ json_encode($chartData) }}"
        :user-position="{{ json_encode($userPosition) }}"
        :latest-transactions="{{ json_encode($latestTransactions) }}"
        :posts="{{ json_encode($posts) }}"
    ></asset-show>
</div>
@endsection
