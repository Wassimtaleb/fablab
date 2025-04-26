@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête de la section avec animation subtile -->
        <div class="mb-6 transform transition duration-500 hover:translate-y-1">
            <div class="flex items-center space-x-4 mb-3">
                <a href="{{ route('admin.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center shadow-md">
                    <svg xmlns="[http://www.w3.org/2000/svg"](http://www.w3.org/2000/svg") class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Tableau de bord
                </a>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2 flex items-center">
                <svg xmlns="[http://www.w3.org/2000/svg"](http://www.w3.org/2000/svg") class="h-8 w-8 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Calendrier des machines
            </h2>
            <p class="text-gray-600 pl-10">Consultez les réservations des machines du FabLab sous forme de calendrier.</p>
        </div>

        <!-- Messages Flash avec animation -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-md mb-6 animate-pulse" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-md mb-6 animate-pulse" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Sélection de machine et navigation avec design amélioré -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6 border-t-4 border-blue-500 transition-all duration-300 hover:shadow-xl">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="mb-4 md:mb-0 w-full md:w-auto">
                    <form id="filter-form" action="{{ route('admin.reservations.calendar') }}" method="GET" class="flex flex-col md:flex-row items-start md:items-center space-y-3 md:space-y-0 md:space-x-4">
                        <div class="w-full md:w-64">
                            <label for="machine_id" class="block text-sm font-medium text-gray-700 mb-1">Sélectionner une machine :</label>
                            <div class="relative">
                                <select id="machine_id" name="machine_id" class="block w-full border border-gray-300 rounded-md pl-3 pr-10 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 appearance-none" onchange="this.form.submit()">
                                    <option value="all" {{ $selectedMachineId == 'all' || !$selectedMachineId ? 'selected' : '' }}>Toutes les machines</option>
                                    @foreach($machines as $machine)
                                        <option value="{{ $machine->id }}" {{ $selectedMachineId == $machine->id ? 'selected' : '' }}>{{ $machine->nom }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="month" value="{{ $selectedMonth }}">
                    </form>
                </div>
                <div class="flex flex-wrap gap-2 justify-center">
                    <a href="{{ route('admin.reservations.calendar', ['month' => $prevMonth, 'machine_id' => $selectedMachineId]) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                        <svg xmlns="[http://www.w3.org/2000/svg"](http://www.w3.org/2000/svg") class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Mois précédent
                    </a>
                    <a href="{{ route('admin.reservations.calendar') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                        <svg xmlns="[http://www.w3.org/2000/svg"](http://www.w3.org/2000/svg") class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                        </svg>
                        Mois actuel
                    </a>
                    <a href="{{ route('admin.reservations.calendar', ['month' => $nextMonth, 'machine_id' => $selectedMachineId]) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md transition-colors duration-200 flex items-center shadow-sm">
                        Mois suivant
                        <svg xmlns="[http://www.w3.org/2000/svg"](http://www.w3.org/2000/svg") class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Titre du mois avec design amélioré -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-4 mb-6 text-center">
            <h3 class="text-2xl font-bold text-white">{{ $currentMonth }}</h3>
        </div>

        <!-- Calendrier avec design amélioré -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
            <!-- Jours de la semaine -->
            <div class="grid grid-cols-7 gap-2 mb-4 text-center">
                @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'] as $day)
                    <div class="p-2 font-bold text-gray-700 bg-gray-100 rounded-md">{{ $day }}</div>
                @endforeach
            </div>

            <!-- Grille du calendrier -->
            <div class="grid grid-cols-7 gap-2">
                @php
                    $date = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
                    $lastDay = (clone $date)->endOfMonth();
                    $firstDayOfWeek = $date->dayOfWeekIso; // 1 (Lundi) à 7 (Dimanche)
                @endphp
                {{-- Jours vides avant le premier jour du mois --}}
                @for ($i = 1; $i < $firstDayOfWeek; $i++)
                    <div class="p-2 bg-gray-100 rounded-md opacity-50" style="height: 180px;"></div>
                @endfor
                {{-- Jours du mois --}}
                @while ($date->lte($lastDay))
                    @php 
                        $dateString = $date->format('Y-m-d'); 
                        $nbRes = isset($reservationsByDate[$dateString]) ? count($reservationsByDate[$dateString]) : 0;
                        $badgeColor = $nbRes === 0 ? '' : ($nbRes === 1 ? 'bg-green-500' : ($nbRes <= 3 ? 'bg-orange-400' : 'bg-red-500'));
                        $isToday = $date->isToday();
                    @endphp
                    <div class="calendar-day p-2 {{ $isToday ? 'ring-4 ring-blue-400' : '' }} bg-blue-50 hover:bg-blue-200 rounded-md cursor-pointer border border-blue-200 transition duration-150 relative group shadow-sm hover:shadow-lg" data-date="{{ $dateString }}" style="height: 180px;">
                        <span class="font-bold text-blue-700 text-lg">{{ $date->day }}</span>
                        @if($nbRes > 0)
                            <span class="absolute top-2 right-2 {{ $badgeColor }} text-white text-xs rounded-full px-2 py-1 font-semibold shadow tooltip-badge" data-machines="@foreach($reservationsByDate[$dateString] as $r){{ $r->machine->nom }}@if(!$loop->last), @endif @endforeach">
                                {{ $nbRes }}
                            </span>
                        @endif
                    </div>
                    @php $date->addDay(); @endphp
                @endwhile
            </div>
        </div>

        <!-- Légende des couleurs avec design amélioré -->
        <div class="bg-white rounded-lg shadow-lg p-6 mt-6 border-t-4 border-gray-200">
            <h4 class="font-semibold mb-4 flex items-center">
                <svg xmlns="[http://www.w3.org/2000/svg"](http://www.w3.org/2000/svg") class="h-5 w-5 mr-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Légende des machines
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($machines as $machine)
                    <div class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-50 transition-colors duration-200">
                        <div class="w-4 h-4 rounded-full bg-{{ $machine->id % 10 }}-300 shadow-sm"></div>
                        <span class="text-sm">{{ $machine->nom }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Modal for reservation details -->
        <div id="reservation-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg relative" id="modal-content">
                <button id="close-reservation-modal" class="absolute top-2 right-2 text-2xl">&times;</button>
                <!-- Content will be injected here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Modal styles */
    #reservation-modal.hidden { display: none; }
    #reservation-modal { z-index: 1000; }
    
    .calendar-day {
        min-height: 120px;
        position: relative;
        overflow: visible;
    }
    .calendar-day.ring-4 {
        box-shadow: 0 0 0 3px #60a5fa !important;
    }
    .tooltip-badge {
        position: relative;
        cursor: pointer;
    }
    .tooltip-badge:hover::after {
        content: attr(data-machines);
        position: absolute;
        top: 120%;
        right: 0;
        left: auto;
        min-width: 180px;
        background: #222;
        color: #fff;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.95em;
        white-space: pre-line;
        z-index: 10;
        box-shadow: 0 2px 12px rgba(0,0,0,0.15);
    }
    .calendar-day:hover {
        background: #dbeafe;
        transform: translateY(-2px) scale(1.03);
        z-index: 2;
    }
    
    .reservation-item {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .reservation-item:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .reservation-container {
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
    }
    
    .reservation-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .reservation-container::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .reservation-container::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 3px;
    }
    
    /* Couleurs pour les différentes machines */
    .bg-1-100 { background-color: #E3F2FD; }
    .bg-2-100 { background-color: #F3E5F5; }
    .bg-3-100 { background-color: #E8F5E9; }
    .bg-4-100 { background-color: #FFF3E0; }
    .bg-5-100 { background-color: #F1F8E9; }
    .bg-6-100 { background-color: #E0F7FA; }
    .bg-7-100 { background-color: #FBE9E7; }
    .bg-8-100 { background-color: #FFFDE7; }
    .bg-9-100 { background-color: #E8EAF6; }
    .bg-0-100 { background-color: #EFEBE9; }
    
    .border-1-300 { border-color: #64B5F6; }
    .border-2-300 { border-color: #BA68C8; }
    .border-3-300 { border-color: #81C784; }
    .border-4-300 { border-color: #FFB74D; }
    .border-5-300 { border-color: #AED581; }
    .border-6-300 { border-color: #4DD0E1; }
    .border-7-300 { border-color: #FF8A65; }
    .border-8-300 { border-color: #FFF176; }
    .border-9-300 { border-color: #7986CB; }
    .border-0-300 { border-color: #A1887F; }
    
    .bg-1-300 { background-color: #64B5F6; }
    .bg-2-300 { background-color: #BA68C8; }
    .bg-3-300 { background-color: #81C784; }
    .bg-4-300 { background-color: #FFB74D; }
    .bg-5-300 { background-color: #AED581; }
    .bg-6-300 { background-color: #4DD0E1; }
    .bg-7-300 { background-color: #FF8A65; }
    .bg-8-300 { background-color: #FFF176; }
    .bg-9-300 { background-color: #7986CB; }
    .bg-0-300 { background-color: #A1887F; }
    
    .text-1-800 { color: #1565C0; }
    .text-2-800 { color: #6A1B9A; }
    .text-3-800 { color: #2E7D32; }
    .text-4-800 { color: #EF6C00; }
    .text-5-800 { color: #558B2F; }
    .text-6-800 { color: #00838F; }
    .text-7-800 { color: #D84315; }
    .text-8-800 { color: #F9A825; }
    .text-9-800 { color: #283593; }
    .text-0-800 { color: #4E342E; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add click handler to all calendar days
        document.querySelectorAll('.calendar-day').forEach(dayCell => {
            dayCell.addEventListener('click', function() {
                const date = this.getAttribute('data-date');
                console.log('Clicked date:', date); // Debug
                fetch(`/admin/reservations/by-date/${date}`)
                    .then(response => response.json())
                    .then(reservations => {
                        console.log('Reservations fetched:', reservations); // Debug
                        showReservationsModal(reservations, date);
                    })
                    .catch(() => {
                        alert('Erreur lors de la récupération des réservations.');
                    });
            });
        });
        // Use event delegation for close button (works even after modal content is replaced)
        document.body.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'close-reservation-modal') {
                document.getElementById('reservation-modal').classList.add('hidden');
            }
        });
    });
    // Function to display reservations in modal
    function showReservationsModal(reservations, date) {
        let html = `<h2 class='text-xl font-bold mb-4'>Réservations pour le ${date}</h2>`;
        if (reservations.length === 0) {
            html += '<p>Aucune réservation validée ce jour.</p>';
        } else {
            html += '<ul class="divide-y divide-gray-200">';
            reservations.forEach(r => {
                html += `<li class='py-2'>
                    <strong>Machine:</strong> ${r.machine.nom}<br>
                    <strong>Heure:</strong> ${r.heure_debut} (${parseInt(r.duree, 10)} ${parseInt(r.duree, 10) > 1 ? 'heures' : 'heure'})<br>
                    <strong>Par:</strong> ${r.user.nom} ${r.user.prenom}<br>
                    <strong>Description:</strong> ${r.description || '—'}
                </li>`;
            });
            html += '</ul>';
        }
        document.getElementById('modal-content').innerHTML = html + `<button id='close-reservation-modal' class='absolute top-2 right-2 text-2xl'>&times;</button>`;
        document.getElementById('reservation-modal').classList.remove('hidden');
    }
</script>
@endpush