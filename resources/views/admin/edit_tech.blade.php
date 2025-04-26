<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Technicien - FabLab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen p-6">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-blue-600 text-white p-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">Modifier un Technicien</h1>
                    <a href="{{ route('admin.dashboard') }}" class="bg-white text-blue-600 px-4 py-2 rounded-md hover:bg-gray-100">
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
                <h2 class="text-lg font-semibold mb-4">Sélectionner un technicien à modifier</h2>
                
                @if($techniciens->isEmpty())
                    <p class="text-gray-500">Aucun technicien disponible.</p>
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
                                @foreach($techniciens as $technicien)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">{{ $technicien->nom }}</td>
                                        <td class="py-2 px-4 border-b">{{ $technicien->prenom }}</td>
                                        <td class="py-2 px-4 border-b">{{ $technicien->email }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <button 
                                                class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 edit-tech"
                                                data-id="{{ $technicien->id }}"
                                                data-nom="{{ $technicien->nom }}"
                                                data-prenom="{{ $technicien->prenom }}"
                                                data-email="{{ $technicien->email }}">
                                                <i class="fas fa-edit mr-1"></i>Modifier
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Formulaire de modification (initialement caché) -->
            <div id="edit-form-container" class="hidden p-6 bg-gray-50 border-t">
                <h2 class="text-lg font-semibold mb-4">Modifier le technicien</h2>
                <form id="edit-form" method="POST" action="" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" name="nom" id="nom" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" name="prenom" id="prenom" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Nouveau mot de passe (laisser vide pour conserver l'actuel)
                        </label>
                        <input type="password" name="password" id="password" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancel-edit" 
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-tech');
            const editFormContainer = document.getElementById('edit-form-container');
            const editForm = document.getElementById('edit-form');
            const cancelButton = document.getElementById('cancel-edit');
            
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nom = this.getAttribute('data-nom');
                    const prenom = this.getAttribute('data-prenom');
                    const email = this.getAttribute('data-email');
                    
                    // Mettre à jour l'action du formulaire avec la route Laravel
                    editForm.action = "{{ url('admin/techniciens/update') }}/" + id;
                    
                    // Remplir les champs du formulaire
                    document.getElementById('nom').value = nom;
                    document.getElementById('prenom').value = prenom;
                    document.getElementById('email').value = email;
                    document.getElementById('password').value = '';
                    
                    // Afficher le formulaire
                    editFormContainer.classList.remove('hidden');
                    
                    // Faire défiler jusqu'au formulaire
                    editFormContainer.scrollIntoView({ behavior: 'smooth' });
                });
            });
            
            cancelButton.addEventListener('click', function() {
                editFormContainer.classList.add('hidden');
            });
        });
    </script>
</body>
</html>
