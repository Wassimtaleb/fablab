<!-- Barre de navigation pour les pages d'administration -->
<div class="bg-white shadow mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between py-3">
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Tableau de bord
                </a>
                
                @if(isset($currentPage) && $currentPage !== 'dashboard')
                <h2 class="text-lg font-semibold text-gray-800 self-center">{{ $pageTitle ?? 'Administration' }}</h2>
                @endif
            </div>
            
            <div class="flex items-center space-x-2">
                @if(session('admin'))
                <span class="text-sm text-gray-600">{{ session('admin')->nom }} {{ session('admin')->prenom }}</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-md">
                        <i class="fas fa-sign-out-alt mr-1"></i> Déconnexion
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
