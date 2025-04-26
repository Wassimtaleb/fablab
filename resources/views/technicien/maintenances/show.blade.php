@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête de la section -->
        <div class="mb-6">
            <div class="flex items-center space-x-4 mb-3">
                <a href="{{ route('technicien.maintenances-technicien.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour à la liste
                </a>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Détails de la maintenance
            </h2>
            <p class="text-gray-600 pl-10">Consultez et gérez les détails de cette maintenance.</p>
        </div>

        <!-- Messages Flash -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-md mb-6 animate-pulse" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-md mb-6 animate-pulse" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Détails de la maintenance -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    <div class="bg-blue-600 text-white p-4">
                        <h3 class="text-lg font-semibold">Informations de la maintenance</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Machine</h4>
                                <p class="text-lg font-semibold text-gray-900">{{ $maintenance->machine->nom }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Type</h4>
                                <p class="text-lg font-semibold text-gray-900">{{ $maintenance->type }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Date de début</h4>
                                <p class="text-lg font-semibold text-gray-900">{{ $maintenance->date_debut->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Date de fin</h4>
                                <p class="text-lg font-semibold text-gray-900">{{ $maintenance->date_fin ? $maintenance->date_fin->format('d/m/Y H:i') : 'Non définie' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Statut</h4>
                                <p class="text-lg">
                                    @if($maintenance->statut === 'planifiee')
                                        <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Planifiée</span>
                                    @elseif($maintenance->statut === 'en_cours')
                                        <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">En cours</span>
                                    @elseif($maintenance->statut === 'terminee')
                                        <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Terminée</span>
                                    @elseif($maintenance->statut === 'annulee')
                                        <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Annulée</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Description</h4>
                            <div class="bg-gray-50 p-4 rounded-md text-gray-900">
                                {{ $maintenance->description }}
                            </div>
                        </div>
                        
                        @if($maintenance->notes)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Notes</h4>
                            <div class="bg-gray-50 p-4 rounded-md text-gray-900">
                                {{ $maintenance->notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Commentaires -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-indigo-600 text-white p-4">
                        <h3 class="text-lg font-semibold">Commentaires</h3>
                    </div>
                    <div class="p-6">
                        @if(count($commentaires) > 0)
                            <div class="space-y-4">
                                @foreach($commentaires as $commentaire)
                                    <div class="bg-gray-50 p-4 rounded-md">
                                        <div class="flex justify-between items-start">
                                            <div class="font-medium text-gray-900">{{ $commentaire->technicien->nom }} {{ $commentaire->technicien->prenom }}</div>
                                            <div class="text-sm text-gray-500">{{ $commentaire->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                        <div class="mt-2 text-gray-700">
                                            {{ $commentaire->contenu }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                <p class="text-gray-500">Aucun commentaire pour le moment.</p>
                            </div>
                        @endif

                        <!-- Formulaire d'ajout de commentaire -->
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Ajouter un commentaire</h4>
                            <form action="{{ route('technicien.maintenances-technicien.add-comment', $maintenance) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <textarea name="contenu" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Votre commentaire..." required></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                        </svg>
                                        Ajouter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    <div class="bg-yellow-600 text-white p-4">
                        <h3 class="text-lg font-semibold">Actions</h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('technicien.maintenances-technicien.update-status', $maintenance) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="statut" class="block text-sm font-medium text-gray-700 mb-2">Changer le statut</label>
                                <select id="statut" name="statut" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="planifiee" {{ $maintenance->statut === 'planifiee' ? 'selected' : '' }}>Planifiée</option>
                                    <option value="en_cours" {{ $maintenance->statut === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="terminee" {{ $maintenance->statut === 'terminee' ? 'selected' : '' }}>Terminée</option>
                                    <option value="annulee" {{ $maintenance->statut === 'annulee' ? 'selected' : '' }}>Annulée</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (optionnel)</label>
                                <textarea id="notes" name="notes" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Ajoutez des notes sur cette maintenance...">{{ $maintenance->notes }}</textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Mettre à jour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
