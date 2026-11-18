
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Ajouter un client</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                @include('clients.form')
            </form>
        </div>
    </div>
</div>
@endsection
