<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Utilisateur - FabLab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
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
<body class="bg-gray-100 min-h-screen">
    @if(!Auth::check() || session('user_type') !== 'user')
        <div class="container mx-auto px-4 py-8">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Erreur!</strong>
                <span class="block sm:inline">Session invalide. Veuillez vous reconnecter.</span>
                <a href="{{ url('/') }}" class="block mt-2 text-red-700 hover:text-red-900 underline">Retour à la page de connexion</a>
            </div>
        </div>
    @else
    <!-- En-tête -->
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">FabLab</h1>
            <div class="flex items-center gap-4">
                <span class="hidden md:inline">{{ Auth::user()->nom }} {{ Auth::user()->prenom }}</span>
                <form action="{{ route('logout') }}" method="POST" class="ml-2">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded-md text-sm flex items-center">
                        <i class="fas fa-sign-out-alt mr-1"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- En-tête -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Tableau de bord</h2>
                <p class="text-gray-600">Bienvenue sur votre espace personnel</p>
            </div>

            <!-- État de l'abonnement -->
            @if($abonnementEnAttente)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
                    <p><strong>Votre demande d'abonnement est en attente de validation par l'administrateur.</strong></p>
                    <p class="text-blue-800 font-semibold mt-1">L'activation de votre abonnement se fera après le paiement de la facture sur place.</p>
                    <p>Vous devez attendre la validation de votre abonnement avant de pouvoir réserver des machines.</p>
                    <p class="text-sm mt-1">Date de la demande : {{ $abonnementEnAttente->created_at->format('d/m/Y H:i') }}</p>
                    <a href="{{ route('user.subscription.facture.generate', ['id' => $abonnementEnAttente->id]) }}" class="inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-file-invoice mr-2"></i> Télécharger la facture
                    </a>
                </div>
            @elseif(!$abonnementActif)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <p>Vous n'avez pas d'abonnement actif.</p>
                    <a href="{{ route('user.subscription.form') }}" class="inline-block mt-2 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Souscrire à un abonnement
                    </a>
                </div>
            @else
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p><strong>Votre abonnement est actif.</strong></p>
                            <p class="text-sm mt-1">Date d'activation : {{ $abonnementActif->date_debut->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Cartes de statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Réservations en cours -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                            <i class="fas fa-calendar-check text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Réservations en cours</p>
                            <p class="text-lg font-semibold">{{ $reservations->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Machines disponibles -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                            <i class="fas fa-tools text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Machines disponibles</p>
                            <p class="text-lg font-semibold">{{ $machines->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Jours restants -->
                @if($abonnementActif)
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jours restants</p>
                            <p class="text-lg font-semibold">{{ (int)now()->diffInDays($abonnementActif->date_fin) }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Réservations en cours -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Vos réservations en cours</h3>
                    <a href="{{ route('user.reservations.my') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                        Voir toutes les réservations
                    </a>
                </div>
                @if($reservations->isEmpty())
                    <p class="text-gray-500 text-center py-4">Aucune réservation en cours</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Machine</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reservations as $reservation)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $reservation->machine->nom }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $reservation->date_reservation->format('d/m/Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $reservation->heure_debut }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $reservation->duree }}h</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $reservation->statut === 'validee' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($reservation->statut) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($reservation->statut === 'validee')
                                                <a href="{{ route('user.reservations.ticket', $reservation->id) }}" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md flex items-center text-sm">
                                                    <i class="fas fa-file-alt mr-2"></i> Télécharger le ticket
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Machines disponibles -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Machines disponibles</h3>
                    <a href="{{ route('user.machines.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                        Voir toutes les machines
                    </a>
                </div>
                @if($machines->isEmpty())
                    <p class="text-gray-500 text-center py-4">Aucune machine disponible pour le moment</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($machines as $machine)
                            <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                                <img src="/images/machines/{{ strtolower(str_replace(' ', '-', $machine->nom)) }}.jpg" 
                                     alt="{{ $machine->nom }}" 
                                     class="w-full h-48 object-cover cursor-pointer hover:opacity-90 transition-opacity"
                                     onclick="openModal('/images/machines/{{ strtolower(str_replace(' ', '-', $machine->nom)) }}.jpg', '{{ $machine->nom }}')">
                                <div class="p-4">
                                    <div class="flex items-center mb-3">
                                        <i class="fas fa-tools text-blue-500 mr-2"></i>
                                        <h4 class="text-lg font-medium text-gray-900">{{ $machine->nom }}</h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($machine->description, 100) }}</p>
                                    <a href="{{ route('user.machines.show', $machine) }}" 
                                       class="inline-flex items-center text-blue-500 hover:text-blue-700 text-sm">
                                        Voir les détails
                                        <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pied de page -->
   
    @endif
</body>
</html>