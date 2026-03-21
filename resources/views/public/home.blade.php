<!DOCTYPE html>
<html lang="en" class="">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HomeOptic — Eye Care That Comes To You</title>
<script>
    // Apply theme before paint to prevent flash
    (function(){
        const s = localStorage.getItem('ho_theme');
        if (s === 'dark' || (!s && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    })();
</script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    navy: { 900: '#0f2150', 800: '#1a3166', 700: '#1e3a8a', 600: '#1d4ed8' },
                }
            }
        }
    }
</script>
<style>
    html { scroll-behavior: smooth; }
    .hero-gradient {
        background: linear-gradient(135deg, #0f2150 0%, #1e3a8a 40%, #1d4ed8 70%, #0ea5e9 100%);
    }
    .dark .hero-gradient {
        background: linear-gradient(135deg, #020817 0%, #0f172a 40%, #1e3a8a 70%, #1d4ed8 100%);
    }
    .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card-hover:hover { transform: translateY(-3px); }
</style>
</head>
<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-sans antialiased">

{{-- ── Navigation ──────────────────────────────────────────────────────────── --}}
<nav class="sticky top-0 z-50 bg-white/90 dark:bg-gray-950/90 backdrop-blur border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-blue-800 dark:text-blue-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                HomeOptic
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="#services" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 transition-colors">Services</a>
                <a href="#how-it-works" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 transition-colors">How It Works</a>
                <a href="#about" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 transition-colors">About</a>
                <a href="#contact" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 transition-colors">Contact</a>
            </div>

            <div class="flex items-center gap-3">
                {{-- Dark mode toggle --}}
                <button id="theme-toggle"
                        class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 text-gray-500 dark:text-gray-300 transition-colors"
                        title="Toggle dark mode">
                    <svg id="icon-sun" class="hidden dark:block h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <svg id="icon-moon" class="block dark:hidden h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>
                <a href="{{ route('book') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-700 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Book Appointment
                </a>
            </div>
        </div>
    </div>
</nav>

{{-- ── Hero ─────────────────────────────────────────────────────────────────── --}}
<section class="hero-gradient text-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-36">
        <div class="max-w-2xl">
            <span class="inline-block px-3 py-1 bg-white/15 text-white/90 text-xs font-semibold tracking-wider uppercase rounded-full mb-6">
                NHS Registered · Domiciliary Optometry
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                Eye Care That<br>Comes To You
            </h1>
            <p class="text-lg md:text-xl text-blue-100 dark:text-blue-200 mb-10 leading-relaxed">
                Professional NHS-registered domiciliary optometry services across the West Midlands.
                We visit you at home — no travel, no fuss.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('book') }}"
                   class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white text-blue-800 font-bold rounded-xl hover:bg-blue-50 transition-colors shadow-lg text-base">
                    Book Appointment
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="tel:+441902000000"
                   class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl border border-white/30 transition-colors text-base">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 6.75Z" />
                    </svg>
                    Call Us
                </a>
            </div>
        </div>
    </div>
    {{-- Subtle wave divider --}}
    <div class="relative h-20 overflow-hidden">

    </div>
</section>

{{-- ── Services ─────────────────────────────────────────────────────────────── --}}
<section id="services" class="py-20 bg-white dark:bg-gray-950">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">Our Services</h2>
            <p class="text-gray-500 dark:text-gray-400 text-lg max-w-xl mx-auto">
                Comprehensive eye care delivered to your door across the West Midlands.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            {{-- Card 1 --}}
            <div class="card-hover bg-slate-50 dark:bg-gray-900 rounded-2xl p-8 border border-gray-100 dark:border-gray-800 shadow-sm">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-xl flex items-center justify-center mb-6">
                    <svg class="h-6 w-6 text-blue-700 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Home Eye Tests</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    Full NHS sight tests carried out in the comfort of your own home by a qualified optometrist. Ideal for elderly patients, those with mobility difficulties, or anyone who finds travelling difficult.
                </p>
            </div>
            {{-- Card 2 --}}
            <div class="card-hover bg-slate-50 dark:bg-gray-900 rounded-2xl p-8 border border-gray-100 dark:border-gray-800 shadow-sm">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/40 rounded-xl flex items-center justify-center mb-6">
                    <svg class="h-6 w-6 text-emerald-700 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">NHS GOS6 Eligible</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    We work within the NHS General Ophthalmic Services framework. Eligible patients can receive a fully funded domiciliary sight test under GOS6. We handle all the paperwork.
                </p>
            </div>
            {{-- Card 3 --}}
            <div class="card-hover bg-slate-50 dark:bg-gray-900 rounded-2xl p-8 border border-gray-100 dark:border-gray-800 shadow-sm">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/40 rounded-xl flex items-center justify-center mb-6">
                    <svg class="h-6 w-6 text-purple-700 dark:text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Follow-Up Care</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    We provide follow-up visits to monitor eye conditions, review prescriptions, and ensure your vision is as good as it can be. Continuity of care from the same optometrist.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ── How It Works ─────────────────────────────────────────────────────────── --}}
<section id="how-it-works" class="py-20 bg-slate-50 dark:bg-gray-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">How It Works</h2>
            <p class="text-gray-500 dark:text-gray-400 text-lg">Simple, straightforward, and stress-free.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-10">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-700 dark:bg-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl font-extrabold mx-auto mb-6 shadow-lg">1</div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">Book Online</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    Fill in our simple online booking form. Tell us your address, preferred date, and a little about your needs. We'll confirm your appointment by email.
                </p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-700 dark:bg-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl font-extrabold mx-auto mb-6 shadow-lg">2</div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">We Come To You</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    Our optometrist arrives at your home with all the equipment needed for a full NHS-standard sight test. No travel, no waiting rooms.
                </p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-700 dark:bg-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl font-extrabold mx-auto mb-6 shadow-lg">3</div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">Get Your Results</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    Receive your prescription on the day. We'll discuss findings with you and arrange any necessary follow-up or referrals.
                </p>
            </div>
        </div>
        <div class="text-center mt-12">
            <a href="{{ route('book') }}"
               class="inline-flex items-center gap-2 px-7 py-3.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl transition-colors shadow-md text-base">
                Book Your Home Eye Test
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- ── Trust ────────────────────────────────────────────────────────────────── --}}
<section id="about" class="py-20 bg-white dark:bg-gray-950">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-sm font-semibold mb-8">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
            </svg>
            GOC Registered Optometrists
        </div>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
            Trusted Domiciliary Eye Care<br class="hidden md:block"> Across the West Midlands
        </h2>
        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
            HomeOptic is operated by <strong class="text-gray-800 dark:text-gray-200">Psk Locum Cover Ltd</strong>, providing professional domiciliary optometry to patients who are unable to attend a high-street practice. All our optometrists are registered with the General Optical Council and fully qualified to deliver NHS sight tests in the home setting.
        </p>
        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
            We serve patients throughout the West Midlands region, within 20 miles of Wolverhampton — including Walsall, Dudley, Birmingham, Sandwell, and surrounding areas.
        </p>
        <div class="mt-10 flex flex-wrap items-center justify-center gap-8 text-sm text-gray-500 dark:text-gray-400">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
                GOC Registered
            </div>
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
                NHS GOS6 Provider
            </div>
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
                West Midlands Based
            </div>
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
                Domiciliary Specialists
            </div>
        </div>
    </div>
</section>

{{-- ── Footer ───────────────────────────────────────────────────────────────── --}}
<footer id="contact" class="bg-gray-900 dark:bg-gray-950 text-gray-300 border-t border-gray-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid md:grid-cols-3 gap-10 mb-10">
            <div>
                <div class="flex items-center gap-2 font-bold text-xl text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    HomeOptic
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Domiciliary optometry services across the West Midlands. Operated by Psk Locum Cover Ltd.
                </p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#services" class="hover:text-white transition-colors">Services</a></li>
                    <li><a href="#how-it-works" class="hover:text-white transition-colors">How It Works</a></li>
                    <li><a href="#about" class="hover:text-white transition-colors">About Us</a></li>
                    <li><a href="{{ route('book') }}" class="hover:text-white transition-colors">Book Appointment</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Contact</h4>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-gray-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 6.75Z" />
                        </svg>
                        <a href="tel:+441902000000" class="hover:text-white transition-colors">01902 000 000</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-gray-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        Wolverhampton & West Midlands
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-6 flex items-center justify-between text-xs text-gray-500">
            <span>&copy; {{ date('Y') }} Psk Locum Cover Ltd. All rights reserved.</span>
            <a href="{{ route('login') }}"
               title="Admin login"
               class="w-8 h-8 rounded-full flex items-center justify-center text-gray-600 hover:text-gray-400 hover:bg-white/5 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </a>
        </div>
    </div>
</footer>

<script>
    document.getElementById('theme-toggle').addEventListener('click', function() {
        const html = document.documentElement;
        const dark = html.classList.toggle('dark');
        localStorage.setItem('ho_theme', dark ? 'dark' : 'light');
    });
</script>
</body>
</html>
