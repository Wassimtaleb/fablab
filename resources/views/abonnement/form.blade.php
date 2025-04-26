@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Formulaire d'abonnement</div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($demandeEnAttente)
                        <div class="alert alert-info">
                            Vous avez déjà une demande d'abonnement en attente de validation.
                            <a href="{{ route('abonnement.status') }}">Voir le statut</a>
                        </div>
                    @else
                        <form method="POST" action="{{ route('abonnement.subscribe') }}">
                            @csrf

                            <div class="form-group row mb-3">
                                <label for="type" class="col-md-4 col-form-label text-md-right">Type d'abonnement</label>

                                <div class="col-md-6">
                                    <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="mensuel">Mensuel (29.99€/mois)</option>
                                        <option value="annuel">Annuel (299.99€/an)</option>
                                    </select>

                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Soumettre la demande
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 