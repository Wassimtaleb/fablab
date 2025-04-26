<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TechnicienController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\AbonnementController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\Technicien\MaintenanceController as TechnicienMaintenanceController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;
use App\Http\Middleware\TechnicienMiddleware;
use App\Http\Controllers\FactureController;

// Routes de test
Route::get('/test', function () {
    return 'Ceci est une route de test.';
});

Route::get('/test-admin', function () {
    return 'Vous êtes un admin.';
})->middleware(\App\Http\Middleware\AdminMiddleware::class);

// Route de débogage pour l'authentification admin
Route::get('/debug-admin', function () {
    $isAdminAuthenticated = \Illuminate\Support\Facades\Auth::guard('admin')->check();
    $userType = session('user_type');
    $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
    
    return [
        'is_admin_authenticated' => $isAdminAuthenticated,
        'user_type' => $userType,
        'admin' => $admin ? [
            'id' => $admin->id,
            'nom' => $admin->nom,
            'prenom' => $admin->prenom,
            'email' => $admin->email,
        ] : null,
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
    ];
});

// Route de test pour l'authentification admin
Route::get('/test-admin-auth', function () {
    // Créer un admin de test
    $admin = \App\Models\Admin::first();
    
    if (!$admin) {
        return 'Aucun admin trouvé dans la base de données.';
    }
    
    // Connecter l'admin
    \Illuminate\Support\Facades\Auth::guard('admin')->login($admin);
    session(['user_type' => 'admin']);
    
    // Vérifier si l'authentification a réussi
    $isAuthenticated = \Illuminate\Support\Facades\Auth::guard('admin')->check();
    $userType = session('user_type');
    
    return [
        'admin' => [
            'id' => $admin->id,
            'nom' => $admin->nom,
            'prenom' => $admin->prenom,
            'email' => $admin->email,
        ],
        'is_authenticated' => $isAuthenticated,
        'user_type' => $userType,
        'session_id' => session()->getId(),
    ];
});

// ROUTES DE TEST DEBUG
Route::get('/debug-finances', function () {
    return 'debug finances route OK';
});

// Pages de login pour chaque profil
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login']);

Route::get('/user/login', [UserController::class, 'showLoginForm'])->name('user.login');
Route::post('/user/login', [UserController::class, 'login']);

Route::get('/technicien/login', [TechnicienController::class, 'showLoginForm'])->name('technicien.login');
Route::post('/technicien/login', [TechnicienController::class, 'login']);

// Routes publiques
Route::get('/', [AuthController::class, 'home'])->name('home');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

// Routes pour l'inscription des utilisateurs
Route::get('/user/signup', [SignupController::class, 'showSignupForm'])->name('user.signup');
Route::post('/user/signup', [SignupController::class, 'signup'])->name('user.signup.store');

// Routes pour les utilisateurs standard
Route::prefix('user')->name('user.')->group(function () {
    // Routes protégées par le middleware UserMiddleware
    Route::middleware([UserMiddleware::class])->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        
        // Abonnements
        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('/', [AbonnementController::class, 'showForm'])->name('form');
            Route::post('/subscribe', [AbonnementController::class, 'subscribe'])->name('subscribe');
            Route::get('/status', [AbonnementController::class, 'status'])->name('status');
            Route::post('/cancel', [AbonnementController::class, 'cancel'])->name('cancel');
            Route::get('/facture/{id}', [FactureController::class, 'generateFacture'])->name('facture.generate');
        });

        // Machines
        Route::get('/machines', [MachineController::class, 'userIndex'])->name('machines.index');
        Route::get('/machines/{machine}', [MachineController::class, 'show'])->name('machines.show');
        
        // Réservations
        Route::prefix('reservations')->name('reservations.')->group(function () {
            Route::get('/my', [ReservationController::class, 'userReservations'])->name('my');
            Route::get('/create/{machine}', [ReservationController::class, 'create'])->name('create');
            Route::post('/store', [ReservationController::class, 'store'])->name('store');
            Route::get('/ticket/{id}', [\App\Http\Controllers\TicketController::class, 'generateTicket'])->name('ticket');
        });
    });
});

// Rediriger /admin vers la page de login admin
Route::redirect('/admin', '/admin/login');

// Routes pour les administrateurs
Route::prefix('admin')->name('admin.')->group(function () {
    // Routes d'enregistrement (accessibles sans authentification)
    Route::get('/register', [AdminController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AdminController::class, 'register']);
    
    // Routes protégées par le middleware AdminMiddleware
    Route::middleware([AdminMiddleware::class])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Gestion des abonnements
        Route::get('/abonnements', [AbonnementController::class, 'index'])->name('abonnements.index');
        Route::put('/abonnements/{abonnement}', [AbonnementController::class, 'update'])->name('abonnements.update');
        Route::delete('/abonnements/{abonnement}', [AbonnementController::class, 'destroy'])->name('abonnements.destroy');
        Route::get('/abonnements/config', [AbonnementController::class, 'showConfig'])->name('abonnements.config');
        Route::post('/abonnements/config', [AbonnementController::class, 'updateConfig'])->name('abonnements.config.update');
        Route::get('/abonnements/create-type', [AbonnementController::class, 'createType'])->name('abonnements.create-type');
        Route::post('/abonnements/store-type', [AbonnementController::class, 'storeType'])->name('abonnements.store-type');
        Route::delete('/abonnements/delete-type/{id}', [AbonnementController::class, 'deleteType'])->name('abonnements.delete-type');
        Route::post('/abonnements/{abonnement}/validate', [AbonnementController::class, 'validate'])->name('abonnements.validate');
        Route::post('/abonnements/{abonnement}/reject', [AbonnementController::class, 'reject'])->name('abonnements.reject');
        Route::get('/abonnements/active', [AbonnementController::class, 'activeSubscriptions'])->name('abonnements.active');
        Route::get('/abonnements/payments', [AbonnementController::class, 'paymentHistory'])->name('abonnements.payments');
        
        // Gestion des admins
        Route::get('/create', [AdminController::class, 'create'])->name('create');
        Route::post('/store', [AdminController::class, 'store'])->name('store');
        Route::get('/edit', [AdminController::class, 'edit'])->name('edit');
        Route::put('/update/{admin}', [AdminController::class, 'update'])->name('update');
        Route::get('/delete', [AdminController::class, 'delete'])->name('delete');
        Route::delete('/destroy', [AdminController::class, 'destroy'])->name('destroy');

        // Gestion des utilisateurs
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users/store', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/update/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::get('/users/delete', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::delete('/users/destroy', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // Gestion des techniciens
        Route::get('/techniciens/create', [AdminController::class, 'createTech'])->name('techniciens.create');
        Route::post('/techniciens/store', [AdminController::class, 'storeTech'])->name('techniciens.store');
        Route::get('/techniciens/edit', [AdminController::class, 'editTech'])->name('techniciens.edit');
        Route::put('/techniciens/update/{technicien}', [AdminController::class, 'updateTech'])->name('techniciens.update');
        Route::get('/techniciens/delete', [AdminController::class, 'deleteTech'])->name('techniciens.delete');
        Route::delete('/techniciens/destroy', [AdminController::class, 'destroyTech'])->name('techniciens.destroy');

        // Gestion des machines
        Route::get('/machines/create', [MachineController::class, 'create'])->name('machines.create');
        Route::post('/machines/store', [MachineController::class, 'store'])->name('machines.store');
        Route::get('/machines', [MachineController::class, 'index'])->name('machines.index');
        Route::get('/machines/manage', [MachineController::class, 'manage'])->name('machines.manage');
        Route::get('/machines/{machine}/edit', [MachineController::class, 'edit'])->name('machines.edit');
        Route::put('/machines/{machine}', [MachineController::class, 'update'])->name('machines.update');
        Route::delete('/machines/{machine}', [MachineController::class, 'destroy'])->name('machines.destroy');
        
        // Gestion des réservations
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/alerts', [ReservationController::class, 'alerts'])->name('reservations.alerts');
        Route::get('/reservations/create', [ReservationController::class, 'createAdmin'])->name('reservations.create');
        Route::get('/reservations/calendar', [ReservationController::class, 'calendar'])->name('reservations.calendar');
        Route::get('/reservations/{id}/details', [ReservationController::class, 'getDetails'])->name('reservations.details');
        Route::post('/reservations/store', [ReservationController::class, 'storeAdmin'])->name('reservations.store');
        Route::post('/reservations/{id}/validate', [ReservationController::class, 'validateReservation'])->name('reservations.validate');
        Route::post('/reservations/{id}/reject', [ReservationController::class, 'reject'])->name('reservations.reject');
        Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

        // Gestion des maintenances
        Route::get('/maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
        Route::get('/maintenances/create', [MaintenanceController::class, 'create'])->name('maintenances.create');
        Route::post('/maintenances', [MaintenanceController::class, 'store'])->name('maintenances.store');
        Route::get('/maintenances/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenances.show');
        Route::get('/maintenances/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenances.edit');
        Route::put('/maintenances/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenances.update');
        Route::delete('/maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenances.destroy');

        // Gestion des messages
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
        Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

        // Rapport financier
        Route::get('/debug-admin-finances', function () {
            return 'debug admin finances route OK';
        });
        Route::get('/finances', [AdminController::class, 'finances'])->name('finances');

        // Statistiques d'utilisation
        Route::get('/statistiques', [AdminController::class, 'statistiques'])->name('statistiques');

        // AJAX endpoint for admin calendar: fetch reservations by date
        Route::get('/reservations/by-date/{date}', [\App\Http\Controllers\ReservationController::class, 'getByDate'])
            ->name('admin.reservations.byDate')
            ->middleware('auth:admin');
    });
});

// Export CSV pour chaque type de données
Route::get('/admin/export/tout', [App\Http\Controllers\AdminController::class, 'exportToutCsv'])->name('admin.export.tout');
Route::get('/admin/export/pdf', [App\Http\Controllers\AdminController::class, 'exportToutPdf'])->name('admin.export.toutpdf');

Route::middleware(['auth', 'admin'])->group(function() {
    Route::get('/admin/export/abonnements', [App\Http\Controllers\AdminController::class, 'exportAbonnementsCsv'])->name('admin.export.abonnements');
    Route::get('/admin/export/paiements', [App\Http\Controllers\AdminController::class, 'exportPaiementsCsv'])->name('admin.export.paiements');
    Route::get('/admin/export/reservations', [App\Http\Controllers\AdminController::class, 'exportReservationsCsv'])->name('admin.export.reservations');
    Route::get('/admin/export/machines', [App\Http\Controllers\AdminController::class, 'exportMachinesCsv'])->name('admin.export.machines');
    Route::get('/admin/export/statistiques', [App\Http\Controllers\AdminController::class, 'exportStatistiquesCsv'])->name('admin.export.statistiques');
    Route::get('/admin/export/finances', [App\Http\Controllers\AdminController::class, 'exportFinancesCsv'])->name('admin.export.finances');
});

// Routes pour les techniciens
Route::prefix('technicien')->name('technicien.')->group(function () {
    // Routes protégées par le middleware TechnicienMiddleware
    Route::middleware([TechnicienMiddleware::class])->group(function () {
        Route::get('/dashboard', [TechnicienController::class, 'dashboard'])->name('dashboard');
        
        // Gestion des maintenances
        Route::get('/maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
        Route::get('/maintenances/create', [MaintenanceController::class, 'create'])->name('maintenances.create');
        Route::post('/maintenances', [MaintenanceController::class, 'store'])->name('maintenances.store');
        Route::get('/maintenances/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenances.show');
        Route::get('/maintenances/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenances.edit');
        Route::put('/maintenances/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenances.update');
        Route::delete('/maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenances.destroy');

        // Gestion des messages
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
        Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        
        // Nouvelle gestion simplifiée des maintenances
        Route::post('/machines/{machine}/maintenance', [App\Http\Controllers\Technicien\MachineMaintenanceController::class, 'mettreEnMaintenance']);
        Route::post('/maintenances/{maintenance}/terminer-maintenance', [App\Http\Controllers\Technicien\MachineMaintenanceController::class, 'terminerMaintenance']);
        
        // Gestion des maintenances technicien
        Route::get('/maintenances-technicien', [TechnicienMaintenanceController::class, 'index'])->name('maintenances-technicien.index');
        Route::get('/maintenances-technicien/create', [TechnicienMaintenanceController::class, 'create'])->name('maintenances-technicien.create');
        Route::post('/maintenances-technicien', [TechnicienMaintenanceController::class, 'store'])->name('maintenances-technicien.store');
        Route::get('/maintenances-technicien/{maintenance}', [TechnicienMaintenanceController::class, 'show'])->name('maintenances-technicien.show');
        Route::post('/maintenances-technicien/{maintenance}/status', [TechnicienMaintenanceController::class, 'updateStatus'])->name('maintenances-technicien.update-status');
        Route::post('/maintenances-technicien/{maintenance}/comments', [TechnicienMaintenanceController::class, 'addComment'])->name('maintenances-technicien.add-comment');
    });
});

// Routes pour les abonnements utilisateur
Route::middleware(['auth'])->group(function () {
    Route::get('/abonnements/status', [AbonnementController::class, 'status'])->name('abonnements.status');
    Route::get('/abonnements/form', [AbonnementController::class, 'showForm'])->name('abonnements.form');
    Route::post('/abonnements/subscribe', [AbonnementController::class, 'subscribe'])->name('abonnements.subscribe');
    Route::post('/abonnements/cancel', [AbonnementController::class, 'cancel'])->name('abonnements.cancel');
});

// Routes pour les réservations
Route::middleware(['auth'])->group(function () {
    Route::get('/reservations/my', [ReservationController::class, 'myReservations'])->name('reservations.my');
    Route::get('/machines/{machine}/reserve', [ReservationController::class, 'create'])->name('user.machines.reserve');
    Route::post('/machines/{machine}/reserve', [ReservationController::class, 'store'])->name('user.machines.reserve.store');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'cancel'])->name('reservations.cancel');
});

// Route pour prévisualiser l'email de validation d'abonnement (uniquement en développement)
if (app()->environment('local')) {
    Route::get('/mailable/abonnement-valide', function () {
        $abonnement = \App\Models\Abonnement::first();
        if (!$abonnement) {
            return 'Aucun abonnement trouvé dans la base de données.';
        }
        return new \App\Mail\AbonnementValide($abonnement);
    });

    Route::put('/admin/abonnements/{id}/valider', [AbonnementController::class, 'valider'])->name('admin.abonnements.valider');

}
