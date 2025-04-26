@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Nouvelle maintenance</h2>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('maintenances.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="machine_id" class="form-label">Machine</label>
                            <select class="form-select @error('machine_id') is-invalid @enderror" id="machine_id" name="machine_id" required>
                                <option value="">Sélectionnez une machine</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->id }}" {{ old('machine_id', request('machine_id')) == $machine->id ? 'selected' : '' }}>
                                        {{ $machine->nom }} ({{ $machine->type }})
                                    </option>
                                @endforeach
                            </select>
                            @error('machine_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="technicien_id" class="form-label">Technicien</label>
                            <select class="form-select @error('technicien_id') is-invalid @enderror" id="technicien_id" name="technicien_id" required>
                                <option value="">Sélectionnez un technicien</option>
                                @foreach($techniciens as $technicien)
                                    <option value="{{ $technicien->id }}" {{ old('technicien_id') == $technicien->id ? 'selected' : '' }}>
                                        {{ $technicien->nom }} {{ $technicien->prenom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('technicien_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type de maintenance</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="preventive" {{ old('type') == 'preventive' ? 'selected' : '' }}>Préventive</option>
                                <option value="corrective" {{ old('type') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                                <option value="calibration" {{ old('type') == 'calibration' ? 'selected' : '' }}>Calibration</option>
                                <option value="nettoyage" {{ old('type') == 'nettoyage' ? 'selected' : '' }}>Nettoyage</option>
                                <option value="autre" {{ old('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date_debut" class="form-label">Date de début</label>
                            <input type="datetime-local" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut', date('Y-m-d\TH:i')) }}" required>
                            @error('date_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date_fin" class="form-label">Date de fin (optionnelle)</label>
                            <input type="datetime-local" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin') }}">
                            @error('date_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                <option value="planifiee" {{ old('statut') == 'planifiee' ? 'selected' : '' }}>Planifiée</option>
                                <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="terminee" {{ old('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                                <option value="annulee" {{ old('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (optionnelles)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Créer la maintenance</button>
                            <a href="{{ route('maintenances.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 