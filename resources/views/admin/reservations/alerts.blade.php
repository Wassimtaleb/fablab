@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
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
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Gérer les réservations</h2>
            <p class="text-gray-600">Validez, rejetez ou supprimez les réservations des machines du FabLab.</p>
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

        @if(session('danger'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('danger') }}</span>
            </div>
        @endif

        <!-- Filtres -->
        {{-- Les filtres ont été supprimés pour afficher directement les réservations en attente --}}

        <!-- Tableau des réservations -->
        <div class="overflow-x-auto mb-10">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Machine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reservations as $reservation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $reservation->user->nom ?? '-' }} {{ $reservation->user->prenom ?? '' }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $reservation->user->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $reservation->machine->nom ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $reservation->date_reservation ? \Carbon\Carbon::parse($reservation->date_reservation)->format('d/m/Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $reservation->heure_debut }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $reservation->duree }} min</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($reservation->statut === 'validee') bg-green-100 text-green-800
                                    @elseif($reservation->statut === 'refusee') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($reservation->statut) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $reservation->description }}</td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    @if($reservation->statut === 'en_attente')
                                        <form action="{{ route('admin.reservations.validate', $reservation->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-check"></i> Valider
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reservations.reject', $reservation->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-times"></i> Refuser
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Aucune réservation trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Abonnements en attente -->
        {{-- Bloc supprimé : affichage des abonnements en attente --}}

        <!-- Pagination -->
        <div class="mt-4">
            {{ $reservations->links() }}
        </div>
    </div>
</div>
@endsection
