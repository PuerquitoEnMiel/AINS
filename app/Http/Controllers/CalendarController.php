<?php

namespace App\Http\Controllers;

use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    public function schedule(Request $request, LessonPlan $lessonPlan)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string', // e.g. "09:00"
            'duration' => 'required|integer', // in minutes, e.g. 60
        ]);

        $token = session('google_access_token');
        if (!$token) {
            return response()->json([
                'error' => 'auth_required',
                'redirect' => route('auth.google.export-authorize'),
            ], 401);
        }

        try {
            $startDateTime = new \DateTime($request->date . ' ' . $request->time);
            $endDateTime = clone $startDateTime;
            $endDateTime->modify('+' . $request->duration . ' minutes');

            $payload = [
                'summary' => 'Class: ' . $lessonPlan->title,
                'description' => "Subject: {$lessonPlan->subject}\n" .
                                 "Grade Level: {$lessonPlan->grade_level}\n" .
                                 "Learning Objectives:\n{$lessonPlan->objectives}\n\n" .
                                 "---\n" .
                                 "View details in AINS App.",
                'start' => [
                    'dateTime' => $startDateTime->format(\DateTime::ATOM),
                    'timeZone' => 'America/Managua',
                ],
                'end' => [
                    'dateTime' => $endDateTime->format(\DateTime::ATOM),
                    'timeZone' => 'America/Managua',
                ],
            ];

            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ])
                ->withoutVerifying()
                ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $payload);

            if (!$response->successful()) {
                Log::error('Google Calendar Add Event Error: ' . $response->body());
                return response()->json(['error' => 'Failed to create Calendar Event.'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully scheduled in Google Calendar!',
            ]);

        } catch (\Exception $e) {
            Log::error('Google Calendar Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Connection error: ' . $e->getMessage()], 500);
        }
    }
}
