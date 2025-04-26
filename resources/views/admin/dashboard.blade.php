<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - FabLab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- En-tête -->
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">FabLab Admin</h1>
            <div class="flex items-center gap-4">
                @if($admin)
                <span class="hidden md:inline">{{ $admin->nom }} {{ $admin->prenom }}</span>
                <div class="relative group">
                    <button class="flex items-center gap-2 focus:outline-none">
                        <i class="fas fa-user-shield text-xl"></i>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded-md text-sm flex items-center">
                        <i class="fas fa-sign-out-alt mr-1"></i> Déconnexion
                    </button>
                </form>
                @else
                <p>Admin non connecté</p>
                @endif
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <div class="container mx-auto p-4">
        @if(isset($admin))
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Bienvenue, {{ $admin->nom }} {{ $admin->prenom }}</h2>
            <p class="text-gray-600">Tableau de bord d'administration du FabLab</p>
        </div>

        <!-- Cartes de statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Utilisateurs</p>
                        <p class="text-lg font-semibold">{{ $stats['users'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Machines</p>
                        <p class="text-lg font-semibold">{{ $stats['machines'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Réservations validées</p>
                        <p class="text-lg font-semibold">{{ $stats['reservations_validees'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Abonnements actifs</p>
                        <p class="text-lg font-semibold">{{ $stats['abonnements_actifs'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bloc supprimé : statistiques d'utilisation et bouton vers la page dédiée --}}

        <!-- Modules principaux -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Gestion des accès -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-blue-600 text-white p-4">
                    <h3 class="font-bold text-lg"><i class="fas fa-user-lock mr-2"></i>Gestion des accès</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                            <span>Administrateurs</span>
                            <div>
                                <a href="{{ route('admin.create') }}" class="text-blue-500 hover:text-blue-700 mr-2" title="Créer"><i class="fas fa-plus-circle"></i></a>
                                <a href="{{ route('admin.edit') }}" class="text-yellow-500 hover:text-yellow-700 mr-2" title="Modifier"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('admin.delete') }}" class="text-red-500 hover:text-red-700" title="Supprimer"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                            <span>Utilisateurs</span>
                            <div>
                                <a href="{{ route('admin.users.create') }}" class="text-blue-500 hover:text-blue-700 mr-2" title="Créer"><i class="fas fa-plus-circle"></i></a>
                                <a href="{{ route('admin.users.edit') }}" class="text-yellow-500 hover:text-yellow-700 mr-2" title="Modifier"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('admin.users.delete') }}" class="text-red-500 hover:text-red-700" title="Supprimer"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                            <span>Techniciens</span>
                            <div>
                                <a href="{{ route('admin.techniciens.create') }}" class="text-blue-500 hover:text-blue-700 mr-2" title="Créer"><i class="fas fa-plus-circle"></i></a>
                                <a href="{{ route('admin.techniciens.edit') }}" class="text-yellow-500 hover:text-yellow-700 mr-2" title="Modifier"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('admin.techniciens.delete') }}" class="text-red-500 hover:text-red-700" title="Supprimer"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gestion des machines -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-green-600 text-white p-4">
                    <h3 class="font-bold text-lg"><i class="fas fa-tools mr-2"></i>Gestion des machines</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        <a href="{{ route('admin.machines.create') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-plus-circle mr-2 text-green-500"></i>Ajouter une machine
                        </a>
                        <a href="{{ route('admin.machines.index') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-list mr-2 text-green-500"></i>Liste des machines
                        </a>
                        <a href="{{ route('admin.machines.manage') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-cogs mr-2 text-green-500"></i>Gérer les machines
                        </a>
                    </div>
                </div>
            </div>

            <!-- Réservations et alertes -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-yellow-500 text-white p-4">
                    <h3 class="font-bold text-lg"><i class="fas fa-calendar-alt mr-2"></i>Gestion des réservations</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        <a href="{{ route('admin.reservations.index') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-calendar-check mr-2 text-yellow-500"></i>Voir les réservations 
                            @if($stats['reservations_validees'] > 0)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $stats['reservations_validees'] }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.reservations.alerts') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-tasks mr-2 text-yellow-500"></i>Demandes en attente 
                            @if($stats['reservations_en_attente'] > 0)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $stats['reservations_en_attente'] }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.reservations.create') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-calendar-plus mr-2 text-yellow-500"></i>Créer une réservation
                        </a>
                        <a href="{{ route('admin.reservations.calendar') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-calendar-alt mr-2 text-yellow-500"></i>Calendrier 
                        </a>
                    </div>
                </div>
            </div>

    

            <!-- Abonnements et paiements -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-purple-600 text-white p-4">
                    <h3 class="font-bold text-lg"><i class="fas fa-credit-card mr-2"></i>Gestion des abonnements </h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        <a href="{{ route('admin.abonnements.config') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-cog mr-2 text-purple-500"></i>Gérer les forfaits
                        </a>
                        <a href="{{ route('admin.abonnements.index') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-clock mr-2 text-purple-500"></i>Demandes en attente
                            @if($stats['abonnements_en_attente'] > 0)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $stats['abonnements_en_attente'] }}
                                </span>
                            @endif
                        </a>
                       
                        <a href="{{ route('admin.abonnements.active') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-users-cog mr-2 text-purple-500"></i>Abonnements utilisateurs
                            @if($stats['abonnements_actifs'] > 0)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $stats['abonnements_actifs'] }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.abonnements.payments') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-file-invoice-dollar mr-2 text-purple-500"></i>Historique des paiements
                        </a>
                    </div>
                </div>
            </div>
           
            <!-- Reporting -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-indigo-600 text-white p-4">
                    <h3 class="font-bold text-lg"><i class="fas fa-chart-pie mr-2"></i>Reporting</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        <a href="{{ route('admin.statistiques') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-chart-bar mr-2 text-indigo-500"></i>Statistiques d'utilisation
                        </a>
                        <a href="{{ route('admin.finances') }}" class="block p-2 hover:bg-gray-50 rounded">
                            <i class="fas fa-coins mr-2 text-indigo-500"></i>Rapport financier
                        </a>
                        <a href="{{ route('admin.export.toutpdf') }}" class="block p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <i class="fas fa-download mr-2 text-indigo-500"></i>Exporter les données (PDF)
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <i class="fas fa-exclamation-circle text-yellow-500 text-4xl mb-4"></i>
            <p class="text-lg text-gray-700">Veuillez vous connecter pour accéder au tableau de bord.</p>
            <a href="{{ url('/') }}" class="mt-4 inline-block bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Se connecter</a>
        </div>
        @endif
    </div>

    <!-- Footer -->
    
</body>
</html>