<?php

namespace App\Http\Controllers;

use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClassroomController extends Controller
{
    public function courses()
    {
        $token = session('google_access_token');
        if (!$token) {
            return response()->json([
                'error' => 'auth_required',
                'redirect' => route('auth.google.export-authorize'),
            ], 401);
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->withoutVerifying()
                ->get('https://classroom.googleapis.com/v1/courses', [
                    'courseStates' => 'ACTIVE',
                ]);

            if ($response->status() === 401) {
                session()->forget('google_access_token');
                return response()->json([
                    'error' => 'auth_required',
                    'redirect' => route('auth.google.export-authorize'),
                ], 401);
            }

            if (!$response->successful()) {
                Log::error('Google Classroom Fetch Courses Error: ' . $response->body());
                return response()->json(['error' => 'Failed to fetch courses from Google Classroom.'], 500);
            }

            $data = $response->json();
            $courses = $data['courses'] ?? [];

            return response()->json([
                'success' => true,
                'courses' => array_map(function ($c) {
                    return [
                        'id' => $c['id'],
                        'name' => $c['name'],
                        'section' => $c['section'] ?? '',
                    ];
                }, $courses),
            ]);

        } catch (\Exception $e) {
            Log::error('Google Classroom Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Connection error with Google Classroom: ' . $e->getMessage()], 500);
        }
    }

    public function share(Request $request, LessonPlan $lessonPlan)
    {
        $request->validate([
            'course_id' => 'required|string',
            'share_type' => 'required|string|in:assignment,announcement',
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $token = session('google_access_token');
        if (!$token) {
            return response()->json([
                'error' => 'auth_required',
                'redirect' => route('auth.google.export-authorize'),
            ], 401);
        }

        $courseId = $request->course_id;
        $shareType = $request->share_type;

        try {
            if ($shareType === 'assignment') {
                $payload = [
                    'title' => $request->title,
                    'description' => $request->instructions . "\n\n---\nLesson Plan Content:\n" . $lessonPlan->content,
                    'workType' => 'ASSIGNMENT',
                    'state' => 'PUBLISHED',
                ];

                if ($request->due_date) {
                    $dueDate = new \DateTime($request->due_date);
                    $payload['dueDate'] = [
                        'year' => (int)$dueDate->format('Y'),
                        'month' => (int)$dueDate->format('m'),
                        'day' => (int)$dueDate->format('d'),
                    ];
                    $payload['dueTime'] = [
                        'hours' => 23,
                        'minutes' => 59,
                    ];
                }

                $url = "https://classroom.googleapis.com/v1/courses/{$courseId}/courseWork";
            } else {
                $payload = [
                    'text' => "**{$request->title}**\n\n" . $request->instructions . "\n\n---\nLesson Plan Content:\n" . $lessonPlan->content,
                    'state' => 'PUBLISHED',
                ];

                $url = "https://classroom.googleapis.com/v1/courses/{$courseId}/announcements";
            }

            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ])
                ->withoutVerifying()
                ->post($url, $payload);

            if (!$response->successful()) {
                Log::error('Google Classroom Share Error: ' . $response->body());
                return response()->json(['error' => 'Failed to share to Google Classroom.'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully shared to Google Classroom!',
            ]);

        } catch (\Exception $e) {
            Log::error('Google Classroom Share Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Connection error: ' . $e->getMessage()], 500);
        }
    }
}
