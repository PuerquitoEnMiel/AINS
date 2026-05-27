@extends('layouts.app')

@section('header-title', 'User Management')
@section('header-subtitle', 'Manage platform users and roles')

@section('content')

<!-- Search & Filters -->
<div class="flex flex-col sm:flex-row gap-4 mb-6">
    <form method="GET" class="flex-1 flex gap-3">
        <div class="relative flex-1">
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
        </div>
        <select name="role" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none bg-white">
            <option value="">All Roles</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
            <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
        </select>
        <button type="submit" class="px-5 py-2.5 bg-ans-dark-green text-white rounded-xl font-semibold text-sm hover:bg-ans-seal-green transition-all shadow-md">Search</button>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Favorites</th>
                    <th class="px-6 py-4">Reviews</th>
                    <th class="px-6 py-4">Chats</th>
                    <th class="px-6 py-4">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $u)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($u->avatar)
                                <img src="{{ $u->avatar }}" alt="" class="w-9 h-9 rounded-full border border-gray-200">
                            @else
                                <div class="w-9 h-9 rounded-full bg-ans-dark-green text-white flex items-center justify-center text-sm font-bold">{{ substr($u->name, 0, 1) }}</div>
                            @endif
                            <span class="font-semibold text-gray-800">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-xs">{{ $u->email }}</td>
                    <td class="px-6 py-4">
                        <select onchange="updateRole({{ $u->id }}, this.value)" class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-semibold bg-white focus:ring-2 focus:ring-ans-dark-green/20 outline-none {{ $u->role === 'admin' ? 'text-red-600' : ($u->role === 'teacher' ? 'text-blue-600' : 'text-green-600') }}">
                            <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="teacher" {{ $u->role === 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="student" {{ $u->role === 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </td>
                    <td class="px-6 py-4 text-center text-gray-600">{{ $u->favorites_count }}</td>
                    <td class="px-6 py-4 text-center text-gray-600">{{ $u->reviews_count }}</td>
                    <td class="px-6 py-4 text-center text-gray-600">{{ $u->conversations_count }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $u->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $users->links() }}
</div>

<script>
function updateRole(userId, role) {
    fetch(`/admin/users/${userId}/role`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ role })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Brief flash effect
            const row = event.target.closest('tr');
            row.style.backgroundColor = '#F0FDF4';
            setTimeout(() => row.style.backgroundColor = '', 1000);
        }
    });
}
</script>

@endsection
