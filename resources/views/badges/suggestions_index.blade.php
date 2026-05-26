@extends('layouts.app')

@section('header-title', 'Mis Sugerencias de Insignias')
@section('header-subtitle', 'Monitorea las insignias que has sugerido al equipo administrativo')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('badges.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-ans-dark-green transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Volver a Insignias</span>
        </a>

        <a href="{{ route('badge-suggestions.create') }}" class="px-4 py-2 bg-ans-dark-green text-white text-xs font-semibold rounded-xl hover:bg-ans-seal-green transition-all shadow-md">
            + Sugerir Otra Insignia
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium">✅ {{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nombre de Insignia</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Descripción</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Enviada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($suggestions as $suggestion)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-gray-800">
                            {{ $suggestion->name }}
                            @if($suggestion->certification_url)
                            <a href="{{ $suggestion->certification_url }}" target="_blank" class="block text-[11px] text-blue-600 hover:underline mt-0.5">Enlace Oficial</a>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-600 max-w-xs truncate">{{ $suggestion->description }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColor = match($suggestion->status) {
                                    'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                };
                                $statusText = match($suggestion->status) {
                                    'pending' => 'Pendiente',
                                    'approved' => 'Aprobada',
                                    'rejected' => 'Rechazada',
                                    default => 'Desconocido'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold capitalize border {{ $statusColor }}">
                                {{ $statusText }}
                            </span>
                            @if($suggestion->admin_notes)
                            <p class="text-[11px] text-gray-400 mt-1 italic">Nota Admin: "{{ $suggestion->admin_notes }}"</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-400">{{ $suggestion->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">
                            Aún no has sugerido ninguna insignia.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
