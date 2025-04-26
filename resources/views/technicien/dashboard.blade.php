@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Bienvenue, {{ $technicien->nom }} {{ $technicien->prenom }}</h2>
            <p class="text-gray-600">Tableau de bord technicien du FabLab</p>
        </div>

        @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
        @endif

        @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
        @endif

        <!-- Liste de toutes les machines -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="bg-blue-600 text-white p-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold">Toutes les machines</h3>
            </div>
            <div class="p-4">
                @if($machines->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Machine</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Réservations</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($machines as $machine)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $machine->nom }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($machine->statut == 'disponible' && $machine->disponible)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Disponible
                                                </span>
                                            @elseif($machine->statut == 'en_maintenance')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    En maintenance
                                                </span>
                                            @elseif($machine->statut == 'hors_service')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Hors service
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Non disponible
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if(isset($reservationsParMachine[$machine->id]) && $reservationsParMachine[$machine->id]->count() > 0)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                    {{ $reservationsParMachine[$machine->id]->count() }} réservation(s)
                                                </span>
                                                <div class="mt-1 text-xs text-gray-500">
                                                    @php
                                                        $prochaine = $reservationsParMachine[$machine->id]->first();
                                                    @endphp
                                                    Prochaine: {{ \Carbon\Carbon::parse($prochaine->date_reservation)->format('d/m/Y') }} à {{ $prochaine->heure_debut }}
                                                </div>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Aucune réservation
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($machine->statut == 'disponible' && $machine->disponible)
                                                @if(isset($reservationsParMachine[$machine->id]) && $reservationsParMachine[$machine->id]->count() > 0)
                                                    <span class="text-yellow-600 italic">
                                                        La machine a des réservations futures et ne peut pas être mise en maintenance
                                                    </span>
                                                @else
                                                    <a href="{{ url('/technicien/machines/'.$machine->id.'/maintenance') }}" 
                                                       onclick="event.preventDefault(); document.getElementById('maintenance-form-'+{{ $machine->id }}).submit();" 
                                                       class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                        Mettre en maintenance
                                                    </a>
                                                    <form id="maintenance-form-{{ $machine->id }}" action="{{ url('/technicien/machines/'.$machine->id.'/maintenance') }}" method="POST" class="hidden">
                                                        @csrf
                                                    </form>
                                                @endif
                                            @elseif($machine->statut == 'en_maintenance')
                                                @php
                                                    $maintenance = $machinesEnMaintenance->where('machine_id', $machine->id)->first();
                                                @endphp
                                                @if($maintenance && $maintenance->technicien_id == $technicien->id)
                                                    <form action="{{ url('/technicien/maintenances/'.$maintenance->id.'/terminer-maintenance') }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                            Terminer la maintenance
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-500 italic">En maintenance par un autre technicien</span>
                                                @endif
                                            @else
                                                <span class="text-gray-500 italic">Aucune action disponible</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-tools text-gray-400 text-5xl mb-3"></i>
                        <p class="text-gray-500">Aucune machine trouvée.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Machines en maintenance par ce technicien -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-yellow-600 text-white p-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold">Mes machines en maintenance</h3>
            </div>
            <div class="p-4">
                @if($machinesEnMaintenance->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Machine</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Depuis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($machinesEnMaintenance as $maintenance)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maintenance->machine->nom }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maintenance->created_at->diffForHumans() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form action="{{ url('/technicien/maintenances/'.$maintenance->id.'/terminer-maintenance') }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    Terminer la maintenance
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-gray-400 text-5xl mb-3"></i>
                        <p class="text-gray-500">Vous n'avez aucune machine en maintenance actuellement.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection