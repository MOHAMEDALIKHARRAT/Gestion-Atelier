@extends('layouts.app')

@section('title', 'Ajouter un fournisseur')

@section('content')

<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="mb-0">Ajouter un fournisseur</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('fournisseurs.store') }}" method="POST">
            @csrf

            @include('fournisseurs.form')
        </form>
    </div>
</div>

@endsection