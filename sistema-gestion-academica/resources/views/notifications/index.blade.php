@extends('layouts.app')

@section('title', 'Notificaciones')
@section('header', 'Centro de Notificaciones')
@section('subheader', 'Avisos sobre inscripciones, calificaciones y actualizaciones de cursos')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div class="text-sm text-slate-600">
        Tienes <strong class="text-[#0F2744]">{{ $unreadCount }}</strong> notificaciones sin leer.
    </div>
    @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-xs font-semibold cursor-pointer">
                Marcar todas como leídas
            </button>
        </form>
    @endif
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm divide-y divide-slate-100">
    @forelse($notifications as $notif)
        <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $notif->isUnread() ? 'bg-sky-50/50' : '' }}">
            <div class="flex items-start gap-3.5">
                <span class="mt-1 w-2.5 h-2.5 rounded-full shrink-0 {{ $notif->isUnread() ? 'bg-sky-600' : 'bg-slate-300' }}"></span>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-sm text-[#0F2744]">{{ $notif->title }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-600">{{ $notif->type }}</span>
                    </div>
                    <p class="text-sm text-slate-600 mt-1">{{ $notif->message }}</p>
                    <div class="text-xs text-slate-400 mt-1.5">{{ $notif->created_at->format('d/m/Y H:i') }} · {{ $notif->created_at->diffForHumans() }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @if($notif->link)
                    <a href="{{ $notif->link }}" class="text-xs font-semibold text-sky-700 hover:underline">Ver detalle</a>
                @endif
                @if($notif->isUnread())
                    <form method="POST" action="{{ route('notifications.read', $notif) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100 cursor-pointer">
                            Marcar leída
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="p-10 text-center text-slate-500 text-sm">No tienes notificaciones registradas todavía.</div>
    @endforelse
</div>

<div class="mt-4">
    {{ $notifications->links() }}
</div>
@endsection
