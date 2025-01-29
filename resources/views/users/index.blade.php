@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Usuarios</h1>
    <ul>
        @foreach ($users as $user)
            <li>{{ $user->name }} ({{ $user->username }})</li>
        @endforeach
    </ul>
</div>
@endsection