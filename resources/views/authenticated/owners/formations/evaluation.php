@extends('layouts.authenticated.owners.index')
@section("page-title", "évaluation : ".$formation->title)
@section('dashboard-content')

Formation : {{$foramtion->title}}
@if($evaluation)
<h3>Évaluation : {{ $evaluation->title }}</h3>
<p>{{ $evaluation->description }}</p>
<h4>Questions :</h4>
<ul>
    @foreach($questions as $question)
    <li>
        {{ $question->content }}
        <ul>
            @foreach($question->answers as $answer)
            <li>{{ $answer->content }} @if($answer->is_correct) (Correcte) @endif</li>
            @endforeach
        </ul>
    </li>
    @endforeach
</ul>
@endif
@endsection