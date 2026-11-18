@extends('layouts.app')

@section('title', 'Ajouter une pièce')

@section('content')

<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="mb-0">Ajouter une pièce</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('pieces.store') }}" method="POST">
            @csrf

            @include('pieces.form')
        </form>
    </div>
</div>

@endsection