
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gestion Atelier')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6fb;
            color: #17233d;
            font-family: Arial, sans-serif;
        }

        .topbar {
            background: #14213d;
        }

        .brand-mark {
            background: #315fbd;
            padding: 8px;
            border-radius: 8px;
            margin-right: 8px;
        }

        .nav-link {
            color: white !important;
        }

        .page-wrap {
            max-width: 1250px;
        }

        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 5px 22px #17233d0a;
        }

        .btn {
            border-radius: 8px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark topbar">
    <div class="container page-wrap">

        <a class="navbar-brand fw-bold" href="{{ route('pieces.index') }}">
            <span class="brand-mark">⚙</span>
            Gestion Atelier
        </a>

        @auth

        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">

            <div class="navbar-nav me-auto">

                <a class="nav-link" href="{{ route('pieces.index') }}">
                    Pièces
                </a>

                @if(auth()->user()->isManager())

                    <a class="nav-link" href="{{ route('fournisseurs.index') }}">
                        Fournisseurs
                    </a>

                    <a class="nav-link" href="{{ route('clients.index') }}">
                        Clients
                    </a>

                    <a class="nav-link" href="{{ route('factures.index') }}">
                        Factures
                    </a>

                    <a class="nav-link" href="{{ route('paiements.index') }}">
                        Paiements
                    </a>
                    <a href="{{ route('pointages.index') }}">
    Pointage des employés
</a>
<a href="{{ route('dashboard')}}">
    Pointage des employés
</a>

                @endif

            </div>

            <div class="d-flex align-items-center gap-3 text-white">

                <span>
                    {{ auth()->user()->name }}
                    -
                    {{ auth()->user()->role === 'manager' ? 'Manager' : 'Employé' }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm">
                        Déconnexion
                    </button>
                </form>

            </div>

        </div>

        @endauth

    </div>
</nav>

<main class="container page-wrap py-4">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())

        <div class="alert alert-danger">
            <strong>Vérifiez les informations :</strong>

            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif

    @yield('content')

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
