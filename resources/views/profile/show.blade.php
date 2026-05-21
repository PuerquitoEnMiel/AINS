@extends('layouts.app')

@section('header-title', 'My Profile')
@section('header-subtitle', 'Manage your account settings')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <!-- Cover -->
            <div class="h-24 bg-gradient-to-br from-ans-dark-green via-ans-seal-green to-ans-dark-green relative">
                <div class="absolute -bottom-10 left-6">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="" class="w-20 h-20 rounded-2xl border-4 border-white shadow-lg object-cover">
                    @else
                        <div class="w-20 h-20 rounded-2xl border-4 border-white shadow-lg bg-ans-orange flex items-center justify-center text-white font-bold text-2xl">{{ substr($user->name, 0, 1) }}</div>
                    @endif
                </div>
            </div>
            <div class="pt-14 px-6 pb-6">
                <h3 class="text-xl font-heading font-bold text-gray-800">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-2.5 py-1 bg-ans-dark-green/10 text-ans-dark-green text-xs font-bold rounded-lg uppercase">{{ $user->role }}</span>
                    @if($user->department)
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg">{{ $user->department }}</span>
                    @endif
                </div>
                @if($user->bio)
                    <p class="text-sm text-gray-600 mt-4 leading-relaxed">{{ $user->bio }}</p>
                @endif

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-3 mt-6 pt-5 border-t border-gray-100">
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-800">{{ $user->favorites_count }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wide">Favorites</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-800">{{ $user->reviews_count }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wide">Reviews</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-800">{{ $user->conversations_count }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wide">Chats</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-6">
            <h4 class="font-heading font-bold text-gray-800 mb-4">Edit Profile</h4>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Department</label>
                    <input type="text" name="department" value="{{ old('department', $user->department) }}" placeholder="e.g. Mathematics" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Bio</label>
                    <textarea name="bio" rows="3" placeholder="Tell us about yourself..." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all resize-none">{{ old('bio', $user->bio) }}</textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-ans-dark-green text-white rounded-xl font-semibold hover:bg-ans-seal-green transition-all shadow-md text-sm">Save Changes</button>
            </form>
            @if(session('success'))
                <p class="text-sm text-green-600 mt-3 font-medium">✅ {{ session('success') }}</p>
            @endif
        </div>
    </div>

    <!-- Right Column: Favorites + Reviews -->
    <div class="lg:col-span-2 space-y-6">
        <!-- EdTech Badges -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-heading font-bold text-gray-800">🏅 My EdTech Badges</h4>
                <a href="{{ route('badges.index') }}" class="text-xs text-ans-dark-green font-semibold hover:underline">View All Badges →</a>
            </div>
            @if($badges->isEmpty())
                <div class="text-center py-6">
                    <p class="text-sm text-gray-400">No badges earned yet. Take quizzes in the gallery to earn them!</p>
                    <a href="{{ route('badges.index') }}" class="inline-block mt-3 px-4 py-2 bg-ans-dark-green text-white text-xs font-semibold rounded-xl hover:bg-ans-seal-green transition-all shadow-sm">Explore Badges</a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($badges as $badge)
                    <a href="{{ route('badges.show', $badge->slug) }}" class="flex flex-col items-center text-center p-3 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-md hover:scale-105 transition-all group">
                        <span class="text-4xl mb-2 group-hover:scale-110 transition-transform">{{ $badge->icon }}</span>
                        <span class="font-semibold text-gray-800 text-xs truncate w-full" title="{{ $badge->name }}">{{ $badge->name }}</span>
                        <span class="text-[9px] text-gray-400 mt-1 uppercase font-bold tracking-wider" style="color: {{ $badge->color }}">{{ $badge->difficulty }}</span>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Favorite Tools -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-heading font-bold text-gray-800">❤️ Favorite Tools</h4>
                <a href="{{ route('favorites.index') }}" class="text-xs text-ans-dark-green font-semibold hover:underline">View All →</a>
            </div>
            @if($favorites->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">No favorites yet. Browse the catalog to bookmark tools.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($favorites as $tool)
                    <a href="{{ route('tools.show', $tool) }}" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors group">
                        @if($tool->logo_url)
                            <img src="{{ $tool->logo_url }}" alt="" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-ans-dark-green to-ans-light-green flex items-center justify-center text-white font-bold text-sm">{{ substr($tool->name, 0, 2) }}</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate group-hover:text-ans-dark-green transition-colors">{{ $tool->name }}</p>
                            <p class="text-xs text-gray-500">⭐ {{ number_format($tool->avg_rating, 1) }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Reviews -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h4 class="font-heading font-bold text-gray-800 mb-4">📝 My Reviews</h4>
            @if($reviews->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">You haven't reviewed any tools yet.</p>
            @else
                <div class="space-y-4">
                    @foreach($reviews as $review)
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <a href="{{ route('tools.show', $review->tool) }}" class="font-semibold text-gray-800 hover:text-ans-dark-green transition-colors">{{ $review->tool->name }}</a>
                            <div class="flex gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
