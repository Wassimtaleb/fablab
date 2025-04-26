<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un Utilisateur - FabLab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen p-6">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-red-600 text-white p-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">Supprimer un Utilisateur</h1>
                    <a href="{{ route('admin.dashboard') }}" class="bg-white text-red-600 px-4 py-2 rounded-md hover:bg-gray-100">
                        <i class="fas fa-arrow-left mr-2"></i>Retour au tableau de bord
                    </a>
                </div>
            </div>
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative m-4" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative m-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Sélectionner un utilisateur à supprimer</h2>
                
                @if($users->isEmpty())
                    <p class="text-gray-500">Aucun utilisateur disponible.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Nom</th>
                                    <th class="py-2 px-4 border-b text-left">Prénom</th>
                                    <th class="py-2 px-4 border-b text-left">Email</th>
                                    <th class="py-2 px-4 border-b text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">{{ $user->nom }}</td>
                                        <td class="py-2 px-4 border-b">{{ $user->prenom }}</td>
                                        <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <button 
                                                class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 delete-user"
                                                data-id="{{ $user->id }}"
                                                data-nom="{{ $user->nom }}"
                                                data-prenom="{{ $user->prenom }}"
                                                data-email="{{ $user->email }}">
                                                <i class="fas fa-trash-alt mr-1"></i>Supprimer
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Formulaire de confirmation de suppression (initialement caché) -->
            <div id="delete-form-container" class="hidden p-6 bg-gray-50 border-t">
                <h2 class="text-lg font-semibold mb-4">Confirmer la suppression</h2>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Attention : Cette action est irréversible. Êtes-vous sûr de vouloir supprimer cet utilisateur ?
                            </p>
                        </div>
                    </div>
                </div>
                <form id="delete-form" method="POST" action="{{ route('admin.users.destroy') }}" class="space-y-4">
                    @csrf
                    @method('DELETE')
                    
                    <input type="hidden" name="email" id="delete-email">
                    
                    <div>
                        <p class="text-gray-700">Vous êtes sur le point de supprimer l'utilisateur :</p>
                        <p class="font-semibold mt-2" id="user-info"></p>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancel-delete" 
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Confirmer la suppression
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-user');
            const deleteFormContainer = document.getElementById('delete-form-container');
            const deleteForm = document.getElementById('delete-form');
            const cancelButton = document.getElementById('cancel-delete');
            const userInfoElement = document.getElementById('user-info');
            const deleteEmailInput = document.getElementById('delete-email');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nom = this.getAttribute('data-nom');
                    const prenom = this.getAttribute('data-prenom');
                    const email = this.getAttribute('data-email');
                    
                    // Remplir les informations de l'utilisateur
                    userInfoElement.textContent = `${prenom} ${nom} (${email})`;
                    deleteEmailInput.value = email;
                    
                    // Afficher le formulaire de confirmation
                    deleteFormContainer.classList.remove('hidden');
                    
                    // Faire défiler jusqu'au formulaire
                    deleteFormContainer.scrollIntoView({ behavior: 'smooth' });
                });
            });
            
            cancelButton.addEventListener('click', function() {
                deleteFormContainer.classList.add('hidden');
            });
        });
    </script>
</body>
</html>