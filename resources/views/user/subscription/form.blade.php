<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement - FabLab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
            <div>
                <h1 class="text-2xl font-bold text-center text-gray-900">Choisir un Abonnement</h1>
                @if(session('error'))
                    <div class="mt-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            @if($demandeEnAttente)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative">
                    <p>Vous avez déjà une demande d'abonnement en attente de validation.</p>
                </div>
            @else
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded animate-pulse" role="alert">
                    <strong>Attention :</strong> Vous ne pouvez pas changer votre type d'abonnement après avoir souscrit.
                </div>
                <form action="{{ route('user.subscription.subscribe') }}" method="POST" class="mt-8 space-y-6">
                    @csrf
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="radio" name="type" value="mensuel" id="mensuel" class="h-4 w-4 text-blue-600" required {{ !config('abonnements.enable_monthly') ? 'disabled' : '' }}>
                            <label for="mensuel" class="ml-3 block text-gray-700 {{ !config('abonnements.enable_monthly') ? 'opacity-50' : '' }}">
                                <span class="text-lg font-medium">Abonnement Mensuel</span>
                                <span class="block text-gray-500">{{ number_format(config('abonnements.prix_mensuel'), 2) }} DT / mois</span>
                                <span class="block text-sm text-gray-500">{{ config('abonnements.description_mensuel') }}</span>
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="radio" name="type" value="annuel" id="annuel" class="h-4 w-4 text-blue-600" {{ !config('abonnements.enable_yearly') ? 'disabled' : '' }}>
                            <label for="annuel" class="ml-3 block text-gray-700 {{ !config('abonnements.enable_yearly') ? 'opacity-50' : '' }}">
                                <span class="text-lg font-medium">Abonnement Annuel</span>
                                <span class="block text-gray-500">{{ number_format(config('abonnements.prix_annuel'), 2) }} DT / an</span>
                                <span class="block text-sm text-gray-500">{{ config('abonnements.description_annuel') }}</span>
                            </label>
                        </div>

                        @if(isset($config['types']) && count($config['types']) > 0)
                            @foreach($config['types'] as $id => $type)
                                @if($type['active'])
                                    <div class="flex items-center">
                                        <input type="radio" name="type" value="{{ $id }}" id="{{ $id }}" class="h-4 w-4 text-blue-600">
                                        <label for="{{ $id }}" class="ml-3 block text-gray-700">
                                            <span class="text-lg font-medium">{{ $type['name'] }}</span>
                                            <span class="block text-gray-500">{{ number_format($type['price'], 2) }} DT / {{ $type['duration'] }} jours</span>
                                            <span class="block text-sm text-gray-500">{{ $type['description'] }}</span>
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Souscrire
                        </button>
                    </div>
                </form>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">
                    Retour au home
                </a>
            </div>
        </div>
    </div>
</body>
</html> 