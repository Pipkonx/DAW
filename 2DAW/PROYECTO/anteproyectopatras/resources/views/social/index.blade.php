@extends('layouts.app')

@section('content')
<div id="app">
    <social-index 
        :posts="{{ json_encode($posts) }}"
        :featured-post="{{ json_encode($featuredPost) }}"
        :filters="{{ json_encode($filters) }}"
        :users-to-follow="{{ json_encode($usersToFollow) }}"
        :trending-assets="{{ json_encode($trendingAssets) }}"
        :famous-portfolios="{{ json_encode($famousPortfolios) }}"
        :user="{{ json_encode(auth()->user()) }}"
    ></social-index>
</div>
@endsection
