@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>{{ $message->sujet }}</h2>
                    <div>
                        <a href="{{ route(session('user_type') === 'admin' ? 'admin.messages.index' : (session('user_type') === 'technicien' ? 'technicien.messages.index' : 'user.messages.index')) }}" class="btn btn-secondary">Retour</a>
                        <form action="{{ route(session('user_type') === 'admin' ? 'admin.messages.destroy' : (session('user_type') === 'technicien' ? 'technicien.messages.destroy' : 'user.messages.destroy'), $message) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">Supprimer</button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-3 fw-bold">De :</div>
                            <div class="col-md-9">
                                @if($message->expediteur_type === 'App\\Models\\Admin')
                                    Admin
                                @elseif($message->expediteur_type === 'App\\Models\\Technicien')
                                    Technicien
                                @else
                                    Utilisateur
                                @endif
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3 fw-bold">À :</div>
                            <div class="col-md-9">
                                @if($message->destinataire_type === 'App\\Models\\Admin')
                                    Admin
                                @elseif($message->destinataire_type === 'App\\Models\\Technicien')
                                    Technicien
                                @else
                                    Utilisateur
                                @endif
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3 fw-bold">Date :</div>
                            <div class="col-md-9">{{ $message->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            {!! nl2br(e($message->contenu)) !!}
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route(session('user_type') === 'admin' ? 'admin.messages.create' : (session('user_type') === 'technicien' ? 'technicien.messages.create' : 'user.messages.create'), ['destinataire_type' => $message->expediteur_type, 'destinataire_id' => $message->expediteur_id]) }}" class="btn btn-primary">Répondre</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 