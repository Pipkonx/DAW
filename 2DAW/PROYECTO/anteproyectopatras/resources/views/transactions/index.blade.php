@extends('layouts.app')

@section('content')
<div class="container">
    <div id="app">
        <transactions-index 
            :portfolios="{{ json_encode($portfolios) }}"
            :selected-portfolio-id="'{{ $selectedPortfolioId }}'"
            :selected-asset-id="'{{ $selectedAssetId }}'"
            :status-summary="{{ json_encode($status_summary) }}"
            :assets="{{ json_encode($assets) }}"
            :transactions-data="{{ json_encode($transactions) }}"
            :chart-data="{{ json_encode($chart) }}"
            :allocations="{{ json_encode($allocations) }}"
            :filters="{{ json_encode($filters) }}"
            :min-date="'{{ $minDate }}'"
        ></transactions-index>
    </div>
</div>
@endsection
