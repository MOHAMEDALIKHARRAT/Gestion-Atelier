
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PieceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\PointageController;

// Login
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');


// Routes accessible only after login
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Home page
    Route::get('/', function () {
        return redirect()->route('pieces.index');
    });

    // Manager and employer can view pieces
    Route::get('/pieces', [PieceController::class, 'index'])
        ->name('pieces.index');


    // Manager only
    Route::middleware('manager')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Pieces management
        Route::resource('pieces', PieceController::class)
            ->except(['index', 'show']);

        // Suppliers management
        Route::resource('fournisseurs', FournisseurController::class)
            ->except(['show']);

        // Clients management
        Route::resource('clients', ClientController::class)
            ->except(['show']);

        // Invoices management
        Route::resource('factures', FactureController::class);

        // Payments
        Route::get('/paiements', [PaiementController::class, 'index'])
            ->name('paiements.index');

        Route::get('/factures/{facture}/paiements/create', [PaiementController::class, 'create'])
            ->name('factures.paiements.create');

        Route::post('/factures/{facture}/paiements', [PaiementController::class, 'store'])
            ->name('factures.paiements.store');

        Route::patch('/paiements/{paiement}/statut', [PaiementController::class, 'updateStatut'])
            ->name('paiements.statut');
    });
    Route::get('/employes', [EmployeController::class, 'index'])
    ->name('employes.index');

Route::get('/employes/create', [EmployeController::class, 'create'])
    ->name('employes.create');

Route::post('/employes', [EmployeController::class, 'store'])
    ->name('employes.store');
});
Route::get('/pointages', [PointageController::class, 'index'])
    ->name('pointages.index');

Route::post('/pointages/arriver', [PointageController::class, 'arriver'])
    ->name('pointages.arriver');

Route::post('/pointages/partir', [PointageController::class, 'partir'])
    ->name('pointages.partir');


Route::get('/pointages/{id}/modifier', [PointageController::class, 'edit'])
    ->name('pointages.edit');

Route::put('/pointages/{id}', [PointageController::class, 'update'])
    ->name('pointages.update');



// Automatic payment reminder schedule
Schedule::command('paiements:verifier-echeances')
    ->dailyAt('09:00')
    ->timezone('Africa/Tunis');
