@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête de la section -->
        <div class="mb-6">
            <div class="flex items-center space-x-4 mb-3">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Tableau de bord
                </a>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Créer une réservation</h2>
            <p class="text-gray-600">Créez une nouvelle réservation pour un utilisateur.</p>
        </div>

        <!-- Messages Flash -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire de création de réservation -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <form action="{{ route('admin.reservations.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Sélection de l'utilisateur -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Utilisateur</label>
                        <select name="user_id" id="user_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">Sélectionner un utilisateur</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" 
                                    {{ !$user->has_active_subscription ? 'disabled' : '' }}
                                    class="{{ !$user->has_active_subscription ? 'text-gray-400' : '' }}">
                                    {{ $user->nom }} {{ $user->prenom }} ({{ $user->email }})
                                    @if(!$user->has_active_subscription)
                                        - Pas d'abonnement actif
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Les utilisateurs sans abonnement actif sont grisés et ne peuvent pas être sélectionnés.</p>
                    </div>
                    
                    <!-- Sélection de la machine -->
                    <div>
                        <label for="machine_id" class="block text-sm font-medium text-gray-700 mb-1">Machine</label>
                        <select name="machine_id" id="machine_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">Sélectionner une machine</option>
                            @foreach($machines as $machine)
                                <option value="{{ $machine->id }}" 
                                    {{ $machine->statut !== 'disponible' ? 'disabled' : '' }}
                                    class="{{ $machine->statut !== 'disponible' ? 'text-gray-400' : '' }}">
                                    {{ $machine->nom }}
                                    @if($machine->statut === 'disponible')
                                        (Disponible)
                                    @elseif($machine->statut === 'en_maintenance')
                                        (En maintenance)
                                    @elseif($machine->statut === 'hors_service')
                                        (Hors service)
                                    @else
                                        (Non disponible)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Seules les machines disponibles peuvent être réservées. Les autres sont grisées et non sélectionnables.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date de réservation -->
                        <div>
                            <label for="date_reservation" class="block text-sm font-medium text-gray-700 mb-1">Date de réservation</label>
                            <input type="date" name="date_reservation" id="date_reservation" required min="{{ date('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        
                        <!-- Heure de début -->
                        <div>
                            <label for="heure_debut" class="block text-sm font-medium text-gray-700 mb-1">Heure de début</label>
                            <input type="time" name="heure_debut" id="heure_debut" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>
                    
                    <!-- Durée -->
                    <div>
                        <label for="duree" class="block text-sm font-medium text-gray-700 mb-1">Durée (en heures)</label>
                        <input type="number" name="duree" id="duree" required min="1" max="6" step="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <p class="text-sm text-gray-500 mt-1">La durée minimale est de 1 heure et la durée maximale est de 6 heures.</p>
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                    </div>
                    
                    <!-- Statut -->
                    <div>
                        <input type="hidden" name="statut" value="validee">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">Les réservations créées par un administrateur sont automatiquement validées.</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Créer la réservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
