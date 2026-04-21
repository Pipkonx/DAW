@extends('layouts.app')

@section('content')
<div id="app">
    <ai-analyst-index 
        :analyses="{{ json_encode($analyses) }}"
        :has-investments="{{ json_encode($has_investments) }}"
        :user-name="'{{ $user_name }}'"
    ></ai-analyst-index>
</div>
@endsection
