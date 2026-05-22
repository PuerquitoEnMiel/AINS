@extends('layouts.app')

@section('header-title', 'AI Task Force')
@section('header-subtitle', 'Meet the leaders responsible for guiding the pedagogical, ethical, and technical integration of AI at ANS.')

@section('content')

<style>
    /* Efecto de brillo y escala en tarjetas del directorio */
    .member-card {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(229, 231, 235, 0.6);
    }
    .member-card:hover {
        transform: translateY(-6px);
        border-color: rgba(0, 105, 55, 0.2);
        box-shadow: 0 20px 30px -10px rgba(0, 105, 55, 0.08), 0 4px 12px -5px rgba(255, 131, 0, 0.03);
    }
    
    /* Efecto de foco en inputs de formulario */
    .premium-input {
        transition: all 0.2s ease-in-out;
    }
    .premium-input:focus {
        border-color: #006937;
        box-shadow: 0 0 0 3px rgba(0, 105, 55, 0.15);
        outline: none;
    }
</style>

<div class="space-y-12 pb-12 animate-fade-in-up">

    <!-- Sección 1: Introducción y Propósito -->
    <div class="bg-gradient-to-r from-ans-dark-green/10 via-ans-light-green/5 to-transparent border border-ans-dark-green/10 rounded-3xl p-8 relative overflow-hidden shadow-sm">
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-ans-dark-green/5 rounded-full blur-xl"></div>
        <div class="max-w-3xl space-y-3 relative z-10">
            <h3 class="text-xl font-heading font-extrabold text-ans-dark-green">Our Mission</h3>
            <p class="text-sm text-gray-700 leading-relaxed">
                The American Nicaraguan School (ANS) <strong>AI Task Force</strong> is an interdisciplinary team of educators, technology specialists, and academic leaders. Our mission is to research, validate, and propose guidelines that enable the constructive use of Artificial Intelligence in classrooms, protecting the intellectual integrity and privacy of our community.
            </p>
        </div>
    </div>

    <!-- Sección 2: Directorio de Miembros -->
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-2xl font-heading font-extrabold text-gray-800 tracking-tight">
                Committee Members
            </h3>
            <p class="text-gray-500 mt-1">Project leaders you can contact to resolve doubts or propose initiatives.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($members as $member)
            <div class="member-card bg-white rounded-3xl p-6 flex flex-col justify-between items-center text-center shadow-sm relative overflow-hidden">
                <div class="space-y-4 flex flex-col items-center">
                    @if($member->image_url)
                        <img src="{{ $member->image_url }}" alt="{{ $member->name }}" class="w-24 h-24 rounded-2xl object-cover border-2 border-gray-100 shadow-sm">
                    @else
                        <div class="w-24 h-24 rounded-2xl border-2 flex items-center justify-center text-3xl font-bold shadow-inner" style="background-color: {{ $member->avatar_color }}1a; border-color: {{ $member->avatar_color }}33; color: {{ $member->avatar_color }};">
                            {{ $member->initials }}
                        </div>
                    @endif
                    <div class="space-y-1">
                        <h4 class="font-heading font-extrabold text-gray-900 text-lg">{{ $member->name }}</h4>
                        <p class="text-xs font-bold text-ans-orange uppercase tracking-wider">{{ $member->role }}</p>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        {{ $member->description }}
                    </p>
                </div>
                <div class="mt-6 w-full">
                    <a href="mailto:{{ $member->email }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 hover:border-ans-dark-green hover:bg-ans-dark-green/5 text-xs font-bold text-gray-700 hover:text-ans-dark-green transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Contact
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full py-8 text-center text-gray-500">
                No members registered at this time.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Sección 3: Formulario de Contacto y Sugerencias (Estilo Premium) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
        <!-- Izquierda: Info de contacto -->
        <div class="lg:col-span-4 bg-ans-dark-green text-white rounded-3xl p-8 flex flex-col justify-between relative overflow-hidden shadow-md">
            <!-- Decorative shape -->
            <div class="absolute -left-12 -bottom-12 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
            
            <div class="space-y-6 relative z-10">
                <h4 class="text-2xl font-heading font-extrabold tracking-tight">Have questions or proposals?</h4>
                <p class="text-sm text-white/80 leading-relaxed">
                    Write to us directly through this institutional channel. We evaluate every suggestion regarding the AI tools catalog or the institution's academic integrity guidelines.
                </p>
            </div>

            <div class="space-y-4 pt-8 relative z-10 border-t border-white/10 text-xs">
                <div class="flex items-center gap-3">
                    <span class="text-lg"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></span>
                    <span>Innovation Building, Room 102</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-lg"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></span>
                    <span>aitaskforce@ans.edu.ni</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-lg"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                    <span>Monday to Friday: 8:00 AM - 3:00 PM</span>
                </div>
            </div>
        </div>

        <!-- Derecha: Formulario -->
        <div class="lg:col-span-8 bg-white border border-gray-200/60 rounded-3xl p-8 shadow-sm relative">
            
            <!-- Toast de Éxito Interactivo (JS) -->
            <div id="contact-success-toast" class="hidden absolute inset-0 bg-white rounded-3xl z-10 flex flex-col items-center justify-center text-center p-8 space-y-4 animate-fade-in-up">
                <div>
                    <h5 class="text-xl font-heading font-extrabold text-gray-900">Message sent successfully</h5>
                    <p class="text-sm text-gray-500 mt-2 max-w-sm">Your question or proposal has been successfully received by the ANS AI Task Force. We will respond within a maximum of 48 business hours.</p>
                </div>
                <button onclick="resetContactForm()" class="px-5 py-2.5 bg-ans-dark-green hover:bg-ans-seal-green text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                    Send another message
                </button>
            </div>

            <form id="task-force-contact-form" onsubmit="submitContactForm(event)" class="space-y-6">
                <h4 class="text-xl font-heading font-extrabold text-gray-800">Send suggestion or inquiry</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Full Name</label>
                        <input type="text" id="name" required class="premium-input w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-sm text-gray-800">
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Institutional Email</label>
                        <input type="email" id="email" required class="premium-input w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-sm text-gray-800">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="subject" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Subject</label>
                    <input type="text" id="subject" required class="premium-input w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-sm text-gray-800">
                </div>

                <div class="space-y-2">
                    <label for="message" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Message or Proposal</label>
                    <textarea id="message" required rows="4" class="premium-input w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-sm text-gray-800 resize-none"></textarea>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-ans-dark-green hover:bg-ans-seal-green text-white text-sm font-bold rounded-xl transition-all shadow-md hover:shadow-lg">
                        <span>Send Message</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function submitContactForm(event) {
        event.preventDefault();
        
        // Simular envío rápido mostrando el panel de éxito
        document.getElementById('contact-success-toast').classList.remove('hidden');
    }

    function resetContactForm() {
        // Limpiar formulario y ocultar toast de éxito
        document.getElementById('task-force-contact-form').reset();
        document.getElementById('contact-success-toast').classList.add('hidden');
    }
</script>

@endsection
