<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\BadgeEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BadgeEvidenceController extends Controller
{
    /**
     * Show upload form for a specific badge.
     */
    public function create(Badge $badge)
    {
        if (! $badge->requires_evidence) {
            return redirect()->route('badges.show', $badge->slug)
                ->with('error', 'Esta insignia no requiere evidencia manual.');
        }

        $existing = BadgeEvidence::where('user_id', Auth::id())
            ->where('badge_id', $badge->id)
            ->first();

        return view('badges.evidence_upload', compact('badge', 'existing'));
    }

    /**
     * Submit evidence for admin review.
     */
    public function store(Request $request, Badge $badge)
    {
        if (! $badge->requires_evidence) {
            abort(403, 'Esta insignia no acepta evidencia manual.');
        }

        $request->validate([
            'certificate_url' => 'nullable|url|max:500',
            'notes'           => 'nullable|string|max:1000',
            'evidence_file'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ], [], [
            'evidence_file' => 'archivo de evidencia',
        ]);

        // Require at least one form of evidence
        if (! $request->certificate_url && ! $request->hasFile('evidence_file')) {
            return back()->withErrors(['evidence' => 'Debes proporcionar al menos un enlace de certificación o un archivo adjunto.'])->withInput();
        }

        $filePath = null;
        $fileName = null;
        $fileType = null;

        if ($request->hasFile('evidence_file')) {
            $file = $request->file('evidence_file');
            $filePath = $file->store('badge-evidence/' . Auth::id(), 'public');
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getClientOriginalExtension();
        }

        BadgeEvidence::updateOrCreate(
            ['user_id' => Auth::id(), 'badge_id' => $badge->id],
            [
                'file_path'       => $filePath,
                'file_name'       => $fileName,
                'file_type'       => $fileType,
                'certificate_url' => $request->certificate_url,
                'notes'           => $request->notes,
                'status'          => 'pending', // Reset to pending on resubmit
                'admin_notes'     => null,
                'reviewed_by'     => null,
                'reviewed_at'     => null,
                'expires_at'      => null,
            ]
        );

        return redirect()->route('badges.show', $badge->slug)
            ->with('success', '¡Evidencia enviada! El administrador la revisará pronto y activará tu insignia.');
    }
}
