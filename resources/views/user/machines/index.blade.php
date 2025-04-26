@extends('layouts.app')

@section('content')
<!-- Modal pour l'image -->
<div id="imageModal" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-75 flex items-center justify-center p-4">
    <div class="relative max-w-4xl w-full">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-xl font-bold">&times;</button>
        <img id="modalImage" src="" alt="" class="w-full rounded-lg">
    </div>
</div>

<script>
function openModal(imageSrc, altText) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalImage').alt = altText;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Fermer la modal si on clique en dehors de l'image
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Fermer la modal avec la touche Echap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Machines disponibles</h2>
                <p class="mt-2 text-sm text-gray-600">Sélectionnez une machine pour effectuer une réservation.</p>
            </div>
            <a href="{{ route('user.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Retour au tableau de bord
            </a>
        </div>

        @if(session('success'))
            <div class="mb-8 bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($machines->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <p class="text-gray-500">Aucune machine n'est disponible pour le moment.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($machines as $machine)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <img src="/images/machines/{{ strtolower(str_replace(' ', '-', $machine->nom)) }}.jpg" 
                             alt="{{ $machine->nom }}" 
                             class="w-full h-48 object-cover cursor-pointer hover:opacity-90 transition-opacity"
                             onclick="openModal('/images/machines/{{ strtolower(str_replace(' ', '-', $machine->nom)) }}.jpg', '{{ $machine->nom }}')">


                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900">{{ $machine->nom }}</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ $machine->description }}</p>
                            
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    Durée moyenne : {{ $machine->duree_utilisation ?? '1h' }}
                                </div>

                                @if($machine->reservations->isNotEmpty())
                                    <div class="flex items-center text-sm text-blue-600">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Réservée jusqu'au {{ $machine->reservations->first()->date_reservation->format('d/m/Y H:i') }}
                                    </div>
                                @elseif($machine->statut == 'en_maintenance')
                                    <div class="flex items-center text-sm text-yellow-600">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        En maintenance
                                    </div>
                                @elseif($machine->statut == 'hors_service')
                                    <div class="flex items-center text-sm text-red-600">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Hors service
                                    </div>
                                @elseif($machine->statut == 'non_disponible')
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Non disponible
                                    </div>
                                @else
                                    <div class="flex items-center text-sm text-green-600">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Disponible
                                    </div>
                                @endif
                            </div>

                            <div class="mt-6">
                                @if($machine->reservations->isNotEmpty() || $machine->statut == 'en_maintenance' || $machine->statut == 'hors_service' || $machine->statut == 'non_disponible')
                                    <button class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed">
                                        Réserver
                                    </button>
                                @else
                                    <a href="{{ route('user.reservations.create', $machine) }}"
                                       class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Réserver
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection 