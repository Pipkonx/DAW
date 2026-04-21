@extends('layouts.app')

@section('content')
<div id="app">
    <famous-portfolio-show 
        :profile="{{ json_encode($profile) }}"
        :holdings="{{ json_encode($holdings) }}"
        :history="{{ json_encode($history) }}"
        :is-following="{{ json_encode($isFollowing) }}"
        :stats="{{ json_encode($stats) }}"
    ></famous-portfolio-show>
</div>
@endsection
