@extends('layouts.app')

@section('content')
    <h1>News</h1>

    <ul>
        @foreach($news as $newsItem)
            <li>{{ $newsItem->title }}</li>
        @endforeach
    </ul>
@endsection 