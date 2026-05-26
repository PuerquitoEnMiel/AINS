@extends('layouts.app')

@section('header-title', 'Badge Details')
@section('header-subtitle', 'Understand the criteria and check your qualification status')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <a href="{{ route('badges.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-ans-dark-green transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Badges Gallery</span>
        </a>
    </div>

    <!-- Main Badge Show Card -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden mb-8">
        <!-- Top Colored Header Block -->
        <div class="h-32 relative flex items-end px-8 pb-4" style="background: linear-gradient(135deg, {{ $badge->color }} 0%, {{ $badge->color }}dd 60%, {{ $badge->color }}88 100%)">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle, white 10%, transparent 11%); background-size: 12px 12px;"></div>
            
            <!-- Floating Badge Icon -->
            <div class="absolute -bottom-10 left-8 w-24 h-24 rounded-3xl bg-white border-4 border-white shadow-2xl flex items-center justify-center text-5xl overflow-hidden">
                @if($badge->image_path)
                    <img src="{{ asset('storage/' . $badge->image_path) }}" class="w-full h-full object-cover">
                @else
                    {{ $badge->icon }}
                @endif
            </div>
        </div>

        <div class="pt-16 px-8 pb-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2.5 mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider" 
                              style="background-color: {{ $badge->color }}20; color: {{ $badge->color }}">
                            {{ str_replace('_', ' ', $badge->category) }}
                        </span>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">{{ $badge->difficulty }} Level</span>
                    </div>
                    <h3 class="text-2xl font-heading font-extrabold text-gray-800">{{ $badge->name }}</h3>
                </div>

                @if($isEarned)
                    <span class="px-4 py-2 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Earned & Active</span>
                    </span>
                @else
                    <span class="px-4 py-2 bg-ans-orange/10 text-ans-orange text-xs font-bold rounded-xl flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Locked</span>
                    </span>
                @endif
            </div>

            <!-- Description -->
            <p class="text-gray-600 text-sm leading-relaxed mb-6">{{ $badge->description }}</p>

            <!-- Qualification Criteria -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 mb-6">
                <h4 class="font-heading font-bold text-gray-800 text-sm mb-3">Qualification Requirement</h4>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-700">Envío de Evidencia</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Debe subir una prueba o enlace que demuestre la obtención de la competencia.</p>
                    </div>
                </div>
            </div>

            <!-- Expiry / Validity Info -->
            <div class="flex flex-wrap gap-3 mb-6">
                @if($badge->certification_url)
                <a href="{{ $badge->certification_url }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-100 rounded-xl text-sm text-blue-700 font-medium hover:bg-blue-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    View official certification program
                </a>
                @endif
                <span class="flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-100 rounded-xl text-sm text-amber-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Duración: {{ $badge->validityLabel() }}
                </span>
            </div>

            <!-- Qualification Box / Results -->
            @auth
                @php
                    $myEvidence = $badge->requires_evidence ? Auth::user()->badgeEvidences()->where('badge_id', $badge->id)->first() : null;
                @endphp
            @endauth

            @if($badge->requires_evidence)
                @auth
                    <!-- Stepper Visual de 3 Estados -->
                    <div class="mb-6 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                        <h4 class="font-heading font-bold text-gray-800 text-xs uppercase tracking-wider mb-4">Certification Progress</h4>
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative">
                            
                            <!-- Step 1: Enviado -->
                            @php
                                $step1Completed = (bool)$myEvidence;
                            @endphp
                            <div class="flex items-center gap-4 flex-1 w-full">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 transition-all duration-300 {{ $step1Completed ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'bg-gray-100 text-gray-400 border border-gray-200' }}">
                                    @if($step1Completed)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <span class="text-sm font-bold">1</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h5 class="text-xs font-bold text-gray-800">1. Evidence Submitted</h5>
                                    <p class="text-[11px] text-gray-500 mt-0.5 truncate">
                                        @if($myEvidence)
                                            @if($myEvidence->file_name)
                                                <a href="{{ Storage::url($myEvidence->file_path) }}" target="_blank" class="text-ans-dark-green hover:underline font-medium inline-flex items-center gap-0.5">
                                                    <span>{{ Str::limit($myEvidence->file_name, 15) }}</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            @elseif($myEvidence->certificate_url)
                                                <a href="{{ $myEvidence->certificate_url }}" target="_blank" class="text-ans-dark-green hover:underline font-medium inline-flex items-center gap-0.5">
                                                    <span>View Link</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            @else
                                                Submitted
                                            @endif
                                        @else
                                            Pending submission
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Divider Line 1 (Desktop) -->
                            <div class="hidden md:block w-8 h-px bg-gray-200"></div>

                            <!-- Step 2: En Revisión -->
                            @php
                                $step2Active = $myEvidence && $myEvidence->isPending();
                                $step2Completed = $myEvidence && ($myEvidence->isApproved() || $myEvidence->isRejected() || $isEarned);
                            @endphp
                            <div class="flex items-center gap-4 flex-1 w-full">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 transition-all duration-300
                                    {{ $step2Completed ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 
                                       ($step2Active ? 'bg-amber-500 text-white animate-pulse shadow-md shadow-amber-500/20' : 'bg-gray-100 text-gray-400 border border-gray-200') }}">
                                    @if($step2Completed)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    @elseif($step2Active)
                                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18"></path></svg>
                                    @else
                                        <span class="text-sm font-bold">2</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h5 class="text-xs font-bold text-gray-800">2. Under Review</h5>
                                    <p class="text-[11px] text-gray-500 mt-0.5">
                                        @if(!$myEvidence)
                                            Awaiting submission
                                        @elseif($myEvidence->isPending())
                                            In review queue
                                        @else
                                            Review completed
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Divider Line 2 (Desktop) -->
                            <div class="hidden md:block w-8 h-px bg-gray-200"></div>

                            <!-- Step 3: Decisión Final -->
                            @php
                                $step3Completed = $isEarned || ($myEvidence && $myEvidence->isApproved());
                                $step3Rejected = $myEvidence && $myEvidence->isRejected() && !$isEarned;
                            @endphp
                            <div class="flex items-center gap-4 flex-1 w-full">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 transition-all duration-300
                                    {{ $step3Completed ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 
                                       ($step3Rejected ? 'bg-rose-500 text-white shadow-md shadow-rose-500/20' : 'bg-gray-100 text-gray-400 border border-gray-200') }}">
                                    @if($step3Completed)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    @elseif($step3Rejected)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @else
                                        <span class="text-sm font-bold">3</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h5 class="text-xs font-bold text-gray-800">3. Decision</h5>
                                    <p class="text-[11px] text-gray-500 mt-0.5">
                                        @if($step3Completed)
                                            Approved
                                        @elseif($step3Rejected)
                                            Rejected
                                        @else
                                            Pending
                                        @endif
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                @endauth
            @endif

            @if($isEarned)
                @php
                    $myEvidenceForExpiry = Auth::user()->badgeEvidences()->where('badge_id', $badge->id)->first();
                    $individualExpiry = $myEvidenceForExpiry && $myEvidenceForExpiry->expires_at ? $myEvidenceForExpiry->expires_at : null;
                @endphp
                <div class="p-6 border border-emerald-200 bg-emerald-50/50 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h4 class="font-heading font-bold text-emerald-800 text-sm mb-1">🎉 ¡Ganaste esta insignia!</h4>
                        <p class="text-xs text-emerald-700">
                            Otorgada el {{ $earnedPivot->earned_at ? \Carbon\Carbon::parse($earnedPivot->earned_at)->format('d M Y') : now()->format('d M Y') }}.
                            @if($individualExpiry)
                                · Vence el {{ $individualExpiry->format('d M Y') }}
                                @if($individualExpiry->isPast())
                                    <span class="text-rose-600 font-bold">(Expirada)</span>
                                @endif
                            @else
                                · Permanente
                            @endif
                        </p>
                    </div>
                </div>
            @else
                @auth
                    @if(!$myEvidence)
                        <div class="p-6 border border-gray-200 bg-gray-50/50 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h4 class="font-heading font-bold text-gray-800 text-sm mb-1">Evidencia de Certificación Requerida</h4>
                                <p class="text-xs text-gray-500">{{ $badge->evidence_instructions ?? 'Sube tu evidencia de certificación para que el administrador la apruebe y active tu insignia.' }}</p>
                            </div>
                            <a href="{{ route('badge-evidence.create', $badge) }}" class="px-5 py-2.5 bg-ans-dark-green text-white text-xs font-bold rounded-xl hover:bg-ans-seal-green transition-all shadow-md whitespace-nowrap">
                                📎 Subir Evidencia
                            </a>
                        </div>
                    @elseif($myEvidence->isPending())
                        <div class="p-6 border border-amber-200 bg-amber-50/30 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h4 class="font-heading font-bold text-amber-800 text-sm mb-1">⏳ Evidencia en Revisión</h4>
                                <p class="text-xs text-amber-700">Tu evidencia ha sido recibida y está en espera de validación por el administrador.</p>
                                @if($myEvidence->notes)
                                    <p class="text-[11px] text-amber-600/80 mt-1 italic">Tus notas: "{{ $myEvidence->notes }}"</p>
                                @endif
                            </div>
                            <span class="px-5 py-2.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-xl whitespace-nowrap">En Proceso</span>
                        </div>
                    @elseif($myEvidence->isRejected())
                        <div class="p-6 border border-rose-200 bg-rose-50/50 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <h4 class="font-heading font-bold text-rose-800 text-sm mb-1">❌ Evidencia Rechazada</h4>
                                <p class="text-xs text-rose-700 font-medium">El administrador ha rechazado tu evidencia. Puedes subir una nueva evidencia corregida.</p>
                                @if($myEvidence->admin_notes)
                                    <div class="mt-2 p-3 bg-white rounded-xl border border-rose-100">
                                        <p class="text-xs font-bold text-rose-800">Comentario del Administrador:</p>
                                        <p class="text-[11px] text-rose-700/90 mt-0.5">"{{ $myEvidence->admin_notes }}"</p>
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('badge-evidence.create', $badge) }}" class="px-5 py-2.5 bg-rose-600 text-white text-xs font-bold rounded-xl hover:bg-rose-700 transition-all shadow-md whitespace-nowrap">
                                📎 Reenviar Evidencia
                            </a>
                        </div>
                    @endif
                @else
                    <div class="p-6 border border-gray-200 bg-gray-50/50 rounded-2xl">
                        <p class="text-sm text-gray-500 text-center">
                            <a href="{{ route('login') }}" class="text-ans-dark-green font-semibold hover:underline">Inicia sesión</a> para obtener esta insignia.
                        </p>
                    </div>
                @endauth
            @endif
        </div>
    </div>
</div>
@endsection

