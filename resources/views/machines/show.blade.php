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
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $machine->nom }}</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            @if($machine->statut === 'disponible')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Disponible
                                </span>
                            @elseif($machine->statut === 'en_maintenance')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    En maintenance
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Hors service
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    @if($machine->statut === 'disponible')
                        <a href="{{ route('user.machines.reserve', $machine) }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Réserver cette machine
                        </a>
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Description</h3>
                        <p class="mt-2 text-gray-600">
                            {{ $machine->description }}
                        </p>
                    </div>
                    <div>
                        <img src="/images/machines/{{ strtolower(str_replace(' ', '-', $machine->nom)) }}.jpg" 
                             alt="{{ $machine->nom }}" 
                             class="w-full h-64 object-cover rounded-lg shadow-lg cursor-pointer hover:opacity-90 transition-opacity"
                             onclick="openModal('/images/machines/{{ strtolower(str_replace(' ', '-', $machine->nom)) }}.jpg', '{{ $machine->nom }}')">
                    </div>
                </div>

                @if($machine->date_maintenance)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">Prochaine maintenance</h3>
                        <p class="mt-2 text-gray-600">
                            {{ $machine->date_maintenance->format('d/m/Y') }}
                        </p>
                    </div>
                @endif

                <div class="mt-8">
                    <a href="{{ route('user.machines.index') }}"
                       class="text-blue-600 hover:text-blue-800">
                        &larr; Retour à la liste des machines
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 