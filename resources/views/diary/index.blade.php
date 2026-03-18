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
                <div id="new-appt-form" class="hidden mb-6 p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h4 class="font-medium text-gray-800 mb-3">Book New Appointment</h4>
                    <form method="POST" action="{{ route('appointments.store') }}">
                        @csrf
                        <input type="hidden" name="diary_id" value="{{ $diary->id }}">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Patient ID</label>
                                <input type="number" name="patient_id" required placeholder="Patient ID"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" name="date" required value="{{ $anchorDate->toDateString() }}"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                                <input type="time" name="start_time" required step="300"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Length (min)</label>
                                <input type="number" name="length_minutes" required value="30" min="1"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                                <select name="appointment_type" required
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($appointmentTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                <select name="appointment_status" required
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($appointmentStatuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Display Text (optional)</label>
                                <input type="text" name="display_text" placeholder="e.g. domiciliary – extra time needed"
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

                {{-- Calendar grid --}}
                @if($viewMode === 'week')
                    @php
                        $days = collect();
                        $d = $from->copy();
                        while ($d->lte($to)) {
                            $days->push($d->copy());
                            $d->addDay();
                        }
                        $byDate = $appointments->groupBy(fn($a) => $a->date->toDateString());
                    @endphp
                    <div class="grid grid-cols-7 gap-1">
                        @foreach($days as $day)
                            <div class="bg-white rounded-lg border border-gray-200 min-h-[200px]">
                                <div class="px-2 py-1.5 border-b border-gray-100 {{ $day->isToday() ? 'bg-indigo-50' : '' }}">
                                    <p class="text-xs font-medium text-gray-500 uppercase">{{ $day->format('D') }}</p>
                                    <p class="text-sm font-semibold {{ $day->isToday() ? 'text-indigo-700' : 'text-gray-800' }}">{{ $day->format('j') }}</p>
                                </div>
                                <div class="p-1 space-y-1">
                                    @foreach($byDate->get($day->toDateString(), collect()) as $appt)
                                        <a href="{{ route('appointments.edit', $appt) }}"
                                           class="block px-1.5 py-1 rounded text-xs {{ $appt->cancelled_at ? 'bg-red-50 text-red-700 line-through' : 'bg-indigo-50 text-indigo-800 hover:bg-indigo-100' }}">
                                            <span class="font-medium">{{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }}</span>
                                            {{ $appt->patient?->first_name }} {{ $appt->patient?->surname }}
                                            @if($appt->display_text)
                                                <br><span class="opacity-75">{{ $appt->display_text }}</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Day view --}}
                    <div class="bg-white rounded-lg border border-gray-200">
                        @if($appointments->isEmpty())
                            <p class="text-gray-500 text-center py-12">No appointments on this day.</p>
                        @else
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="text-left px-4 py-2 font-medium text-gray-600 w-20">Time</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600">Patient</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600">Type</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600">Status</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600">Length</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600">Notes</th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($appointments as $appt)
                                        <tr class="{{ $appt->cancelled_at ? 'opacity-50' : 'hover:bg-gray-50' }}">
                                            <td class="px-4 py-2 font-mono text-gray-700">
                                                {{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }}
                                            </td>
                                            <td class="px-4 py-2">
                                                @if($appt->patient)
                                                    <a href="{{ route('patients.show', $appt->patient) }}" class="text-indigo-600 hover:underline font-medium">
                                                        {{ $appt->patient->first_name }} {{ $appt->patient->surname }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-gray-600">{{ $appt->appointment_type->label() }}</td>
                                            <td class="px-4 py-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    {{ $appt->cancelled_at ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                                    {{ $appt->cancelled_at ? 'Cancelled' : $appt->appointment_status->label() }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-gray-600">{{ $appt->length_minutes }} min</td>
                                            <td class="px-4 py-2 text-gray-500 text-xs">{{ $appt->display_text }}</td>
                                            <td class="px-4 py-2">
                                                <a href="{{ route('appointments.edit', $appt) }}" class="text-indigo-600 hover:text-indigo-800 text-xs">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
