<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statut de l'Abonnement - FabLab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
            <div>
                <h1 class="text-2xl font-bold text-center text-gray-900">Statut de l'Abonnement</h1>
                @if(session('success'))
                    <div class="mt-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded relative" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            @if($abonnement)
                <div class="mt-6 space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-medium text-gray-900">Détails de l'abonnement</h2>
                        <dl class="mt-2 space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($abonnement->type === 'mensuel')
                                        Abonnement Mensuel
                                    @elseif($abonnement->type === 'annuel')
                                        Abonnement Annuel
                                    @elseif(isset(config('abonnements.types')[$abonnement->type]))
                                        {{ config('abonnements.types')[$abonnement->type]['name'] }}
                                    @else
                                        {{ ucfirst($abonnement->type) }}
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Prix</dt>
                                <dd class="text-sm text-gray-900">{{ number_format($abonnement->prix, 2) }} DT</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                <dd class="text-sm">
                                    @switch($abonnement->statut)
                                        @case('en_attente')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                En attente
                                            </span>
                                            @break
                                        @case('approuve')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Approuvé
                                            </span>
                                            @break
                                        @case('refuse')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Refusé
                                            </span>
                                            @break
                                    @endswitch
                                </dd>
                            </div>
                            @if($abonnement->commentaire_admin)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Commentaire administrateur</dt>
                                    <dd class="text-sm text-gray-900">{{ $abonnement->commentaire_admin }}</dd>
                                </div>
                            @endif
                            @if($abonnement->statut === 'approuve')
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date de début</dt>
                                    <dd class="text-sm text-gray-900">{{ $abonnement->date_debut->format('d/m/Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date de fin</dt>
                                    <dd class="text-sm text-gray-900">{{ $abonnement->date_fin->format('d/m/Y') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    @if($abonnement->statut === 'approuve' && $abonnement->actif)
                        <form action="{{ route('user.subscription.cancel') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Annuler l'abonnement
                            </button>
                        </form>
                    @endif
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative">
                    <p>Vous n'avez pas encore d'abonnement.</p>
                </div>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-800">
                    Retour au home
                </a>
            </div>
        </div>
    </div>
</body>
</html>