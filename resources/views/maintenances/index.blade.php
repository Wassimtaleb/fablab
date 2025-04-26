@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Maintenances</h2>
                    <a href="{{ route('maintenances.create') }}" class="btn btn-primary">Nouvelle maintenance</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Machine</th>
                                    <th>Technicien</th>
                                    <th>Type</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($maintenances as $maintenance)
                                    <tr>
                                        <td>{{ $maintenance->machine->nom }}</td>
                                        <td>{{ $maintenance->technicien->nom }} {{ $maintenance->technicien->prenom }}</td>
                                        <td>{{ $maintenance->type }}</td>
                                        <td>{{ $maintenance->date_debut->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($maintenance->date_fin)
                                                {{ $maintenance->date_fin->format('d/m/Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($maintenance->statut === 'en_cours')
                                                <span class="badge bg-warning">En cours</span>
                                            @elseif($maintenance->statut === 'terminee')
                                                <span class="badge bg-success">Terminée</span>
                                            @elseif($maintenance->statut === 'planifiee')
                                                <span class="badge bg-info">Planifiée</span>
                                            @else
                                                <span class="badge bg-danger">Annulée</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('maintenances.show', $maintenance) }}" class="btn btn-sm btn-info">Voir</a>
                                            <a href="{{ route('maintenances.edit', $maintenance) }}" class="btn btn-sm btn-primary">Modifier</a>
                                            <form action="{{ route('maintenances.destroy', $maintenance) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette maintenance ?')">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune maintenance trouvée.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 