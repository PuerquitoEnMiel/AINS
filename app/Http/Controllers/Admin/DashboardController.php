<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\Tool;
use App\Models\ToolRequest;
use App\Models\ToolView;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Summary Stats ───────────────────────────────────────
        $stats = [
            'total_users'      => User::count(),
            'total_tools'      => Tool::approved()->count(),
            'pending_requests' => ToolRequest::where('status', 'pending')->count(),
            'total_chats'      => ChatConversation::count(),
            'total_reviews'    => DB::table('reviews')->count(),
            'total_views'      => ToolView::count(),
        ];

        // ── Role Breakdown ──────────────────────────────────────
        $roleBreakdown = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        // ── Activity Last 30 Days (views per day) ───────────────
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $dailyViews = ToolView::select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("count(*) as views")
            )
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('views', 'date')
            ->toArray();

        // Fill missing days with 0
        $activityChart = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $activityChart[$date] = $dailyViews[$date] ?? 0;
        }

        // ── Top 5 Popular Tools ─────────────────────────────────
        $topTools = Tool::approved()
            ->orderByDesc('click_count')
            ->take(5)
            ->get(['id', 'name', 'click_count', 'avg_rating', 'logo_url']);

        // ── Recent Pending Requests ─────────────────────────────
        $recentRequests = ToolRequest::where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // ── New Users This Week ─────────────────────────────────
        $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->subWeek())->count();

        // ── Category Distribution ───────────────────────────────
        $categoryDistribution = DB::table('tools')
            ->join('categories', 'tools.category_id', '=', 'categories.id')
            ->where('tools.approval_status', 'approved')
            ->select('categories.name', DB::raw('count(*) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'roleBreakdown',
            'activityChart',
            'topTools',
            'recentRequests',
            'newUsersThisWeek',
            'categoryDistribution'
        ));
    }
}
