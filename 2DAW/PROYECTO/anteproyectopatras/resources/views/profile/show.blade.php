@extends('layouts.app')

@section('content')
<div id="app">
    <profile-show 
        :profile-user="{{ json_encode($profileUser) }}"
        :posts="{{ json_encode($posts) }}"
        :bookmarks="{{ json_encode($bookmarks) }}"
        :is-own-profile="{{ json_encode($isOwnProfile) }}"
        :is-following="{{ json_encode($isFollowing) }}"
        :is-blocked="{{ json_encode($isBlocked) }}"
        :joined-at="'{{ $joined_at }}'"
    ></profile-show>
</div>
@endsection
