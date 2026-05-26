<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChatConversation;
use App\Models\LessonPlan;
use App\Models\PromptTip;
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
        $lessonPlanCount = LessonPlan::count();
        $promptTipCount = PromptTip::count();

        $stats = [
            'total_users' => User::count(),
            'total_tools' => Tool::approved()->count(),
            'pending_requests' => ToolRequest::where('status', 'pending')->count(),
            'total_chats' => ChatConversation::count(),
            'total_reviews' => DB::table('reviews')->count(),
            'total_views' => ToolView::count(),
            'lesson_plans' => $lessonPlanCount,
            'prompt_tips' => $promptTipCount,
        ];

        // ── Role Breakdown ──────────────────────────────────────
        $roleBreakdown = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        // ── Activity Last 30 Days (views per day) ───────────────
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $dailyViews = ToolView::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as views')
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

        // ── Top 10 Most Favorited Tools ─────────────────────────
        $topFavorited = Tool::approved()
            ->withCount('favoritedBy')
            ->orderByDesc('favorited_by_count')
            ->take(10)
            ->get(['id', 'name', 'logo_url', 'avg_rating', 'click_count']);

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

        // ── Catalog Gaps Analysis ───────────────────────────────
        $allCategories = Category::withCount('approvedTools')->orderBy('sort_order')->get();
        $catalogGaps = $allCategories->map(fn ($cat) => [
            'name' => $cat->name,
            'icon' => $cat->icon ?? '📁',
            'count' => $cat->approved_tools_count,
            'status' => $cat->approved_tools_count === 0 ? 'empty' : ($cat->approved_tools_count < 3 ? 'low' : 'ok'),
        ]);

        // ── Monthly Adoption Trend (favorites per month, last 6 months) ──
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $monthlyAdoption = DB::table('tool_user')
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('count(*) as favorites')
            )
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('favorites', 'month')
            ->toArray();

        // Fill missing months
        $adoptionChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $adoptionChart[$month] = $monthlyAdoption[$month] ?? 0;
        }

        // ── Weekly Activity Heatmap (views by day of week) ──────
        $weeklyHeatmap = ToolView::select(
            DB::raw("EXTRACT(DOW FROM created_at) as dow"),
            DB::raw('count(*) as views')
        )
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('dow')
            ->orderBy('dow')
            ->get()
            ->pluck('views', 'dow')
            ->toArray();

        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $heatmapData = [];
        for ($d = 0; $d <= 6; $d++) {
            $heatmapData[$dayNames[$d]] = $weeklyHeatmap[$d] ?? 0;
        }

        // ── Lesson Plans This Week ──────────────────────────────
        $lessonPlansThisWeek = LessonPlan::where('created_at', '>=', Carbon::now()->subWeek())->count();

        // ── Review Engagement ───────────────────────────────────
        $avgReviewsPerTool = Tool::approved()->withCount('reviews')
            ->get()
            ->avg('reviews_count');

        return view('admin.dashboard', compact(
            'stats',
            'roleBreakdown',
            'activityChart',
            'topTools',
            'topFavorited',
            'recentRequests',
            'newUsersThisWeek',
            'categoryDistribution',
            'catalogGaps',
            'adoptionChart',
            'heatmapData',
            'lessonPlansThisWeek',
            'avgReviewsPerTool'
        ));
    }

    public function chatbotSettings()
    {
        $instructionFile = storage_path('app/chatbot_instruction.txt');
        $instructions = '';
        if (file_exists($instructionFile)) {
            $instructions = file_get_contents($instructionFile);
        } else {
            $instructions = "You are 'AINS AI Companion', a premium EdTech Coach and AI pedagogical advisor at the American Nicaraguan School (ANS).\n".
                             "Your purpose is to assist teachers and students in integrating technology, generative AI, and modern tools into teaching, research, and collaborative learning.\n\n".
                             "Guidelines:\n".
                             "1. Provide rich, highly practical pedagogical recommendations (align with SAMR or TPACK models when appropriate).\n".
                             "2. Write clear, detailed prompt engineering templates for teachers and students (e.g. prompt blueprints they can copy).\n".
                             "3. Always reply in English. Keep your tone friendly, professional, institutional, and highly motivating.\n".
                             "4. Use clear markdown formatting, bold points, bullet lists, and code blocks for prompt templates. Never mention system limits; act as a helpful ANS staff assistant.";
        }

        return view('admin.chatbot_settings', compact('instructions'));
    }

    public function updateChatbotSettings(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'instructions' => 'required|string',
        ]);

        $instructionFile = storage_path('app/chatbot_instruction.txt');
        
        if (!is_dir(dirname($instructionFile))) {
            mkdir(dirname($instructionFile), 0755, true);
        }
        
        file_put_contents($instructionFile, $request->input('instructions'));

        return redirect()->route('admin.chatbot-settings')
            ->with('success', 'Chatbot instructions updated successfully!');
    }
}
