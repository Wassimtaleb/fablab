@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="border-2 border-gray-300 rounded-xl bg-white shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6 flex items-center justify-center">
                <i class="fas fa-chart-bar mr-2"></i>Statistiques d'utilisation
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Réservations par mois -->
                <div class="bg-white rounded-lg shadow p-4 border border-blue-200">
                    <h4 class="font-semibold mb-2 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>Réservations par mois
                    </h4>
                    <canvas id="reservationsChart" height="160"></canvas>
                </div>
                <!-- Nouveaux utilisateurs par mois -->
                <div class="bg-white rounded-lg shadow p-4 border border-green-200">
                    <h4 class="font-semibold mb-2 flex items-center">
                        <i class="fas fa-user-plus mr-2 text-green-500"></i>Nouveaux utilisateurs par mois
                    </h4>
                    <canvas id="usersChart" height="160"></canvas>
                </div>
                <!-- Abonnements créés par mois -->
                <div class="bg-white rounded-lg shadow p-4 border border-purple-200">
                    <h4 class="font-semibold mb-2 flex items-center">
                        <i class="fas fa-id-card-alt mr-2 text-purple-500"></i>Abonnements créés par mois
                    </h4>
                    <canvas id="abonnementsChart" height="160"></canvas>
                </div>
                <!-- Top 3 machines les plus utilisées -->
                <div class="bg-white rounded-lg shadow p-4 border border-yellow-200">
                    <h4 class="font-semibold mb-2 flex items-center">
                        <i class="fas fa-crown mr-2 text-yellow-500"></i>Top 3 machines les plus utilisées
                    </h4>
                    <canvas id="machinesChart" height="160"></canvas>
                </div>
            </div>
            <div class="mt-10 flex justify-end">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Retour au tableau de bord
                </a>
            </div>
            <footer class="mt-8 text-center text-gray-400 text-sm"> 2025 FabLab. Tous droits réservés.</footer>
        </div>
    </div>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Labels pour les mois
        const moisLabels = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
        // Réservations par mois
        const reservationsData = @json($reservationsParMois);
        new Chart(document.getElementById('reservationsChart'), {
            type: 'bar',
            data: {
                labels: moisLabels,
                datasets: [{
                    label: 'Réservations',
                    data: reservationsData,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
        // Nouveaux utilisateurs par mois
        const usersData = @json($usersParMois);
        new Chart(document.getElementById('usersChart'), {
            type: 'bar',
            data: {
                labels: moisLabels,
                datasets: [{
                    label: 'Nouveaux utilisateurs',
                    data: usersData,
                    backgroundColor: 'rgba(34, 197, 94, 0.5)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
        // Abonnements créés par mois
        const abonnementsData = @json($abonnementsParMois);
        new Chart(document.getElementById('abonnementsChart'), {
            type: 'bar',
            data: {
                labels: moisLabels,
                datasets: [{
                    label: 'Abonnements créés',
                    data: abonnementsData,
                    backgroundColor: 'rgba(139, 92, 246, 0.5)',
                    borderColor: 'rgba(139, 92, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
        // Top 3 machines les plus utilisées
        const machinesLabels = [
            @foreach($topMachines as $item)
                @if($item->machine && $item->machine->nom)
                    "{{ $item->machine->nom }}",
                @else
                    "Machine #{{ $item->machine_id }}",
                @endif
            @endforeach
        ];
        const machinesData = [
            @foreach($topMachines as $item)
                {{ $item->total }},
            @endforeach
        ];
        new Chart(document.getElementById('machinesChart'), {
            type: 'bar',
            data: {
                labels: machinesLabels,
                datasets: [{
                    label: 'Nombre de réservations',
                    data: machinesData,
                    backgroundColor: [
                        'rgba(251, 191, 36, 0.7)', // Jaune
                        'rgba(156, 163, 175, 0.7)', // Gris
                        'rgba(251, 146, 60, 0.7)'  // Orange
                    ],
                    borderColor: [
                        'rgba(251, 191, 36, 1)',
                        'rgba(156, 163, 175, 1)',
                        'rgba(251, 146, 60, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y', // Barres horizontales
                scales: { x: { beginAtZero: true } }
            }
        });
    </script>
@endsection
