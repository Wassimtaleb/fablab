<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Machine - FabLab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
            <div>
                <h1 class="text-2xl font-bold text-center text-gray-900">Ajouter une Machine</h1>
                @if ($errors->any())
                    <div class="mt-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <form action="{{ route('admin.machines.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-6">
                @csrf
                
                <!-- Nom -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom de la machine</label>
                    <input type="text" name="nom" id="nom" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('nom') }}"
                           placeholder="Ex: Imprimante 3D">
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" required
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              rows="4"
                              placeholder="Description détaillée de la machine">{{ old('description') }}</textarea>
                </div>

                <!-- Statut -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="statut" id="statut" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="disponible" {{ old('statut') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="non_disponible" {{ old('statut') == 'non_disponible' ? 'selected' : '' }}>Non disponible</option>
                        <option value="hors_service" {{ old('statut') == 'hors_service' ? 'selected' : '' }}>Hors service</option>
                    </select>
                </div>

                <!-- Image de la machine -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Image de la machine</label>
                    <div class="mt-1 flex items-center">
                        <input type="file" name="image" id="image" accept="image/*"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Formats acceptés: JPG, PNG, GIF. Max 2MB.</p>
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-between space-x-4">
                    <a href="{{ route('admin.machines.index') }}" 
                       class="w-1/2 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit"
                            class="w-1/2 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ajouter la machine
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 