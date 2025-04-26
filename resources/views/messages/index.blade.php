@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Messages</h2>
                    <a href="{{ route(session('user_type') === 'admin' ? 'admin.messages.create' : (session('user_type') === 'technicien' ? 'technicien.messages.create' : 'user.messages.create')) }}" class="btn btn-primary">Nouveau message</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <ul class="nav nav-tabs mb-4" id="messageTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="recus-tab" data-bs-toggle="tab" data-bs-target="#recus" type="button" role="tab" aria-controls="recus" aria-selected="true">
                                Messages reçus
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="envoyes-tab" data-bs-toggle="tab" data-bs-target="#envoyes" type="button" role="tab" aria-controls="envoyes" aria-selected="false">
                                Messages envoyés
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="messageTabsContent">
                        <div class="tab-pane fade show active" id="recus" role="tabpanel" aria-labelledby="recus-tab">
                            @if($messagesRecus->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>De</th>
                                                <th>Sujet</th>
                                                <th>Date</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($messagesRecus as $message)
                                                <tr>
                                                    <td>
                                                        @if($message->expediteur_type === 'App\\Models\\Admin')
                                                            Admin
                                                        @elseif($message->expediteur_type === 'App\\Models\\Technicien')
                                                            Technicien
                                                        @else
                                                            Utilisateur
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(!$message->lu)
                                                            <strong>{{ $message->sujet }}</strong>
                                                        @else
                                                            {{ $message->sujet }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @if($message->lu)
                                                            <span class="badge bg-secondary">Lu</span>
                                                        @else
                                                            <span class="badge bg-primary">Non lu</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route(session('user_type') === 'admin' ? 'admin.messages.show' : (session('user_type') === 'technicien' ? 'technicien.messages.show' : 'user.messages.show'), $message) }}" class="btn btn-sm btn-info">Voir</a>
                                                        <form action="{{ route(session('user_type') === 'admin' ? 'admin.messages.destroy' : (session('user_type') === 'technicien' ? 'technicien.messages.destroy' : 'user.messages.destroy'), $message) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">Supprimer</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>Aucun message reçu.</p>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="envoyes" role="tabpanel" aria-labelledby="envoyes-tab">
                            @if($messagesEnvoyes->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>À</th>
                                                <th>Sujet</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($messagesEnvoyes as $message)
                                                <tr>
                                                    <td>
                                                        @if($message->destinataire_type === 'App\\Models\\Admin')
                                                            Admin
                                                        @elseif($message->destinataire_type === 'App\\Models\\Technicien')
                                                            Technicien
                                                        @else
                                                            Utilisateur
                                                        @endif
                                                    </td>
                                                    <td>{{ $message->sujet }}</td>
                                                    <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <a href="{{ route(session('user_type') === 'admin' ? 'admin.messages.show' : (session('user_type') === 'technicien' ? 'technicien.messages.show' : 'user.messages.show'), $message) }}" class="btn btn-sm btn-info">Voir</a>
                                                        <form action="{{ route(session('user_type') === 'admin' ? 'admin.messages.destroy' : (session('user_type') === 'technicien' ? 'technicien.messages.destroy' : 'user.messages.destroy'), $message) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">Supprimer</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>Aucun message envoyé.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 