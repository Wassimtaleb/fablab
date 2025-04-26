@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Mes réservations
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Liste de toutes vos demandes de réservation
                        </p>
                    </div>
                    <a href="{{ route('user.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        Retour au tableau de bord
                    </a>
                </div>

                @if($reservations->isEmpty())
                    <div class="px-4 py-5 sm:p-6">
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Vous n'avez pas encore de réservation.</p>
                            <div class="mt-6">
                                <a href="{{ route('user.machines.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Réserver une machine
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="border-t border-gray-200">
                        <ul role="list" class="divide-y divide-gray-200">
                            @foreach($reservations as $reservation)
                                <li class="p-4 hover:bg-gray-50">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $reservation->machine->nom }}
                                            </p>
                                            <div class="mt-1">
                                                @if($reservation->date_souhaitee)
                                                <p class="text-sm text-gray-500">
                                                    Date souhaitée : {{ $reservation->date_souhaitee->format('d/m/Y H:i') }}
                                                </p>
                                                @else
                                                <p class="text-sm text-gray-500">
                                                    Date souhaitée : {{ $reservation->date_reservation->format('d/m/Y') }} à {{ $reservation->heure_debut }}
                                                </p>
                                                @endif
                                                @if($reservation->date_validee)
                                                    <p class="text-sm text-gray-500">
                                                        Date validée : {{ $reservation->date_validee->format('d/m/Y H:i') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-600">
                                                    {{ $reservation->message_utilisateur }}
                                                </p>
                                                @if($reservation->message_admin)
                                                    <p class="mt-1 text-sm text-gray-600">
                                                        <span class="font-medium">Réponse admin :</span> {{ $reservation->message_admin }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            @switch($reservation->statut)
                                                @case('en_attente')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        En attente
                                                    </span>
                                                    @break
                                                @case('validee')
                                                    <div class="flex flex-col space-y-2">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Validée
                                                        </span>
                                                        <a href="{{ route('user.reservations.ticket', $reservation->id) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded bg-blue-600 text-white hover:bg-blue-700">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            Ticket
                                                        </a>
                                                    </div>
                                                    @break
                                                @case('refusee')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Refusée
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ ucfirst($reservation->statut) }}
                                                    </span>
                                            @endswitch
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
</div>
@endsection 