<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('patients.index') }}" class="text-gray-400 hover:text-gray-600">&larr;</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $patient->title }} {{ $patient->first_name }} {{ $patient->surname }}
                </h2>
                @if($patient->has_glaucoma)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Glaucoma</span>
                @endif
                @if($patient->is_diabetic)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Diabetic</span>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('patients.edit', $patient) }}"
                   class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                    Edit Details
                </a>
                <form method="POST" action="{{ route('examinations.store', $patient) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-indigo-700">
                        + New Examination
                    </button>
                </form>
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                {{-- Patient details panel --}}
                <div class="md:col-span-1 space-y-4">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Personal</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">ID</dt>
                                <dd class="font-mono text-gray-800">{{ $patient->id }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">DOB</dt>
                                <dd class="text-gray-800">{{ $patient->date_of_birth->format('d/m/Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Age</dt>
                                <dd class="text-gray-800">{{ $patient->date_of_birth->age }} years</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Sex / Gender</dt>
                                <dd class="text-gray-800">{{ $patient->sex_gender?->label() }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Patient Type</dt>
                                <dd class="text-gray-800">{{ $patient->patient_type?->label() }}</dd>
                            </div>
                            @if($patient->is_nhs)
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">NHS</dt>
                                    <dd class="text-green-700 font-medium">Yes</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Contact</h3>
                        <dl class="space-y-2 text-sm">
                            @if($patient->email)
                                <div>
                                    <dt class="text-gray-500">Email</dt>
                                    <dd class="text-gray-800 break-all">{{ $patient->email }}</dd>
                                </div>
                            @endif
                            @if($patient->telephone_mobile)
                                <div>
                                    <dt class="text-gray-500">Mobile</dt>
                                    <dd class="text-gray-800">{{ $patient->telephone_mobile }}</dd>
                                </div>
                            @endif
                            @if($patient->telephone_other)
                                <div>
                                    <dt class="text-gray-500">Other</dt>
                                    <dd class="text-gray-800">{{ $patient->telephone_other }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-gray-500">Address</dt>
                                <dd class="text-gray-800">
                                    {{ $patient->address_line_1 }}<br>
                                    {{ $patient->town_city }}, {{ $patient->post_code }}
                                    @if($patient->county), {{ $patient->county }}@endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    @if($patient->practice || $patient->doctor)
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Practice &amp; GP</h3>
                            <dl class="space-y-2 text-sm">
                                @if($patient->practice)
                                    <div>
                                        <dt class="text-gray-500">Practice</dt>
                                        <dd class="text-gray-800">{{ $patient->practice->name }}</dd>
                                    </div>
                                @endif
                                @if($patient->doctor)
                                    <div>
                                        <dt class="text-gray-500">GP</dt>
                                        <dd class="text-gray-800">{{ $patient->doctor->name }}</dd>
                                    </div>
                                @elseif($patient->doctor_other)
                                    <div>
                                        <dt class="text-gray-500">GP</dt>
                                        <dd class="text-gray-800">{{ $patient->doctor_other }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif

                    @if($patient->notes)
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Notes</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $patient->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Examination history --}}
                <div class="md:col-span-2">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-medium text-gray-800">Examination History</h3>
                            <span class="text-sm text-gray-500">{{ $examinations->count() }} {{ Str::plural('record', $examinations->count()) }}</span>
                        </div>

                        @if($examinations->isEmpty())
                            <p class="text-gray-500 text-center py-10 text-sm">No examinations on record.</p>
                        @else
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="text-left px-4 py-2 font-medium text-gray-600">Date</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600">Type</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600">Examiner</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600">Signed</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600">Report</th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($examinations as $exam)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 text-gray-800">{{ $exam->examined_at->format('d/m/Y') }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $exam->exam_type->label() }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $exam->staff?->name }}</td>
                                            <td class="px-4 py-2">
                                                @if($exam->signed_at)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                        Signed {{ $exam->signed_at->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-xs">Unsigned</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2">
                                                @if($exam->report_path)
                                                    <a href="{{ route('examinations.report', $exam) }}"
                                                       class="text-xs font-medium text-green-700 hover:text-green-900 hover:underline">
                                                        Download PDF
                                                    </a>
                                                @else
                                                    <span class="text-xs text-gray-300">
                                                        {{ $exam->signed_at ? 'Generating…' : 'Not signed' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <div class="flex items-center justify-end gap-3">
                                                    <a href="{{ route('examinations.show', $exam) }}" class="text-indigo-600 hover:text-indigo-800 text-xs">Open</a>
                                                    @if($exam->signed_at)
                                                        <span class="text-xs text-gray-300 cursor-not-allowed" style="padding-left: 10px;" title="Signed examinations cannot be deleted">Delete</span>
                                                    @else
                                                        <form method="POST" action="{{ route('examinations.destroy', $exam) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800" style="padding-left: 10px;" 
                                                                onclick="return confirm('Are you sure you want to delete this examination? This cannot be undone.')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

            {{-- GOS Forms --}}
            <div class="mt-5 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-5 py-3 border-b border-gray-100">
                    <h3 class="font-medium text-gray-800">GOS Forms</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach(['GOS1' => 'GOS1 — NHS Sight Test', 'GOS3' => 'GOS3 — NHS Optical Voucher', 'GOS6' => 'GOS6 — Domiciliary Visit'] as $type => $label)
                        @php $form = $gosforms[$type] ?? null; $eligible = $form?->effectiveEligibility() ?? false; @endphp
                        <div class="px-5 py-4" x-data="{ open: false }">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-medium text-gray-800">{{ $label }}</span>
                                    @if($eligible)
                                        <span style="background:#dcfce7; color:#15803d;" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">Eligible</span>
                                    @else
                                        <span style="background:#fee2e2; color:#b91c1c;" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">Not Eligible</span>
                                    @endif
                                    @if($form && $form->admin_override !== null)
                                        <span class="text-xs text-gray-400 italic">Manually overridden</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($eligible)
                                        <a href="{{ route('patients.gos.form', [$patient, $type]) }}" target="_blank"
                                           class="text-xs font-medium px-2 py-1 rounded text-white"
                                           style="background:#003087;">Fill Form</a>
                                    @endif
                                    <button type="button" @click="open = !open"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                        <span x-show="!open">Override &darr;</span>
                                        <span x-show="open" x-cloak>Close &uarr;</span>
                                    </button>
                                </div>
                            </div>

                            @if($form && $form->override_note)
                                <p class="mt-1 text-xs text-gray-500 italic">Note: {{ $form->override_note }}</p>
                            @endif

                            <div x-show="open" x-cloak class="mt-3 p-3 bg-gray-50 rounded-md border border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">Override auto-calculated eligibility for this patient.</p>
                                <div class="flex flex-wrap gap-2 items-end">
                                    <form method="POST" action="{{ route('patients.gos.update', [$patient, $type]) }}" class="flex flex-wrap gap-2 items-end">
                                        @csrf
                                        <input type="hidden" name="admin_override" value="1">
                                        <div>
                                            <label class="text-xs text-gray-500 block mb-1">Override note (optional)</label>
                                            <input type="text" name="override_note" value="{{ $form?->override_note }}"
                                                placeholder="Reason for override"
                                                class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-56">
                                        </div>
                                        <button type="submit"
                                            style="background:#15803d; color:#fff;"
                                            class="px-3 py-1.5 text-xs font-medium rounded-md hover:opacity-90">
                                            Mark Eligible
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('patients.gos.update', [$patient, $type]) }}">
                                        @csrf
                                        <input type="hidden" name="admin_override" value="0">
                                        <button type="submit"
                                            style="background:#b91c1c; color:#fff;"
                                            class="px-3 py-1.5 text-xs font-medium rounded-md hover:opacity-90">
                                            Mark Not Eligible
                                        </button>
                                    </form>
                                    @if($form && $form->admin_override !== null)
                                        <form method="POST" action="{{ route('patients.gos.clear', [$patient, $type]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-gray-500 hover:text-gray-700 border border-gray-300 rounded-md bg-white">
                                                Clear Override
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
