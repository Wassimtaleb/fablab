@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Détails de la maintenance</h2>
                    <div>
                        <a href="{{ route('maintenances.index') }}" class="btn btn-secondary">Retour</a>
                        <a href="{{ route('maintenances.edit', $maintenance) }}" class="btn btn-primary">Modifier</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 fw-bold">Machine :</div>
                        <div class="col-md-8">{{ $maintenance->machine->nom }} ({{ $maintenance->machine->type }})</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 fw-bold">Technicien :</div>
                        <div class="col-md-8">{{ $maintenance->technicien->nom }} {{ $maintenance->technicien->prenom }}</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 fw-bold">Type :</div>
                        <div class="col-md-8">{{ $maintenance->type }}</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 fw-bold">Description :</div>
                        <div class="col-md-8">{{ $maintenance->description }}</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 fw-bold">Date de début :</div>
                        <div class="col-md-8">{{ $maintenance->date_debut->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 fw-bold">Date de fin :</div>
                        <div class="col-md-8">
                            @if($maintenance->date_fin)
                                {{ $maintenance->date_fin->format('d/m/Y H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 fw-bold">Statut :</div>
                        <div class="col-md-8">
                            @if($maintenance->statut === 'en_cours')
                                <span class="badge bg-warning">En cours</span>
                            @elseif($maintenance->statut === 'terminee')
                                <span class="badge bg-success">Terminée</span>
                            @elseif($maintenance->statut === 'planifiee')
                                <span class="badge bg-info">Planifiée</span>
                            @else
                                <span class="badge bg-danger">Annulée</span>
                            @endif
                        </div>
                    </div>

                    @if($maintenance->notes)
                        <div class="row mb-4">
                            <div class="col-md-4 fw-bold">Notes :</div>
                            <div class="col-md-8">{{ $maintenance->notes }}</div>
                        </div>
                    @endif

                    <div class="d-grid gap-2 mt-4">
                        <form action="{{ route('maintenances.destroy', $maintenance) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette maintenance ?')">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 