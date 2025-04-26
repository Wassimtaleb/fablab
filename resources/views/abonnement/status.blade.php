@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Statut de votre abonnement</div>

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

                    @if($abonnement)
                        <div class="alert alert-info">
                            <h5>Détails de votre abonnement :</h5>
                            <p><strong>Type :</strong> {{ ucfirst($abonnement->type) }}</p>
                            <p><strong>Statut :</strong> 
                                @if($abonnement->statut == 'en_attente')
                                    <span class="text-warning">En attente de validation</span>
                                @elseif($abonnement->statut == 'accepte')
                                    <span class="text-success">Accepté</span>
                                @elseif($abonnement->statut == 'refuse')
                                    <span class="text-danger">Refusé</span>
                                @endif
                            </p>
                            <p><strong>Date de début :</strong> {{ $abonnement->date_debut ? $abonnement->date_debut->format('d/m/Y') : 'Non définie' }}</p>
                            <p><strong>Date de fin :</strong> {{ $abonnement->date_fin ? $abonnement->date_fin->format('d/m/Y') : 'Non définie' }}</p>
                        </div>

                        @if($abonnement->statut == 'refuse')
                            <div class="mt-3">
                                <a href="{{ route('abonnement.form') }}" class="btn btn-primary">
                                    Faire une nouvelle demande
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            Vous n'avez pas encore fait de demande d'abonnement.
                            <a href="{{ route('abonnement.form') }}" class="btn btn-primary mt-3 d-block">
                                Faire une demande d'abonnement
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 