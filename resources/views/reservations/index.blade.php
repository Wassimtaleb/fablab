@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Liste des machines -->
        <div class="mb-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Machines disponibles</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($machines as $machine)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $machine->nom }}</h3>
                            <p class="mt-2 text-gray-600">{{ $machine->description }}</p>
                            <div class="mt-4">
                                <a href="{{ route('reservations.create', $machine) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Réserver
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Liste des réservations -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Mes réservations</h2>
            @if($reservations->isEmpty())
                <p class="text-gray-600">Vous n'avez pas encore de réservations.</p>
            @else
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul role="list" class="divide-y divide-gray-200">
                        @foreach($reservations as $reservation)
                            <li>
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-medium text-gray-900">
                                                {{ $reservation->machine->nom }}
                                            </h4>
                                            <p class="mt-2 text-sm text-gray-600">
                                                Date souhaitée : {{ $reservation->date_souhaitee->format('d/m/Y H:i') }}
                                            </p>
                                            @if($reservation->date_validee)
                                                <p class="mt-1 text-sm text-gray-600">
                                                    Date validée : {{ $reservation->date_validee->format('d/m/Y H:i') }}
                                                </p>
                                            @endif
                                            <p class="mt-2 text-sm text-gray-600">
                                                Votre message : {{ $reservation->message_utilisateur }}
                                            </p>
                                            @if($reservation->message_admin)
                                                <p class="mt-2 text-sm text-gray-600">
                                                    Réponse admin : {{ $reservation->message_admin }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            @if($reservation->statut === 'en_attente')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                    En attente
                                                </span>
                                            @elseif($reservation->statut === 'approuve')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                    Approuvée
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                    Refusée
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 