@extends('layouts.app')

@section('title', 'Modifier un fournisseur')

@section('content')

<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="mb-0">Modifier un fournisseur</h3>
    </div>

    <div class="card-body">
        <form
            action="{{ route('fournisseurs.update', $fournisseur) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            @include('fournisseurs.form')
        </form>
    </div>
</div>

@endsection