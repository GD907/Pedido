<!-- resources/views/filament/components/notifications-icon.blade.php -->
<div x-data="{ open: false }" class="relative">
    <!-- Ícono de campana para abrir el modal -->
    <button @click="open = true" class="relative focus:outline-none">
        <x-heroicon-o-bell class="w-6 h-6 text-gray-500" />
        @if (auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500"></span>
        @endif
    </button>

    <!-- Modal de Notificaciones -->
    <div x-show="open" x-cloak @click.away="open = false" class="absolute right-0 mt-2 w-72 bg-white shadow-lg rounded-lg p-4 z-50">
        <h3 class="text-lg font-semibold mb-4">Notificaciones</h3>
        <ul>
            @forelse (auth()->user()->unreadNotifications as $notification)
                <li class="p-2 border-b">
                    {{ $notification->data['message'] }}
                    <small class="text-gray-500 block">{{ $notification->created_at->diffForHumans() }}</small>
                </li>
            @empty
                <p class="text-gray-500 text-center">No tienes notificaciones nuevas</p>
            @endforelse
        </ul>
        <button @click="open = false" class="mt-2 text-sm text-blue-500 hover:underline">Cerrar</button>
    </div>
</div>
