@extends('layouts.app')

@section('content')
@push('styles')
<style>
    body, html {
        height: 100vh !important;
        margin: 0 !important;
        overflow: hidden !important;
    }
    main {
        min-height: 100vh !important;
        height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
        box-sizing: border-box !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #f3f4f6 !important;
    }
</style>
@endpush
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-sm bg-white rounded-xl shadow p-6 text-sm">
        <h1 class="text-xl font-bold text-center text-gray-800 mb-1">Créer un compte</h1>
        <p class="text-xs text-gray-500 text-center mb-4">Rejoignez notre communauté FabLab</p>
        @if(session('error') || session('success') || $errors->any())
            <div class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs mb-2">
                {{ session('error') ?? session('success') ?? $errors->first() }}
            </div>
        @endif
        <form action="{{ route('user.signup.store') }}" method="POST" class="space-y-2">
            @csrf
            <input type="hidden" name="user_type" value="user">
            <div>
                <label class="block text-xs text-gray-700 mb-1">Nom</label>
                <input type="text" name="nom" value="{{ old('nom') }}" required class="w-full border rounded text-xs px-2 py-1 mb-1" placeholder="Entrez votre nom">
            </div>
            <div>
                <label class="block text-xs text-gray-700 mb-1">Prénom</label>
                <input type="text" name="prenom" value="{{ old('prenom') }}" required class="w-full border rounded text-xs px-2 py-1 mb-1" placeholder="Entrez votre prénom">
            </div>
            <div>
                <label class="block text-xs text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded text-xs px-2 py-1 mb-1" placeholder="exemple@email.com">
            </div>
            <div>
                <label class="block text-xs text-gray-700 mb-1">Téléphone</label>
                <input type="tel" name="telephone" value="{{ old('telephone') }}" required class="w-full border rounded text-xs px-2 py-1 mb-1" placeholder="Entrez votre numéro de téléphone">
            </div>
            <div>
                <label class="block text-xs text-gray-700 mb-1">Adresse</label>
                <input type="text" name="adresse" value="{{ old('adresse') }}" required class="w-full border rounded text-xs px-2 py-1 mb-1" placeholder="Entrez votre adresse">
            </div>
            <div>
                <label class="block text-xs text-gray-700 mb-1">Mot de passe</label>
                <input type="password" name="password" required class="w-full border rounded text-xs px-2 py-1 mb-1" placeholder="Minimum 8 caractères">
                <p class="text-xxs text-gray-400 mt-0.5">Le mot de passe doit contenir au moins 8 caractères</p>
            </div>
            <div>
                <label class="block text-xs text-gray-700 mb-1">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required class="w-full border rounded text-xs px-2 py-1 mb-1" placeholder="Confirmez votre mot de passe">
            </div>
            <div>
                <button type="submit" class="w-full rounded text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 py-2">S'inscrire</button>
            </div>
        </form>
        <div class="mt-3 text-center">
            <a href="{{ route('user.login') }}" class="text-xs text-blue-600 hover:underline">Retour à la connexion</a>
        </div>
    </div>
</div>
@endsection