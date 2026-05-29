<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    /**
     * Public index — filtered, paginated, role-aware.
     */
    public function index(Request $request)
    {
        $tab    = $request->input('tab', 'all');       // all | saved | proposals
        $area   = $request->input('area', 'all');
        $type   = $request->input('type', 'all');
        $search = $request->input('search');

        $user = Auth::user();

        // Base query: published resources
        $query = Resource::published()
            ->byArea($area)
            ->byType($type)
            ->search($search)
            ->latest();

        // Role filter: only show resources targeting this role (or 'all')
        if ($user) {
            $query->forRole($user->role);
        }

        $resources = $query->paginate(12)->withQueryString();

        // Saved resources tab
        $savedResources = null;
        if ($tab === 'saved' && $user) {
            $savedResources = $user->savedResources()
                ->byArea($area)
                ->byType($type)
                ->search($search)
                ->paginate(12)
                ->withQueryString();
        }

        // Teacher proposals tab
        $proposals = null;
        if ($tab === 'proposals' && $user && ($user->isTeacher() || $user->isAdmin())) {
            $proposals = Resource::where('created_by', $user->id)
                ->byArea($area)
                ->byType($type)
                ->search($search)
                ->latest()
                ->paginate(12)
                ->withQueryString();
        }

        // Admin pending count (for badge)
        $pendingCount = 0;
        if ($user?->isAdmin()) {
            $pendingCount = Resource::where('is_published', false)->count();
        }

        // Saved IDs for current user (to show ♡ state)
        $savedIds = $user
            ? $user->savedResources()->pluck('resources.id')->toArray()
            : [];

        return view('resources.index', compact(
            'resources', 'savedResources', 'proposals',
            'pendingCount', 'savedIds',
            'tab', 'area', 'type', 'search'
        ));
    }

    /**
     * Public resource detail.
     */
    public function show(Resource $resource)
    {
        abort_if(!$resource->is_published, 404);

        $related = Resource::published()
            ->where('area', $resource->area)
            ->where('id', '!=', $resource->id)
            ->limit(4)
            ->get();

        $isSaved = Auth::check()
            ? Auth::user()->savedResources()->where('resources.id', $resource->id)->exists()
            : false;

        return view('resources.show', compact('resource', 'related', 'isSaved'));
    }

    /**
     * Teacher: show propose form.
     */
    public function create()
    {
        $this->authorizeRole(['teacher', 'admin']);
        return view('resources.propose');
    }

    /**
     * Teacher: store a new (unpublished) resource.
     */
    public function store(Request $request)
    {
        $this->authorizeRole(['teacher', 'admin']);

        $validated = $request->validate([
            'title'        => 'required|string|max:200',
            'description'  => 'nullable|string|max:1000',
            'url'          => 'nullable|url|max:500',
            'type'         => 'required|in:link,book,video,article,tool,course',
            'area'         => 'required|in:stem,innovation,ai,robotics,design,programming,math,science,general',
            'target_roles' => 'required|array',
            'target_roles.*' => 'in:student,teacher,all',
            'thumbnail_url'=> 'nullable|url|max:500',
            'author'       => 'nullable|string|max:150',
            'source'       => 'nullable|string|max:150',
        ]);

        $validated['is_published'] = Auth::user()->isAdmin(); // Admin → auto-publish
        $validated['created_by']   = Auth::id();

        Resource::create($validated);

        $msg = Auth::user()->isAdmin()
            ? 'Resource published successfully.'
            : 'Resource submitted for review. An admin will approve it shortly.';

        return redirect()->route('resources.index')->with('success', $msg);
    }

    /**
     * Toggle bookmark on a resource.
     */
    public function save(Resource $resource)
    {
        abort_if(!Auth::check(), 401);

        $user = Auth::user();
        $exists = $user->savedResources()->where('resources.id', $resource->id)->exists();

        if ($exists) {
            $user->savedResources()->detach($resource->id);
            $saved = false;
        } else {
            $user->savedResources()->attach($resource->id, ['saved_at' => now()]);
            $saved = true;
        }

        if (request()->expectsJson()) {
            return response()->json(['saved' => $saved]);
        }

        return back()->with('success', $saved ? 'Resource saved!' : 'Resource removed from saved.');
    }

    // ── Private Helpers ───────────────────────────────────────────

    private function authorizeRole(array $roles): void
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            abort(403, 'Unauthorized.');
        }
    }
}
