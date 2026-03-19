<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Appointment Diary</h2>
            <div class="flex items-center gap-4">
                {{-- Diary switcher --}}
                <form method="GET" action="{{ route('diary.index') }}" id="diary-switcher">
                    <input type="hidden" name="date" value="{{ $anchorDate->toDateString() }}">
                    <input type="hidden" name="view" value="{{ $viewMode }}">
                    <input type="hidden" name="show_cancelled" value="{{ $showCancelled ? '1' : '0' }}">
                    <select name="diary_id" onchange="document.getElementById('diary-switcher').submit()"
                        class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($diaries as $d)
                            <option value="{{ $d->id }}" @selected($diary && $diary->id === $d->id)>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </form>

                {{-- View toggle --}}
                <div class="flex rounded-md shadow-sm">
                    <a href="{{ route('diary.index', array_merge(request()->query(), ['view' => 'week', 'date' => $anchorDate->toDateString()])) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-l-md border {{ $viewMode === 'week' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                        Week
                    </a>
                    <a href="{{ route('diary.index', array_merge(request()->query(), ['view' => 'day', 'date' => $anchorDate->toDateString()])) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-r-md border-t border-b border-r {{ $viewMode === 'day' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                        Day
                    </a>
                </div>

                {{-- Show cancelled toggle --}}
                <a href="{{ route('diary.index', array_merge(request()->query(), ['show_cancelled' => $showCancelled ? '0' : '1'])) }}"
                   class="text-sm {{ $showCancelled ? 'text-red-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $showCancelled ? 'Hide Cancelled' : 'Show Cancelled' }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Date navigation --}}
            <div class="flex items-center justify-between mb-4">
                @if($viewMode === 'day')
                    <a href="{{ route('diary.index', array_merge(request()->query(), ['date' => $anchorDate->copy()->subDay()->toDateString()])) }}"
                       class="inline-flex items-center px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                        &larr; Previous Day
                    </a>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $anchorDate->format('l, j F Y') }}</h3>
                    <a href="{{ route('diary.index', array_merge(request()->query(), ['date' => $anchorDate->copy()->addDay()->toDateString()])) }}"
                       class="inline-flex items-center px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                        Next Day &rarr;
                    </a>
                @else
                    <a href="{{ route('diary.index', array_merge(request()->query(), ['date' => $anchorDate->copy()->subWeek()->toDateString()])) }}"
                       class="inline-flex items-center px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                        &larr; Previous Week
                    </a>
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $from->format('j F') }} – {{ $to->format('j F Y') }}
                    </h3>
                    <a href="{{ route('diary.index', array_merge(request()->query(), ['date' => $anchorDate->copy()->addWeek()->toDateString()])) }}"
                       class="inline-flex items-center px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                        Next Week &rarr;
                    </a>
                @endif
            </div>

            @if(!$diary)
                <p class="text-gray-500 text-center py-12">No diary found. Please create a diary first.</p>
            @else
                {{-- Add appointment button --}}
                <div class="flex justify-end mb-3">
                    <a href="{{ route('appointments.store') }}"
                       onclick="event.preventDefault(); document.getElementById('new-appt-form').classList.toggle('hidden')"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-indigo-700">
                        + New Appointment
                    </a>
                </div>

                {{-- Quick new appointment form --}}
                <div id="new-appt-form" class="{{ $errors->any() ? '' : 'hidden' }} mb-6 p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h4 class="font-medium text-gray-800 mb-3">Book New Appointment</h4>

                    @if($errors->any())
                        <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-md">
                            <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('appointments.store') }}">
                        @csrf
                        <input type="hidden" name="diary_id" value="{{ $diary->id }}">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div x-data="{
                                query: '{{ old('patient_id') ? 'Patient #' . old('patient_id') : '' }}',
                                patientId: {{ old('patient_id') ?? 'null' }},
                                results: [],
                                open: false,
                                loading: false,
                                async fetchResults() {
                                    this.patientId = null;
                                    if (this.query.trim().length < 1) { this.results = []; this.open = false; return; }
                                    this.loading = true;
                                    const res = await fetch('{{ route('patients.search') }}?q=' + encodeURIComponent(this.query));
                                    this.results = await res.json();
                                    this.loading = false;
                                    this.open = this.results.length > 0;
                                },
                                select(patient) {
                                    this.query = patient.first_name + ' ' + patient.surname;
                                    this.patientId = patient.id;
                                    this.open = false;
                                    this.results = [];
                                },
                                clear() {
                                    if (!this.patientId) return;
                                    this.patientId = null;
                                }
                            }" class="relative col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Patient</label>
                                <input type="text"
                                    x-model="query"
                                    @input.debounce.250ms="fetchResults()"
                                    @keydown.escape="open = false"
                                    @focus="open = results.length > 0"
                                    @click="clear()"
                                    placeholder="Search by name or ID…"
                                    autocomplete="off"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('patient_id') border-red-500 @enderror"
                                    :class="{ 'border-indigo-400 ring-1 ring-indigo-300': patientId }">
                                <input type="hidden" name="patient_id" :value="patientId">

                                {{-- Dropdown --}}
                                <div x-show="open" x-cloak @click.outside="open = false"
                                    class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto">
                                    <template x-for="patient in results" :key="patient.id">
                                        <button type="button" @click="select(patient)"
                                            class="w-full text-left px-3 py-2 text-sm hover:bg-indigo-50 flex items-baseline justify-between gap-2">
                                            <span>
                                                <span x-text="patient.first_name + ' ' + patient.surname" class="font-medium text-gray-800"></span>
                                                <span x-text="'DOB ' + patient.date_of_birth" class="ml-2 text-xs text-gray-500"></span>
                                            </span>
                                            <span x-text="'#' + patient.id" class="text-xs text-gray-400 shrink-0"></span>
                                        </button>
                                    </template>
                                </div>

                                {{-- Selected / loading state --}}
                                <p x-show="patientId" class="mt-0.5 text-xs text-indigo-600 font-medium" x-text="'Patient #' + patientId + ' selected'"></p>
                                <p x-show="loading && !patientId" class="mt-0.5 text-xs text-gray-400">Searching…</p>
                                @error('patient_id')
                                    <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" name="date" required
                                    value="{{ old('date', $anchorDate->toDateString()) }}"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('date') border-red-500 @enderror">
                                @error('date')
                                    <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                                <input type="time" name="start_time" required step="300"
                                    value="{{ old('start_time') }}"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('start_time') border-red-500 @enderror">
                                @error('start_time')
                                    <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Length (min)</label>
                                <input type="number" name="length_minutes" required min="1"
                                    value="{{ old('length_minutes', 30) }}"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                                <select name="appointment_type" required
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($appointmentTypes as $value => $label)
                                        <option value="{{ $value }}" @selected(old('appointment_type') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                <select name="appointment_status" required
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($appointmentStatuses as $value => $label)
                                        <option value="{{ $value }}" @selected(old('appointment_status') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Display Text (optional)</label>
                                <input type="text" name="display_text"
                                    value="{{ old('display_text') }}"
                                    placeholder="e.g. domiciliary – extra time needed"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Book</button>
                            <button type="button" onclick="document.getElementById('new-appt-form').classList.add('hidden')"
                                class="px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>

                {{-- Time-grid calendar --}}
                @php
                    $pxPerMin    = 2;      // 2px per minute = 60px per hour
                    $gridStart   = 8 * 60;   // 480 — 08:00
                    $gridEnd     = 20 * 60;  // 1200 — 20:00
                    $totalHeight = ($gridEnd - $gridStart) * $pxPerMin; // 1080px

                    // Inline styles used so colours are guaranteed regardless of Tailwind JIT compilation
                    $statusStyles = [
                        'booked'         => 'background:#dcfce7; border-left:3px solid #16a34a; color:#14532d;',
                        'confirmed'      => 'background:#dbeafe; border-left:3px solid #2563eb; color:#1e3a8a;',
                        'completed'      => 'background:#f3e8ff; border-left:3px solid #9333ea; color:#581c87;',
                        'did_not_attend' => 'background:#fee2e2; border-left:3px solid #dc2626; color:#7f1d1d;',
                        'cancelled'      => 'background:#f3f4f6; border-left:3px solid #9ca3af; color:#9ca3af;',
                    ];

                    if ($viewMode === 'week') {
                        $days   = collect();
                        $d      = $from->copy();
                        while ($d->lte($to)) { $days->push($d->copy()); $d->addDay(); }
                        $byDate = $appointments->groupBy(fn ($a) => $a->date->toDateString());
                    }
                @endphp

                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">

                    {{-- Sticky day-header row (outside the scroll container) --}}
                    <div class="flex border-b border-gray-200 bg-gray-50">
                        <div class="flex-shrink-0 border-r border-gray-200" style="width: 3.5rem"></div>
                        @if($viewMode === 'week')
                            @foreach($days as $day)
                                <div class="flex-1 text-center py-2 border-r border-gray-100 last:border-r-0 {{ $day->isToday() ? 'bg-indigo-50' : '' }}">
                                    <p class="text-xs font-medium text-gray-500 uppercase">{{ $day->format('D') }}</p>
                                    <p class="text-sm font-semibold {{ $day->isToday() ? 'text-indigo-700' : 'text-gray-800' }}">{{ $day->format('j M') }}</p>
                                </div>
                            @endforeach
                        @else
                            <div class="flex-1 text-center py-2 bg-indigo-50">
                                <p class="text-xs font-medium text-gray-500 uppercase">{{ $anchorDate->format('D') }}</p>
                                <p class="text-sm font-semibold text-indigo-700">{{ $anchorDate->format('j F Y') }}</p>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Scrollable time-grid body --}}
                    <div class="overflow-y-hidden" style="max-height: 100%;">
                        <div class="flex" style="height: {{ $totalHeight }}px">

                            {{-- Time axis --}}
                            <div class="flex-shrink-0 border-r border-gray-200 relative bg-gray-50" style="display: flex; justify-content: center; width: 3.5rem; height: {{ $totalHeight }}px">
                                @for($min = $gridStart; $min < $gridEnd; $min += 30)
                                    @php $labelTop = ($min - $gridStart) * $pxPerMin; @endphp
                                    <div class="absolute right-1 text-right text-xs text-gray-400 leading-none select-none"
                                         style="top: {{ $labelTop + 25 }}px">
                                        {{ sprintf('%02d:%02d', intdiv($min, 60), $min % 60) }}
                                    </div>
                                @endfor
                            </div>

                            {{-- Day columns --}}
                            @if($viewMode === 'week')
                                @foreach($days as $day)
                                    @php $dayAppts = $byDate->get($day->toDateString(), collect()); @endphp
                                    <div class="flex-1 relative border-r border-gray-100 last:border-r-0 {{ $day->isToday() ? 'bg-indigo-50/20' : '' }}"
                                         style="height: {{ $totalHeight }}px">
                                        {{-- Hour grid lines --}}
                                        @for($min = $gridStart; $min <= $gridEnd; $min += 60)
                                            <div class="absolute w-full border-t {{ $min === $gridStart ? 'border-gray-200' : 'border-gray-200' }}"
                                                 style="top: {{ ($min - $gridStart) * $pxPerMin }}px"></div>
                                        @endfor
                                        {{-- Half-hour lines --}}
                                        @for($min = $gridStart + 30; $min < $gridEnd; $min += 60)
                                            <div class="absolute w-full border-t border-gray-100"
                                                 style="top: {{ ($min - $gridStart) * $pxPerMin }}px"></div>
                                        @endfor

                                        {{-- Appointment blocks --}}
                                        @foreach($dayAppts as $appt)
                                            @php
                                                $parts     = explode(':', (string) $appt->start_time);
                                                $startMins = (int)$parts[0] * 60 + (int)$parts[1];
                                                $apptTop   = max(0, ($startMins - $gridStart) * $pxPerMin);
                                                $apptH     = max(20, $appt->length_minutes * $pxPerMin);
                                                $sKey      = $appt->cancelled_at ? 'cancelled' : $appt->appointment_status->value;
                                                $blockStyle = ($statusStyles[$sKey] ?? 'background:#e0e7ff; border-left:3px solid #6366f1; color:#312e81;')
                                                    . " position:absolute; top:{$apptTop}px; height:{$apptH}px; left:3px; right:3px;";
                                                $endTime   = \Carbon\Carbon::parse($appt->start_time)->addMinutes($appt->length_minutes)->format('H:i');
                                            @endphp
                                            <a href="{{ route('appointments.edit', $appt) }}"
                                               title="{{ $appt->patient?->first_name }} {{ $appt->patient?->surname }} — {{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }}"
                                               class="block rounded px-1 overflow-hidden hover:opacity-80"
                                               style="{{ $blockStyle }}">
                                                <p class="text-xs font-bold leading-tight truncate" style="margin-top:2px">
                                                    {{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }}–{{ $endTime }}
                                                </p>
                                                <p class="text-xs font-semibold leading-tight truncate">
                                                    {{ $appt->patient?->first_name }} {{ $appt->patient?->surname }}
                                                </p>
                                                @if($apptH >= 44)
                                                    <p class="text-xs leading-tight truncate" style="opacity:0.75">{{ $appt->appointment_type->label() }}</p>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @endforeach
                            @else
                                {{-- Day view: single wide column --}}
                                <div class="flex-1 relative" style="height: {{ $totalHeight }}px">
                                    {{-- Hour grid lines --}}
                                    @for($min = $gridStart; $min <= $gridEnd; $min += 60)
                                        <div class="absolute w-full border-t border-gray-200"
                                             style="top: {{ ($min - $gridStart) * $pxPerMin }}px"></div>
                                    @endfor
                                    {{-- Half-hour lines --}}
                                    @for($min = $gridStart + 30; $min < $gridEnd; $min += 60)
                                        <div class="absolute w-full border-t border-gray-100"
                                             style="top: {{ ($min - $gridStart) * $pxPerMin }}px"></div>
                                    @endfor

                                    {{-- Appointment blocks --}}
                                    @forelse($appointments as $appt)
                                        @php
                                            $parts     = explode(':', (string) $appt->start_time);
                                            $startMins = (int)$parts[0] * 60 + (int)$parts[1];
                                            $apptTop   = max(0, ($startMins - $gridStart) * $pxPerMin);
                                            $apptH     = max(24, $appt->length_minutes * $pxPerMin);
                                            $sKey      = $appt->cancelled_at ? 'cancelled' : $appt->appointment_status->value;
                                            $blockStyle = ($statusStyles[$sKey] ?? 'background:#e0e7ff; border-left:4px solid #6366f1; color:#312e81;')
                                                . " position:absolute; top:{$apptTop}px; height:{$apptH}px; left:4px; right:4px;";
                                            $endTime   = \Carbon\Carbon::parse($appt->start_time)->addMinutes($appt->length_minutes)->format('H:i');
                                        @endphp
                                        <a href="{{ route('appointments.edit', $appt) }}"
                                           class="block rounded px-2 overflow-hidden hover:opacity-80"
                                           style="{{ $blockStyle }}">
                                            <p class="text-xs font-bold leading-tight" style="margin-top:2px">
                                                {{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }} – {{ $endTime }}
                                            </p>
                                            <p class="text-sm font-semibold leading-tight truncate">
                                                {{ $appt->patient?->first_name }} {{ $appt->patient?->surname }}
                                            </p>
                                            @if($apptH >= 52)
                                                <p class="text-xs leading-tight truncate">{{ $appt->appointment_type->label() }}</p>
                                            @endif
                                            @if($appt->display_text && $apptH >= 68)
                                                <p class="text-xs leading-tight truncate" style="opacity:0.75">{{ $appt->display_text }}</p>
                                            @endif
                                        </a>
                                    @empty
                                        <p class="absolute inset-0 flex items-center justify-center text-sm text-gray-400">
                                            No appointments on this day.
                                        </p>
                                    @endforelse
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- Status colour legend --}}
                <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-600">
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block rounded-sm flex-shrink-0" style="width:12px; height:12px; background:#dcfce7; border-left:3px solid #16a34a;"></span>
                        Booked
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block rounded-sm flex-shrink-0" style="width:12px; height:12px; background:#dbeafe; border-left:3px solid #2563eb;"></span>
                        Confirmed
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block rounded-sm flex-shrink-0" style="width:12px; height:12px; background:#f3e8ff; border-left:3px solid #9333ea;"></span>
                        Completed
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block rounded-sm flex-shrink-0" style="width:12px; height:12px; background:#fee2e2; border-left:3px solid #dc2626;"></span>
                        Did Not Attend
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block rounded-sm flex-shrink-0" style="width:12px; height:12px; background:#f3f4f6; border-left:3px solid #9ca3af;"></span>
                        Cancelled
                    </span>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
