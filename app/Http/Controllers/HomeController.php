<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tool;
use App\Models\LessonPlan;
use App\Models\PromptTip;
use App\Models\ToolRequest;
use App\Models\BadgeEvidence;
use App\Models\BadgeSuggestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Show the application landing page.
     */
    /**
     * Show the application landing page.
     */
    public function index(\Illuminate\Http\Request $request, \App\Services\HomeDataService $homeDataService)
    {
        $data = $homeDataService->getHomeData($request);

        // If AJAX request, return HTML segments
        if ($request->ajax() || $request->input('ajax') == 1) {
            return response()->json([
                'html' => view('partials.tool_grid', ['tools' => $data['tools']])->render(),
                'pagination' => $data['tools']->links()->render(),
                'total' => $data['tools']->total()
            ]);
        }

        return view('welcome', $data);
    }
}
