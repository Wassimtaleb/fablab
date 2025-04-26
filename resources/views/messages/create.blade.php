@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Nouveau message</h2>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route(session('user_type') === 'admin' ? 'admin.messages.store' : (session('user_type') === 'technicien' ? 'technicien.messages.store' : 'user.messages.store')) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="destinataire_type" class="form-label">Type de destinataire</label>
                            <select class="form-select @error('destinataire_type') is-invalid @enderror" id="destinataire_type" name="destinataire_type" required>
                                <option value="">Sélectionnez un type</option>
                                @if(isset($destinataires['admins']))
                                    <option value="App\\Models\\Admin">Administrateur</option>
                                @endif
                                @if(isset($destinataires['techniciens']))
                                    <option value="App\\Models\\Technicien">Technicien</option>
                                @endif
                                @if(isset($destinataires['utilisateurs']))
                                    <option value="App\\Models\\User">Utilisateur</option>
                                @endif
                            </select>
                            @error('destinataire_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="destinataire_id" class="form-label">Destinataire</label>
                            <select class="form-select @error('destinataire_id') is-invalid @enderror" id="destinataire_id" name="destinataire_id" required>
                                <option value="">Sélectionnez un destinataire</option>
                            </select>
                            @error('destinataire_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sujet" class="form-label">Sujet</label>
                            <input type="text" class="form-control @error('sujet') is-invalid @enderror" id="sujet" name="sujet" value="{{ old('sujet') }}" required>
                            @error('sujet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contenu" class="form-label">Contenu</label>
                            <textarea class="form-control @error('contenu') is-invalid @enderror" id="contenu" name="contenu" rows="5" required>{{ old('contenu') }}</textarea>
                            @error('contenu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                            <a href="{{ route(session('user_type') === 'admin' ? 'admin.messages.index' : (session('user_type') === 'technicien' ? 'technicien.messages.index' : 'user.messages.index')) }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const destinataireTypeSelect = document.getElementById('destinataire_type');
        const destinataireIdSelect = document.getElementById('destinataire_id');
        
        // Données des destinataires
        const destinataires = {
            @if(isset($destinataires['admins']))
                'App\\Models\\Admin': [
                    @foreach($destinataires['admins'] as $admin)
                        { id: {{ $admin->id }}, nom: '{{ $admin->nom }} {{ $admin->prenom }}' },
                    @endforeach
                ],
            @endif
            @if(isset($destinataires['techniciens']))
                'App\\Models\\Technicien': [
                    @foreach($destinataires['techniciens'] as $technicien)
                        { id: {{ $technicien->id }}, nom: '{{ $technicien->nom }} {{ $technicien->prenom }}' },
                    @endforeach
                ],
            @endif
            @if(isset($destinataires['utilisateurs']))
                'App\\Models\\User': [
                    @foreach($destinataires['utilisateurs'] as $user)
                        { id: {{ $user->id }}, nom: '{{ $user->nom }} {{ $user->prenom }}' },
                    @endforeach
                ],
            @endif
        };
        
        // Mettre à jour les destinataires en fonction du type sélectionné
        destinataireTypeSelect.addEventListener('change', function() {
            const type = this.value;
            destinataireIdSelect.innerHTML = '<option value="">Sélectionnez un destinataire</option>';
            
            if (type && destinataires[type]) {
                destinataires[type].forEach(function(destinataire) {
                    const option = document.createElement('option');
                    option.value = destinataire.id;
                    option.textContent = destinataire.nom;
                    destinataireIdSelect.appendChild(option);
                });
            }
        });
    });
</script>
@endpush
@endsection 