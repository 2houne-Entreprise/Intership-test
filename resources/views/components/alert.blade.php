{{-- 
    Composant Alert réutilisable
    Types disponibles : success, error, warning, info
    
    Utilisation :
    <x-alert type="success" message="Opération réussie !" />
    <x-alert type="error" message="Une erreur est survenue" title="Erreur" />
--}}

@if($message)
    <div class="{{ $getColorClass() }} border-l-4 p-4 mb-4 rounded shadow-sm" role="alert">
        <div class="flex items-start">
            <div class="flex-shrink-0 text-xl mr-3">
                {{ $getIcon() }}
            </div>
            <div class="flex-1">
                @if($title)
                    <h4 class="font-bold mb-1">{{ $title }}</h4>
                @endif
                <p>{{ $message }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                    class="flex-shrink-0 ml-3 text-gray-400 hover:text-gray-600">
                ✕
            </button>
        </div>
    </div>
@endif