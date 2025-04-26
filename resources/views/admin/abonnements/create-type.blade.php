@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Nouveau type d'abonnement</h2>
            <p class="text-gray-600">Créez un nouveau type d'abonnement personnalisé.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.abonnements.store-type') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- Nom -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom affiché</label>
                        <p class="text-xs text-gray-500 mb-1">Nom qui sera affiché aux utilisateurs</p>
                        <input type="text" name="name" id="name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Prix -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Prix (DT)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="price" id="price" required
                                   min="0" step="0.01"
                                   class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 sm:text-sm">DT</span>
                            </div>
                        </div>
                    </div>

                    <!-- Durée -->
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700">Durée (en jours)</label>
                        <input type="number" name="duration" id="duration" required
                               min="1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <!-- Actif -->
                    <div class="flex items-center">
                        <input type="checkbox" name="active" id="active" value="1"
                               class="h-4 w-4 text-blue-600 rounded border-gray-300">
                        <label for="active" class="ml-2 block text-sm text-gray-700">
                            Activer ce type d'abonnement
                        </label>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="mt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.abonnements.config') }}" 
                       class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Créer le type d'abonnement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
