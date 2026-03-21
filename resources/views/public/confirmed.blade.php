<!DOCTYPE html>
<html lang="en" class="">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Received — HomeOptic</title>
<script>
    (function(){
        const s = localStorage.getItem('ho_theme');
        if (s === 'dark' || (!s && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    })();
</script>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-slate-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-sans antialiased min-h-screen">

<nav class="sticky top-0 z-50 bg-white/90 dark:bg-gray-950/90 backdrop-blur border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-blue-800 dark:text-blue-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                HomeOptic
            </a>
        </div>
    </div>
</nav>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    {{-- Success icon --}}
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="h-10 w-10 text-emerald-600 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-3">Thank You!</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
            Your booking request has been received. We'll review it and email you at
            <strong class="text-gray-800 dark:text-gray-200">{{ $pendingBooking->customer_email }}</strong>
            once it's confirmed.
        </p>
    </div>

    {{-- Booking reference --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-6">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Booking Reference</p>
        <p class="font-mono text-lg font-bold text-blue-700 dark:text-blue-400">{{ strtoupper(substr($pendingBooking->token, 0, 8)) }}</p>
        <p class="text-xs text-gray-400 mt-1">Keep this for your records.</p>
    </div>

    {{-- Summary --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-8">
        <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Booking Summary</h2>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400 font-medium">Patient</dt>
                <dd class="text-gray-900 dark:text-gray-100 font-semibold">
                    {{ ($pendingBooking->patient_form_data['first_name'] ?? '') . ' ' . ($pendingBooking->patient_form_data['surname'] ?? '') }}
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400 font-medium">Date</dt>
                <dd class="text-gray-900 dark:text-gray-100 font-semibold">
                    {{ $pendingBooking->appointment_date->format('l j F Y') }}
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400 font-medium">Time</dt>
                <dd class="text-gray-900 dark:text-gray-100 font-semibold">
                    {{ substr($pendingBooking->appointment_time, 0, 5) }}
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400 font-medium">Type</dt>
                <dd class="text-gray-900 dark:text-gray-100 font-semibold">
                    {{ \App\Enums\AppointmentType::from($pendingBooking->appointment_type)->label() }}
                </dd>
            </div>
            @if($pendingBooking->reason)
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400 font-medium">Reason</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ $pendingBooking->reason }}</dd>
            </div>
            @endif
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400 font-medium">Address</dt>
                <dd class="text-gray-900 dark:text-gray-100 text-right">
                    {{ $pendingBooking->patient_form_data['address_line_1'] ?? '' }},
                    {{ $pendingBooking->patient_form_data['town_city'] ?? '' }},
                    {{ $pendingBooking->patient_form_data['post_code'] ?? '' }}
                </dd>
            </div>
        </dl>
    </div>

    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900 rounded-xl text-sm text-blue-800 dark:text-blue-300 mb-8 flex items-start gap-3">
        <svg class="h-5 w-5 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
        </svg>
        <span>
            Our team will review your request and confirm your appointment, usually within one working day.
            If we have any questions we'll contact you by email or phone.
        </span>
    </div>

    <div class="text-center">
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-xl transition-colors">
            Return to HomeOptic
        </a>
    </div>
</div>

<footer class="border-t border-gray-200 dark:border-gray-800 mt-8 py-8 text-center text-sm text-gray-400">
    &copy; {{ date('Y') }} Psk Locum Cover Ltd — HomeOptic
</footer>
</body>
</html>
