@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="border-2 border-green-400 rounded-xl bg-white shadow-lg p-6">
        <h3 class="font-bold mb-6 text-2xl flex items-center justify-center">
            <i class="fas fa-coins mr-2 text-green-600"></i>Rapport financier
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Paiements reçus par mois (bar chart) -->
            <div class="bg-white rounded-lg shadow p-4 border border-green-200">
                <h4 class="font-semibold mb-2 flex items-center">
                    <i class="fas fa-calendar-alt mr-2 text-green-400"></i>Paiements reçus par mois (12 derniers mois)
                </h4>
                <canvas id="paiementsChart" height="160"></canvas>
            </div>
            <!-- Statistiques par type d'abonnement (pie chart) -->
            <div class="bg-white rounded-lg shadow p-4 border border-green-200">
                <h4 class="font-semibold mb-2 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-green-400"></i>Statistiques par type d'abonnement
                </h4>
                <canvas id="typeChart" height="160"></canvas>
            </div>
        </div>
        <div class="mt-8">
            <h4 class="font-bold text-lg flex items-center">
                <i class="fas fa-euro-sign mr-2 text-green-600"></i>Total des recettes (tous types d'abonnement confondus)
            </h4>
            <div class="text-2xl font-bold text-green-700 mt-2">
                {{ number_format(collect($statsType)->sum('total'), 2, ',', ' ') }} DT
            </div>
            <div class="mt-4">
                {{-- <h5 class="font-semibold mb-2">Détail par type d'abonnement :</h5> --}}
                <ul class="list-disc list-inside">
                    {{-- @foreach($statsType as $item) --}}
                        {{-- <li>{{ $item->type === 'mensuel' ? 'Mensuel' : ($item->type === 'annuel' ? 'Annuel' : (isset(config('abonnements.types')[$item->type]) ? config('abonnements.types')[$item->type]['name'] : $item->type)) }} : {{ number_format($item->total, 2, ',', ' ') }} DT</li> --}}
                    {{-- @endforeach --}}
                </ul>
            </div>
        </div>
        <div class="mt-10 flex justify-end">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour au tableau de bord
            </a>
        </div>
    </div>
</div>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const moisLabels = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
    // Palette harmonisée pour types
    const typeColors = [
        'rgba(34,197,94,0.7)',    // Vert (par défaut)
        'rgba(231, 70, 70, 0.7)', // Rouge pour Annuel (remplace Violet)
        'rgba(251,191,36,0.7)',   // Jaune
        'rgba(59,130,246,0.7)',   // Bleu
        'rgba(251,146,60,0.7)'    // Orange
    ];
    // Création dynamique des paiements par type et par mois
    const paiementsParTypeMois = {};
    @foreach($statsType as $itemType)
        paiementsParTypeMois['{{ $itemType->type }}'] = Array(12).fill(0);
    @endforeach
    // Remplissage avec les données du backend (chaque entrée a .mois, .type, .total)
    @foreach($paiementsParMois as $item)
        paiementsParTypeMois['{{ $item->type }}'][{{ $item->mois - 1 }}] = {{ $item->total }};
    @endforeach
    // Données globales (tous types)
    let paiementsData = Array(12).fill(0);
    @foreach($paiementsParMois as $item)
        paiementsData[{{ $item->mois - 1 }}] += {{ $item->total }};
    @endforeach
    let selectedType = null;
    let paiementsChart;
    const typeLabels = [
        @foreach($statsType as $item)
            @if($item->type === 'mensuel')
                'Mensuel',
            @elseif($item->type === 'annuel')
                'Annuel',
            @elseif(isset(config('abonnements.types')[$item->type]))
                '{{ config('abonnements.types')[$item->type]['name'] }}',
            @else
                '{{ $item->type }}',
            @endif
        @endforeach
    ];
    const typeKeys = [
        @foreach($statsType as $item)
            '{{ $item->type }}',
        @endforeach
    ];
    const typeData = [
        @foreach($statsType as $item)
            {{ $item->count }},
        @endforeach
    ];
    // Couleurs pour chaque type (même ordre pour bar chart et pie chart)
    function getTypeColor(idx) {
        return typeColors[idx % typeColors.length];
    }
    function renderPaiementsChart(data, label, color) {
        if (paiementsChart) {
            paiementsChart.data.datasets[0].data = data;
            paiementsChart.data.datasets[0].label = label;
            paiementsChart.data.datasets[0].backgroundColor = color || 'rgba(34,197,94,0.5)';
            paiementsChart.data.datasets[0].borderColor = color ? color.replace('0.7','1') : 'rgba(34,197,94,1)';
            paiementsChart.update();
        } else {
            paiementsChart = new Chart(document.getElementById('paiementsChart'), {
                type: 'bar',
                data: {
                    labels: moisLabels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: color || 'rgba(34,197,94,0.5)',
                        borderColor: color ? color.replace('0.7','1') : 'rgba(34,197,94,1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    }
    renderPaiementsChart(paiementsData, 'Montant reçu (DT)');
    const typeChart = new Chart(document.getElementById('typeChart'), {
        type: 'pie',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeData,
                backgroundColor: typeColors.slice(0, typeLabels.length)
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            onClick: function(evt, elements) {
                if (elements && elements.length > 0) {
                    const idx = elements[0].index;
                    const type = typeKeys[idx];
                    if (selectedType === type) {
                        selectedType = null;
                        renderPaiementsChart(paiementsData, 'Montant reçu (DT)');
                    } else {
                        selectedType = type;
                        renderPaiementsChart(paiementsParTypeMois[type] ?? Array(12).fill(0), 'Montant reçu (' + (typeLabels[idx]) + ', DT)', getTypeColor(idx));
                    }
                }
            }
        }
    });
</script>
@endsection
