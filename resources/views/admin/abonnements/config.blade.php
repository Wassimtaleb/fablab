@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex items-center space-x-4 mb-3">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Tableau de bord
                </a>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Configuration des abonnements</h2>
            <p class="text-gray-600">Gérez les prix et les types d'abonnements disponibles.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Formulaire de configuration -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.abonnements.config.update') }}" method="POST">
                @csrf
                
                <!-- Abonnement Mensuel -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Abonnement Mensuel</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="prix_mensuel" class="block text-sm font-medium text-gray-700">Prix (DT)</label>
                            <input type="number" name="prix_mensuel" id="prix_mensuel" 
                                   value="{{ old('prix_mensuel', config('abonnements.prix_mensuel', 100)) }}" 
                                   step="0.01" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="description_mensuel" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description_mensuel" id="description_mensuel" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description_mensuel', config('abonnements.description_mensuel', 'Accès à toutes les machines pendant un mois')) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Abonnement Annuel -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Abonnement Annuel</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="prix_annuel" class="block text-sm font-medium text-gray-700">Prix (DT)</label>
                            <input type="number" name="prix_annuel" id="prix_annuel" 
                                   value="{{ old('prix_annuel', config('abonnements.prix_annuel', 1000)) }}" 
                                   step="0.01" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="description_annuel" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description_annuel" id="description_annuel" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description_annuel', config('abonnements.description_annuel', 'Accès à toutes les machines pendant un an avec 2 mois offerts')) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Options générales -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Options générales</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <select name="enable_monthly" id="enable_monthly" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="1" {{ config('abonnements.enable_monthly') ? 'selected' : '' }}>Activé</option>
                                <option value="0" {{ !config('abonnements.enable_monthly') ? 'selected' : '' }}>Désactivé</option>
                            </select>
                            <label for="enable_monthly" class="ml-2 block text-sm text-gray-700">
                                Abonnement mensuel
                            </label>
                        </div>
                        <div class="flex items-center">
                            <select name="enable_yearly" id="enable_yearly" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="1" {{ config('abonnements.enable_yearly') ? 'selected' : '' }}>Activé</option>
                                <option value="0" {{ !config('abonnements.enable_yearly') ? 'selected' : '' }}>Désactivé</option>
                            </select>
                            <label for="enable_yearly" class="ml-2 block text-sm text-gray-700">
                                Abonnement annuel
                            </label>
                        </div>

                        @if(isset($config['types']) && count($config['types']) > 0)
                            @foreach($config['types'] as $id => $type)
                                <div class="flex items-center">
                                    <select name="custom_types_status[{{ $id }}]" id="enable_{{ $id }}" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="1" {{ $type['active'] ? 'selected' : '' }}>Activé</option>
                                        <option value="0" {{ !$type['active'] ? 'selected' : '' }}>Désactivé</option>
                                    </select>
                                    <label for="enable_{{ $id }}" class="ml-2 block text-sm text-gray-700">
                                        {{ $type['name'] }}
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Types d'abonnement personnalisés -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Types d'abonnement personnalisés</h3>
                    
                    @if(isset($config['types']) && count($config['types']) > 0)
                        <div class="space-y-4 mb-4">
                            @foreach($config['types'] as $id => $type)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h4 class="font-medium">{{ $type['name'] }}</h4>
                                        <p class="text-sm text-gray-600">{{ $type['description'] }}</p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ number_format($type['price'], 2) }} DT - {{ $type['duration'] }} jours
                                            <span class="ml-2 {{ $type['active'] ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $type['active'] ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </p>
                                    </div>
                                    <button type="button" 
                                            onclick="deleteType('{{ $id }}')"
                                            class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic mb-4">Aucun type d'abonnement personnalisé n'a été créé.</p>
                    @endif

                    <a href="{{ route('admin.abonnements.create-type') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Ajouter un type d'abonnement
                    </a>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-end space-x-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    <!-- Script pour la suppression -->
    <script>
        function deleteType(id) {
            if (confirm('Voulez-vous vraiment supprimer ce type d\'abonnement ?')) {
                fetch(`/admin/abonnements/delete-type/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Recharger la page pour voir les changements
                        window.location.reload();
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suppression');
                });
            }
        }
    </script>
@endsection
