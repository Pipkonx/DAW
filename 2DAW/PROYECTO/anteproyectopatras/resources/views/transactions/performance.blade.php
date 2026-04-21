@extends('layouts.app')

@section('content')
<div class="container">
    <div id="app">
        <transactions-performance 
            :portfolios="{{ json_encode($portfolios) }}"
            :selected-portfolio-id="'{{ $selectedPortfolioId }}'"
            :annual-data="{{ json_encode($annual) }}"
            :monthly-data="{{ json_encode($monthly) }}"
            :heatmap-data="{{ json_encode($heatmap) }}"
            :detailed-data="{{ json_encode($detailed) }}"
            :view-type="'{{ $viewType }}'"
        ></transactions-performance>
    </div>
</div>
@endsection
