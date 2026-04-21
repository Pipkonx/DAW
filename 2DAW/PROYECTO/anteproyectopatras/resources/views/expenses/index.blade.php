@extends('layouts.app')

@section('content')
<div id="app">
    <expenses-index 
        :filters="{{ json_encode($filters) }}"
        :summary="{{ json_encode($summary) }}"
        :charts="{{ json_encode($charts) }}"
        :transactions-data="{{ json_encode($transactions) }}"
        :portfolios="{{ json_encode($portfolios) }}"
        :categories="{{ json_encode($categories) }}"
        :top-expenses="{{ json_encode($topExpenses) }}"
        :top-income="{{ json_encode($topIncome) }}"
        :min-date="'{{ $min_date }}'"
    ></expenses-index>
</div>
@endsection
