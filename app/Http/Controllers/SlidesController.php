<?php

namespace App\Http\Controllers;

use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlidesController extends Controller
{
    public function generate(LessonPlan $lessonPlan)
    {
        $token = session('google_access_token');
        if (!$token) {
            return response()->json([
                'error' => 'auth_required',
                'redirect' => route('auth.google.export-authorize'),
            ], 401);
        }

        try {
            // 1. Create the presentation
            $createResponse = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ])
                ->withoutVerifying()
                ->post('https://slides.googleapis.com/v1/presentations', [
                    'title' => $lessonPlan->title . ' - Presentation',
                ]);

            if ($createResponse->status() === 401) {
                session()->forget('google_access_token');
                return response()->json([
                    'error' => 'auth_required',
                    'redirect' => route('auth.google.export-authorize'),
                ], 401);
            }

            if (!$createResponse->successful()) {
                Log::error('Google Slides Create Error: ' . $createResponse->body());
                return response()->json(['error' => 'Failed to create Google Slides.'], 500);
            }

            $presentation = $createResponse->json();
            $presentationId = $presentation['presentationId'];

            // 2. Parse the markdown content into sections
            $sections = $this->parseMarkdownToSections($lessonPlan->content);

            // 3. Build batch update requests
            $requests = [];
            $slideIndex = 1; // Slide 0 is the default title slide created by Google Slides

            // Update the title slide (Slide 0)
            // The default template has a title text box and a subtitle text box
            // We'll update them if possible, or just insert custom slides and delete the default one later.
            // Let's create new custom styled slides for each section to keep it clean and predictable.

            foreach ($sections as $title => $body) {
                $slideId = 'slide_' . $slideIndex;
                $titleId = 'title_' . $slideIndex;
                $bodyId = 'body_' . $slideIndex;

                // Add a new slide
                $requests[] = [
                    'createSlide' => [
                        'objectId' => $slideId,
                        'insertionIndex' => $slideIndex,
                        'slideLayoutReference' => ['predefinedLayout' => 'BLANK'],
                    ],
                ];

                // Add a Title Shape
                $requests[] = [
                    'createShape' => [
                        'objectId' => $titleId,
                        'shapeType' => 'TEXT_BOX',
                        'elementProperties' => [
                            'pageObjectId' => $slideId,
                            'size' => [
                                'height' => ['magnitude' => 100, 'unit' => 'PT'],
                                'width' => ['magnitude' => 600, 'unit' => 'PT'],
                            ],
                            'transform' => [
                                'scaleX' => 1,
                                'scaleY' => 1,
                                'translateX' => 50,
                                'translateY' => 40,
                                'unit' => 'PT',
                            ],
                        ],
                    ],
                ];

                // Insert Title Text
                $requests[] = [
                    'insertText' => [
                        'objectId' => $titleId,
                        'insertionIndex' => 0,
                        'text' => $title,
                    ],
                ];

                // Style Title Text
                $requests[] = [
                    'updateTextStyle' => [
                        'objectId' => $titleId,
                        'textRange' => ['type' => 'ALL'],
                        'style' => [
                            'fontSize' => ['magnitude' => 28, 'unit' => 'PT'],
                            'bold' => true,
                            'foregroundColor' => [
                                'opaqueColor' => [
                                    'rgbColor' => ['red' => 0.0, 'green' => 0.47, 'blue' => 0.2] // ANS Dark Green
                                ],
                            ],
                        ],
                        'fields' => 'fontSize,bold,foregroundColor',
                    ],
                ];

                // Add a Body Shape
                $requests[] = [
                    'createShape' => [
                        'objectId' => $bodyId,
                        'shapeType' => 'TEXT_BOX',
                        'elementProperties' => [
                            'pageObjectId' => $slideId,
                            'size' => [
                                'height' => ['magnitude' => 280, 'unit' => 'PT'],
                                'width' => ['magnitude' => 620, 'unit' => 'PT'],
                            ],
                            'transform' => [
                                'scaleX' => 1,
                                'scaleY' => 1,
                                'translateX' => 50,
                                'translateY' => 150,
                                'unit' => 'PT',
                            ],
                        ],
                    ],
                ];

                // Insert Body Text
                $requests[] = [
                    'insertText' => [
                        'objectId' => $bodyId,
                        'insertionIndex' => 0,
                        'text' => $body,
                    ],
                ];

                // Style Body Text
                $requests[] = [
                    'updateTextStyle' => [
                        'objectId' => $bodyId,
                        'textRange' => ['type' => 'ALL'],
                        'style' => [
                            'fontSize' => ['magnitude' => 14, 'unit' => 'PT'],
                            'foregroundColor' => [
                                'opaqueColor' => [
                                    'rgbColor' => ['red' => 0.2, 'green' => 0.2, 'blue' => 0.2]
                                ],
                            ],
                        ],
                        'fields' => 'fontSize,foregroundColor',
                    ],
                ];

                $slideIndex++;
            }

            // Remove the default first slide created by Google (so only our custom slides remain)
            if (isset($presentation['slides'][0]['objectId'])) {
                $requests[] = [
                    'deleteObject' => [
                        'objectId' => $presentation['slides'][0]['objectId'],
                    ],
                ];
            }

            // 4. Execute batchUpdate
            if (!empty($requests)) {
                $updateResponse = Http::timeout(20)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                    ])
                    ->withoutVerifying()
                    ->post("https://slides.googleapis.com/v1/presentations/{$presentationId}:batchUpdate", [
                        'requests' => $requests,
                    ]);

                if (!$updateResponse->successful()) {
                    Log::error('Google Slides Update Error: ' . $updateResponse->body());
                    return response()->json(['error' => 'Failed to customize presentation slides.'], 500);
                }
            }

            return response()->json([
                'success' => true,
                'url' => "https://docs.google.com/presentation/d/{$presentationId}/edit",
            ]);

        } catch (\Exception $e) {
            Log::error('Google Slides Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Connection error: ' . $e->getMessage()], 500);
        }
    }

    private function parseMarkdownToSections($markdown)
    {
        $lines = explode("\n", $markdown);
        $sections = [];
        $currentSectionTitle = 'Lesson Introduction';
        $currentSectionBody = '';

        foreach ($lines as $line) {
            if (str_starts_with($line, '# ') || str_starts_with($line, '## ') || str_starts_with($line, '### ')) {
                // Save previous section if not empty
                if (trim($currentSectionBody) !== '') {
                    $sections[$currentSectionTitle] = trim($currentSectionBody);
                }
                $currentSectionTitle = ltrim($line, '# ');
                $currentSectionBody = '';
            } else {
                $cleanLine = str_replace(['**', '*'], '', $line);
                $currentSectionBody .= $cleanLine . "\n";
            }
        }

        // Save last section
        if (trim($currentSectionBody) !== '') {
            $sections[$currentSectionTitle] = trim($currentSectionBody);
        }

        // Limit section content size so it fits on slide (keep only first 450 chars of body)
        foreach ($sections as $title => $body) {
            if (strlen($body) > 450) {
                $sections[$title] = substr($body, 0, 450) . '...';
            }
        }

        return $sections;
    }
}
