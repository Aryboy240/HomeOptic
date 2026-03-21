<!DOCTYPE html>
<html lang="en" class="">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book an Appointment — HomeOptic</title>
<script>
    (function(){
        const s = localStorage.getItem('ho_theme');
        if (s === 'dark' || (!s && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    })();
</script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = { darkMode: 'class' }
</script>
</head>
<body class="bg-slate-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-sans antialiased min-h-screen">

{{-- Nav (same pattern as home) --}}
<nav class="sticky top-0 z-50 bg-white/90 dark:bg-gray-950/90 backdrop-blur border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-blue-800 dark:text-blue-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                HomeOptic
            </a>
            <div class="flex items-center gap-3">
                <button id="theme-toggle"
                        class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 text-gray-500 dark:text-gray-300 transition-colors">
                    <svg class="hidden dark:block h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <svg class="block dark:hidden h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>
                <a href="tel:+441902000000" class="text-sm text-gray-600 dark:text-gray-400 hover:text-blue-700 dark:hover:text-blue-400">01902 000 000</a>
            </div>
        </div>
    </div>
</nav>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="bookingForm()"
     x-init="fetchSlots()">

    {{-- Step indicator --}}
    <div class="flex items-center gap-2 mb-10">
        @foreach([1 => 'Select Time', 2 => 'Your Details', 3 => 'Review'] as $n => $label)
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2"
                     :class="{{ $n }} <= step ? 'opacity-100' : 'opacity-40'">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-colors"
                         :class="{{ $n }} < step ? 'bg-blue-700 border-blue-700 text-white' : ({{ $n }} === step ? 'border-blue-700 text-blue-700 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-500' : 'border-gray-300 text-gray-400 dark:border-gray-600')">
                        <template x-if="{{ $n }} < step">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                        </template>
                        <template x-if="{{ $n }} >= step">
                            <span>{{ $n }}</span>
                        </template>
                    </div>
                    <span class="text-sm font-medium hidden sm:block"
                          :class="{{ $n }} === step ? 'text-blue-700 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">
                        {{ $label }}
                    </span>
                </div>
            </div>
            @if($n < 3)
                <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700 mx-1"></div>
            @endif
        @endforeach
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-300">
            <p class="font-semibold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('book.submit') }}" id="booking-form">
        @csrf

        {{-- Hidden fields to carry Alpine state into the POST --}}
        <input type="hidden" name="appointment_date" :value="selectedDate">
        <input type="hidden" name="appointment_time" :value="selectedTime">

        {{-- ─── STEP 1: Timeslot ─────────────────────────────────────────── --}}
        <div x-show="step === 1" x-cloak>
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-6">
                <h2 class="text-xl font-bold mb-5 text-gray-900 dark:text-white">Select a Date & Time</h2>

                {{-- Date picker --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Date</label>
                    <input type="date"
                           x-model="selectedDate"
                           @change="fetchSlots()"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full sm:w-auto px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Time grid --}}
                <div class="mb-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        Available Times
                        <span x-show="loadingSlots" class="text-xs text-gray-400 font-normal ml-2">Loading…</span>
                    </label>
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                        <template x-for="slot in slots" :key="slot">
                            <button type="button"
                                    @click="!isTaken(slot) && (selectedTime = slot)"
                                    :disabled="isTaken(slot)"
                                    :class="{
                                        'bg-blue-700 text-white border-blue-700 font-semibold': selectedTime === slot,
                                        'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 line-through cursor-not-allowed': isTaken(slot),
                                        'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:border-blue-400': !isTaken(slot) && selectedTime !== slot
                                    }"
                                    class="py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 text-center transition-colors"
                                    x-text="slot">
                            </button>
                        </template>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Greyed-out slots are already taken.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-6">
                <h2 class="text-xl font-bold mb-5 text-gray-900 dark:text-white">Appointment Details</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Appointment Type <span class="text-red-500">*</span>
                        </label>
                        <select name="appointment_type"
                                x-model="selectedType"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">— Select —</option>
                            @foreach($types as $val => $label)
                                <option value="{{ $val }}" {{ old('appointment_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Reason for Appointment <span class="text-red-500">*</span></label>
                        <textarea name="reason" rows="3"
                                  placeholder="e.g. routine check-up, difficulty seeing in low light, prescription review…"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('reason') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Notes for Examiner <span class="text-gray-400 font-normal">(optional)</span></label>
                        <textarea name="examiner_notes" rows="2"
                                  placeholder="Any specific concerns or information for the optometrist…"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('examiner_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button"
                        @click="step1Next()"
                        :disabled="!selectedTime || !selectedType"
                        :class="(!selectedTime || !selectedType) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-800'"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-700 text-white font-semibold rounded-xl transition-colors">
                    Next: Your Details
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- ─── STEP 2: Patient Details ──────────────────────────────────── --}}
        <div x-show="step === 2" x-cloak>

            {{-- Section 1: Address --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-5">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-7 h-7 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 rounded-lg flex items-center justify-center text-xs font-bold">1</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Address</h3>
                </div>

                {{-- Postcode warning --}}
                <div x-show="!postcodeOk && postcodeError"
                     class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-300 flex items-start gap-2">
                    <svg class="h-4 w-4 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
                    </svg>
                    <span x-text="postcodeError"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Address Line 1 <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="address_line_1" value="{{ old('address_line_1') }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Town / City <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="town_city" value="{{ old('town_city') }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">County</label>
                        <input type="text" name="county" value="{{ old('county') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Postcode <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="post_code" id="post_code" value="{{ old('post_code') }}" required
                                   x-model="postcode"
                                   @blur="checkPostcode()"
                                   class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                                   :class="!postcodeOk ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-gray-700'">
                            <span x-show="postcodeChecking" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Checking…</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">We serve within 20 miles of Wolverhampton.</p>
                    </div>
                </div>
            </div>

            {{-- Section 2: Personal Details --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-5">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-7 h-7 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 rounded-lg flex items-center justify-center text-xs font-bold">2</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Personal Details</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <select name="title" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">—</option>
                            @foreach($titles as $val => $label)
                                <option value="{{ $val }}" {{ old('title') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" x-model="firstName" value="{{ old('first_name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Surname <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="surname" x-model="lastName" value="{{ old('surname') }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Mobile <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="telephone_mobile" value="{{ old('telephone_mobile') }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Other Phone</label>
                        <input type="tel" name="telephone_other" value="{{ old('telephone_other') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" x-model="patientEmail" value="{{ old('email') }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            {{-- Section 3: Clinical & Admin --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-5">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-7 h-7 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 rounded-lg flex items-center justify-center text-xs font-bold">3</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Clinical &amp; Admin</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Sex / Gender <span class="text-red-500">*</span>
                        </label>
                        <select name="sex_gender" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">— Select —</option>
                            @foreach($genders as $val => $label)
                                <option value="{{ $val }}" {{ old('sex_gender') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Patient Type <span class="text-red-500">*</span>
                        </label>
                        <select name="patient_type" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">— Select —</option>
                            @foreach($patientTypes as $val => $label)
                                <option value="{{ $val }}" {{ old('patient_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <input type="checkbox" name="is_nhs" id="is_nhs" value="1" {{ old('is_nhs') ? 'checked' : '' }}
                               class="w-4 h-4 accent-blue-600">
                        <label for="is_nhs" class="text-sm text-gray-700 dark:text-gray-300 font-medium">NHS Patient</label>
                    </div>
                </div>
            </div>

            {{-- Section 4: Medical Information --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-5">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-7 h-7 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 rounded-lg flex items-center justify-center text-xs font-bold">4</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Medical Information</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                    @foreach([
                        'has_glaucoma' => 'Diagnosed with glaucoma',
                        'is_diabetic' => 'Diabetic',
                        'is_blind_partially_sighted' => 'Registered blind or partially sighted',
                        'has_hearing_impairment' => 'Hearing impairment',
                        'has_retinitis_pigmentosa' => 'Retinitis pigmentosa',
                    ] as $field => $label)
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="{{ $field }}" value="1" {{ old($field) ? 'checked' : '' }} class="w-4 h-4 accent-blue-600">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Physical Disabilities</label>
                        <textarea name="physical_disabilities" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y">{{ old('physical_disabilities') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Mental Health Conditions</label>
                        <textarea name="mental_health_conditions" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y">{{ old('mental_health_conditions') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Section 5: Social & Benefits --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-7 h-7 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 rounded-lg flex items-center justify-center text-xs font-bold">5</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Social &amp; Benefits</h3>
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 mb-4">
                    <input type="checkbox" name="in_full_time_education" value="1" {{ old('in_full_time_education') ? 'checked' : '' }} class="w-4 h-4 accent-blue-600">
                    In full-time education
                </label>

                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Benefits received (tick all that apply)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-5 text-sm text-gray-700 dark:text-gray-300">
                    @foreach([
                        'income_support' => 'Income Support',
                        'universal_credit' => 'Universal Credit',
                        'pension_credit' => 'Pension Credit Guarantee Credit',
                        'jobseekers_allowance' => 'Income-based Jobseeker\'s Allowance',
                        'esa' => 'Income-related Employment and Support Allowance',
                        'nhs_tax_credit_exemption' => 'NHS Tax Credit Exemption Certificate',
                        'hc2_certificate' => 'HC2 Certificate',
                    ] as $val => $label)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="benefits[]" value="{{ $val }}" class="w-4 h-4 accent-blue-600"
                                   {{ in_array($val, old('benefits', [])) ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Next of Kin Name</label>
                        <input type="text" name="next_of_kin_name" value="{{ old('next_of_kin_name') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Relationship</label>
                        <input type="text" name="next_of_kin_relationship" value="{{ old('next_of_kin_relationship') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Next of Kin Phone</label>
                        <input type="tel" name="next_of_kin_phone" value="{{ old('next_of_kin_phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Emergency Contact Phone</label>
                        <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Carer Name</label>
                        <input type="text" name="carer_name" value="{{ old('carer_name') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Carer Phone</label>
                        <input type="tel" name="carer_phone" value="{{ old('carer_phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-between">
                <button type="button" @click="step = 1"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Back
                </button>
                <button type="button"
                        @click="step2Next()"
                        :disabled="!postcodeOk"
                        :class="!postcodeOk ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-800'"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-700 text-white font-semibold rounded-xl transition-colors">
                    Next: Review
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- ─── STEP 3: Review & Submit ──────────────────────────────────── --}}
        <div x-show="step === 3" x-cloak>
            {{-- Confirmation email — most prominent field --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-6 mb-6">
                <label class="block text-base font-bold text-blue-800 dark:text-blue-300 mb-1.5">
                    Email address for appointment confirmation <span class="text-red-500">*</span>
                </label>
                <p class="text-sm text-blue-700 dark:text-blue-400 mb-3">We'll email you when your appointment is confirmed or if we need to reach you.</p>
                <input type="email" name="customer_email" x-model="customerEmail" required
                       class="w-full px-3 py-2.5 border border-blue-300 dark:border-blue-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Summary --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Appointment Summary</h3>
                    <button type="button" @click="step = 1" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Edit</button>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <dt class="text-gray-500 dark:text-gray-400 font-medium">Date</dt>
                    <dd class="text-gray-900 dark:text-gray-100 font-semibold" x-text="formatDate(selectedDate)"></dd>
                    <dt class="text-gray-500 dark:text-gray-400 font-medium">Time</dt>
                    <dd class="text-gray-900 dark:text-gray-100 font-semibold" x-text="selectedTime"></dd>
                    <dt class="text-gray-500 dark:text-gray-400 font-medium">Type</dt>
                    <dd class="text-gray-900 dark:text-gray-100 font-semibold" x-text="selectedTypeLabel()"></dd>
                </dl>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Patient Details</h3>
                    <button type="button" @click="step = 2" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Edit</button>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <dt class="text-gray-500 dark:text-gray-400 font-medium">Name</dt>
                    <dd class="text-gray-900 dark:text-gray-100 font-semibold" x-text="(firstName + ' ' + lastName).trim() || '—'"></dd>
                    <dt class="text-gray-500 dark:text-gray-400 font-medium">Address</dt>
                    <dd class="text-gray-900 dark:text-gray-100 font-semibold" x-text="addressSummary()"></dd>
                </dl>
            </div>

            <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-sm text-amber-800 dark:text-amber-300 mb-6">
                By submitting this form you consent to HomeOptic processing your personal information to arrange your appointment. Your details will not be retained if your request is declined.
            </div>

            <div class="flex justify-between">
                <button type="button" @click="step = 2"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Back
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-7 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-colors shadow-md">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Book Appointment
                </button>
            </div>
        </div>

    </form>
</div>

<footer class="border-t border-gray-200 dark:border-gray-800 mt-16 py-8 text-center text-sm text-gray-400">
    &copy; {{ date('Y') }} Psk Locum Cover Ltd &nbsp;&mdash;&nbsp;
    <a href="{{ route('home') }}" class="hover:text-gray-600 dark:hover:text-gray-300">HomeOptic</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    document.getElementById('theme-toggle').addEventListener('click', function() {
        const dark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('ho_theme', dark ? 'dark' : 'light');
    });

    const appointmentTypes = @json($types);

    function bookingForm() {
        return {
            step: {{ old('_step', 1) }},
            selectedDate: '{{ old('appointment_date', date('Y-m-d', strtotime('+1 day'))) }}',
            selectedTime: '{{ old('appointment_time', '') }}',
            selectedType: '{{ old('appointment_type', '') }}',
            firstName: '{{ old('first_name', '') }}',
            lastName: '{{ old('surname', '') }}',
            postcode: '{{ old('post_code', '') }}',
            patientEmail: '{{ old('email', '') }}',
            customerEmail: '{{ old('customer_email', old('email', '')) }}',
            takenSlots: [],
            loadingSlots: false,
            postcodeChecking: false,
            postcodeOk: true,
            postcodeError: '',

            get slots() {
                const s = [];
                for (let h = 8; h < 20; h++) {
                    s.push(String(h).padStart(2, '0') + ':00');
                    s.push(String(h).padStart(2, '0') + ':30');
                }
                return s; // 08:00 – 19:30 (24 slots)
            },

            isTaken(slot) {
                return this.takenSlots.includes(slot);
            },

            async fetchSlots() {
                if (!this.selectedDate) return;
                this.loadingSlots = true;
                try {
                    const r = await fetch('/api/available-slots?date=' + this.selectedDate);
                    this.takenSlots = await r.json();
                    if (this.selectedTime && this.isTaken(this.selectedTime)) this.selectedTime = '';
                } catch (e) {}
                this.loadingSlots = false;
            },

            async checkPostcode() {
                if (!this.postcode) return;
                this.postcodeChecking = true;
                this.postcodeOk = true;
                this.postcodeError = '';
                try {
                    const r = await fetch('/api/check-postcode?postcode=' + encodeURIComponent(this.postcode));
                    const d = await r.json();
                    this.postcodeOk = d.within_range;
                    if (!d.within_range) {
                        this.postcodeError = 'We are unable to visit addresses more than 20 miles from Wolverhampton. Please call us to discuss.';
                    }
                } catch (e) {}
                this.postcodeChecking = false;
            },

            step1Next() {
                const today = new Date().toISOString().split('T')[0];
                if (!this.selectedTime || !this.selectedType || this.selectedDate <= today) return;

                const reasonEl = document.querySelector('[name="reason"]');
                if (reasonEl) {
                    reasonEl.classList.remove('border-red-400', 'dark:border-red-600');
                    const existing = reasonEl.parentElement.querySelector('.field-error');
                    if (existing) existing.remove();
                    if (!reasonEl.value.trim()) {
                        reasonEl.classList.add('border-red-400', 'dark:border-red-600');
                        const err = document.createElement('p');
                        err.className = 'field-error text-xs text-red-500 mt-1';
                        err.textContent = 'Reason for Appointment is required.';
                        reasonEl.parentElement.appendChild(err);
                        reasonEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }
                }

                this.step = 2;
                window.scrollTo(0, 0);
            },

            step2Next() {
                if (!this.postcodeOk) return;

                // Client-side validation of required fields
                const required = [
                    { name: 'title',            label: 'Title' },
                    { name: 'first_name',       label: 'First Name' },
                    { name: 'surname',          label: 'Surname' },
                    { name: 'date_of_birth',    label: 'Date of Birth' },
                    { name: 'sex_gender',       label: 'Sex / Gender' },
                    { name: 'patient_type',     label: 'Patient Type' },
                    { name: 'telephone_mobile', label: 'Mobile' },
                    { name: 'email',            label: 'Email Address' },
                    { name: 'address_line_1',   label: 'Address Line 1' },
                    { name: 'town_city',        label: 'Town / City' },
                    { name: 'post_code',        label: 'Postcode' },
                ];

                let firstError = null;
                required.forEach(f => {
                    const el = document.querySelector('[name="' + f.name + '"]');
                    if (!el) return;
                    // Remove any previous error
                    el.classList.remove('border-red-400', 'dark:border-red-600');
                    const existing = el.parentElement.querySelector('.field-error');
                    if (existing) existing.remove();

                    if (!el.value.trim()) {
                        el.classList.add('border-red-400', 'dark:border-red-600');
                        const err = document.createElement('p');
                        err.className = 'field-error text-xs text-red-500 mt-1';
                        err.textContent = 'This field is required.';
                        el.parentElement.appendChild(err);
                        if (!firstError) firstError = el;
                    }
                });

                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                // Carry email forward to the confirmation field
                this.customerEmail = this.patientEmail;

                this.step = 3;
                window.scrollTo(0, 0);
            },

            formatDate(d) {
                if (!d) return '—';
                const dt = new Date(d + 'T12:00:00');
                return dt.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            },

            selectedTypeLabel() {
                return appointmentTypes[this.selectedType] || this.selectedType || '—';
            },

            addressSummary() {
                const pc = this.postcode;
                const city = document.querySelector('[name=town_city]')?.value || '';
                return [city, pc].filter(Boolean).join(', ') || '—';
            },
        };
    }
</script>
</body>
</html>
