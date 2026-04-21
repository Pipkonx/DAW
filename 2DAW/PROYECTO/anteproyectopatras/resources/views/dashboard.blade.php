@extends('layouts.app')

@section('content')
<div class="container">
    <div id="app">
        <dashboard 
            :summary="{{ json_encode($summary) }}"
            :portfolios="{{ json_encode($portfolios) }}"
            :expenses="{{ json_encode($expenses) }}"
            :charts="{{ json_encode($charts) }}"
            :recent-transactions="{{ json_encode($recentTransactions) }}"
            :all-assets-list="{{ json_encode($allAssetsList) }}"
            :categories="{{ json_encode($categories) }}"
            :unlinked-assets="{{ json_encode($unlinkedAssets) }}"
            :current-filter="'{{ $currentFilter }}'"
            :selected-months="'{{ $selectedMonths }}'"
            :auth="{{ json_encode(['user' => Auth::user()]) }}"
        ></dashboard>
    </div>
</div>
@endsection
