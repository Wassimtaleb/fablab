@extends('layouts.app')
@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6">Connexion Administrateur</h2>
        @if(session('error'))
            <div class="bg-red-100 text-red-700 text-xs rounded p-2 mb-3">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 text-red-700 text-xs rounded p-2 mb-3">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus class="w-full border rounded text-sm px-3 py-2" placeholder="Entrez votre email">
            </div>
            <div>
                <label for="password" class="block text-sm text-gray-700 mb-1">Mot de passe</label>
                <input type="password" name="password" id="password" required class="w-full border rounded text-sm px-3 py-2" placeholder="Mot de passe">
            </div>
            <button type="submit" class="w-full rounded text-sm font-medium text-white bg-red-600 hover:bg-red-700 py-2">Se connecter</button>
        </form>
        <div class="text-center mt-3">
            <a href="{{ route('home') }}" class="text-xs text-blue-600 hover:underline">Retour à l'accueil</a>
        </div>
    </div>
</div>
@endsection
