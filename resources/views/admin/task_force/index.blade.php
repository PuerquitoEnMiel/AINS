@extends('layouts.app')

@section('header-title', 'Task Force Management')
@section('header-subtitle', 'Manage AI Task Force members')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium">✅ {{ session('success') }}</div>
@endif

<div class="flex items-center justify-between mb-6">
    <h3 class="text-lg font-heading font-bold text-gray-800">Miembros del Comité</h3>
    <a href="{{ route('admin.task-force.create') }}" class="px-5 py-2.5 bg-ans-dark-green text-white rounded-xl font-semibold text-sm hover:bg-ans-seal-green transition-all shadow-md">+ Nuevo Miembro</a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100">
                <th class="px-6 py-4">Avatar</th>
                <th class="px-6 py-4">Nombre</th>
                <th class="px-6 py-4">Rol / Puesto</th>
                <th class="px-6 py-4">Email</th>
                <th class="px-6 py-4">Orden</th>
                <th class="px-6 py-4 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($members as $member)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    @if($member->image_url)
                        <img src="{{ $member->image_url }}" alt="{{ $member->name }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-inner" style="background-color: {{ $member->avatar_color }}">
                            {{ $member->initials }}
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 font-semibold text-gray-800">{{ $member->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $member->role }}</td>
                <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $member->email }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $member->sort_order }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.task-force.edit', $member) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">Editar</a>
                        <form method="POST" action="{{ route('admin.task-force.destroy', $member) }}" onsubmit="return confirm('¿Eliminar este miembro?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
