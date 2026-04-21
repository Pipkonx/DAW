@extends('layouts.app')

@section('content')
<div class="container">
    <div id="app">
        <transactions-allocation 
            :portfolios="{{ json_encode($portfolios) }}"
            :selected-portfolio-id="'{{ $selectedPortfolioId }}'"
            :assets-data="{{ json_encode($assets) }}"
        ></transactions-allocation>
    </div>
</div>
@endsection
