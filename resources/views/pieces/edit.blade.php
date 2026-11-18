@extends('layouts.app')

@section('title', 'Modifier une pièce')

@section('content')

<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="mb-0">Modifier une pièce</h3>
    </div>

    <div class="card-body">
        <form
            action="{{ route('pieces.update', $piece) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            @include('pieces.form')
        </form>
    </div>
</div>

@endsection