<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un nouvel Admin</title>
    <!-- Intégration de Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Créer un nouvel Admin</h1>
            <form action="{{ route('admin.store') }}" method="POST">
                @csrf
                <!-- Champ Nom -->
                <div class="mb-4">
                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom :</label>
                    <input type="text" name="nom" id="nom" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <!-- Champ Prénom -->
                <div class="mb-4">
                    <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom :</label>
                    <input type="text" name="prenom" id="prenom" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <!-- Champ Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email :</label>
                    <input type="email" name="email" id="email" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <!-- Champ Mot de passe -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe :</label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <!-- Bouton de soumission -->
                <div class="flex items-center justify-between">
                    <button type="submit"
                            class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Créer
                    </button>
                </div>
            </form>
            <!-- Lien de retour -->
            <div class="mt-6 text-center">
                <a href="{{ route('admin.dashboard') }}" class="text-blue-500 hover:text-blue-700">Retour au tableau de bord</a>
            </div>
        </div>
    </div>
</body>
</html>