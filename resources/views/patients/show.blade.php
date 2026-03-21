<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap gap-y-2 items-start justify-between">
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('patients.index') }}" class="text-gray-400 hover:text-gray-600">&larr;</a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ $patient->title }} {{ $patient->first_name }} {{ $patient->surname }}
                </h2>
                @if($patient->has_glaucoma)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Glaucoma</span>
                @endif
                @if($patient->is_diabetic)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Diabetic</span>
                @endif
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('patients.edit', $patient) }}"
                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                    Edit Details
                </a>
                @php $hasSigned = $examinations->whereNotNull('signed_at')->isNotEmpty(); @endphp
                @if($hasSigned)
                    <span title="Cannot delete — this patient has signed examination records."
                          class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-400 text-sm font-medium border border-gray-200 rounded-md cursor-not-allowed select-none">
                        Delete Patient
                    </span>
                @else
                    <form method="POST" action="{{ route('patients.destroy', $patient) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to permanently delete this patient? All their appointments, examinations and GOS forms will also be deleted. This cannot be undone.')">
                            Delete Patient
                        </button>
                    </form>
                @endif
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left column: info cards --}}
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Personal</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500 dark:text-gray-400">ID</dt>
                                <dd class="font-mono text-gray-800 dark:text-gray-200">{{ $patient->id }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500 dark:text-gray-400">DOB</dt>
                                <dd class="text-gray-800 dark:text-gray-200">{{ $patient->date_of_birth->format('d/m/Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500 dark:text-gray-400">Age</dt>
                                <dd class="text-gray-800 dark:text-gray-200">{{ $patient->date_of_birth->age }} years</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500 dark:text-gray-400">Sex / Gender</dt>
                                <dd class="text-gray-800 dark:text-gray-200">{{ $patient->sex_gender?->label() }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500 dark:text-gray-400">Patient Type</dt>
                                <dd class="text-gray-800 dark:text-gray-200">{{ $patient->patient_type?->label() }}</dd>
                            </div>
                            @if($patient->is_nhs)
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">NHS</dt>
                                    <dd class="text-green-700 font-medium">Yes</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Contact</h3>
                        <dl class="space-y-2 text-sm">
                            @if($patient->email)
                                <div>
                                    <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="text-gray-800 dark:text-gray-200 break-all">{{ $patient->email }}</dd>
                                </div>
                            @endif
                            @if($patient->telephone_mobile)
                                <div>
                                    <dt class="text-gray-500 dark:text-gray-400">Mobile</dt>
                                    <dd class="text-gray-800 dark:text-gray-200">{{ $patient->telephone_mobile }}</dd>
                                </div>
                            @endif
                            @if($patient->telephone_other)
                                <div>
                                    <dt class="text-gray-500 dark:text-gray-400">Other</dt>
                                    <dd class="text-gray-800 dark:text-gray-200">{{ $patient->telephone_other }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Address</dt>
                                <dd class="text-gray-800 dark:text-gray-200">
                                    {{ $patient->address_line_1 }}<br>
                                    {{ $patient->town_city }}, {{ $patient->post_code }}
                                    @if($patient->county), {{ $patient->county }}@endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    @if($patient->practice || $patient->doctor)
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
                            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Practice &amp; GP</h3>
                            <dl class="space-y-2 text-sm">
                                @if($patient->practice)
                                    <div>
                                        <dt class="text-gray-500 dark:text-gray-400">Practice</dt>
                                        <dd class="text-gray-800 dark:text-gray-200">{{ $patient->practice->name }}</dd>
                                    </div>
                                @endif
                                @if($patient->doctor)
                                    <div>
                                        <dt class="text-gray-500 dark:text-gray-400">GP</dt>
                                        <dd class="text-gray-800 dark:text-gray-200">{{ $patient->doctor->name }}</dd>
                                    </div>
                                @elseif($patient->doctor_other)
                                    <div>
                                        <dt class="text-gray-500 dark:text-gray-400">GP</dt>
                                        <dd class="text-gray-800 dark:text-gray-200">{{ $patient->doctor_other }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif

                    @if($patient->notes)
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Notes</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $patient->notes }}</p>
                        </div>
                    @endif

                </div>

                {{-- Right column: examination history --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h3 class="font-medium text-gray-800 dark:text-gray-100">Examination History</h3>
                            <span class="text-sm text-gray-500">{{ $examinations->count() }} {{ Str::plural('record', $examinations->count()) }}</span>
                        </div>

                        @if($examinations->isEmpty())
                            <p class="text-gray-500 text-center py-10 text-sm">No examinations on record.</p>
                        @else
                            <div class="overflow-x-auto">
                            <table class="w-full text-sm min-w-[640px]">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-400 whitespace-nowrap">Date</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-400 whitespace-nowrap">Examiner</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-400">Subjective Rx</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-400">Notes</th>
                                        <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-400 whitespace-nowrap">Report / Status</th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach($examinations as $exam)
                                        @php
                                            $rx = $exam->refraction;

                                            // Format a numeric value with mandatory sign; null if absent or zero
                                            $fmtSigned = function(?string $v): ?string {
                                                if ($v === null || $v === '') return null;
                                                $n = (float) $v;
                                                if ($n === 0.0) return null;
                                                return ($n >= 0 ? '+' : '') . number_format($n, 2);
                                            };

                                            // True if a field value is considered present
                                            $hasValue = fn($v) => $v !== null && $v !== '' && !(is_numeric($v) && (float) $v === 0.0);

                                            // Ordered display fields: [column suffix, label, signed numeric?]
                                            $rxFields = [
                                                ['uav',         'UAV',     false],
                                                ['sph',         'SPH',     true],
                                                ['cyl',         'CYL',     true],
                                                ['axis',        'Axis',    false],
                                                ['va',          'VA',      false],
                                                ['near_add',    'Add',     true],
                                                ['near_acuity', 'Near VA', false],
                                                ['int_add',     'Int',     true],
                                            ];

                                            // Build one Rx line for an eye using only fields with real values
                                            $rxLine = function(string $eye) use ($rx, $fmtSigned, $hasValue, $rxFields): string {
                                                if (!$rx) return '—';
                                                $parts = [];
                                                foreach ($rxFields as [$suffix, $label, $signed]) {
                                                    $v = $rx->{"subj_{$eye}_{$suffix}"};
                                                    if (!$hasValue($v)) continue;
                                                    $parts[] = $label . ' ' . ($signed ? $fmtSigned((string) $v) : $v);
                                                }
                                                return empty($parts) ? '—' : implode(', ', $parts);
                                            };

                                            // $hasRx: true if any subj_r_* or subj_l_* attribute has a real value
                                            $hasRx = false;
                                            if ($rx) {
                                                foreach ($rx->getAttributes() as $col => $v) {
                                                    if ((str_starts_with($col, 'subj_r_') || str_starts_with($col, 'subj_l_')) && $hasValue($v)) {
                                                        $hasRx = true;
                                                        break;
                                                    }
                                                }
                                            }

                                            $rxR = $rxLine('r');
                                            $rxL = $rxLine('l');

                                            // Notes: prefer examination_comment, fall back to exam notes
                                            $rawNote = $rx?->examination_comment ?? $exam->notes ?? '';
                                            $note = mb_strlen($rawNote) > 60
                                                ? mb_substr($rawNote, 0, 60) . '…'
                                                : $rawNote;
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                                {{ $exam->examined_at->format('d/m/Y') }}
                                                <span class="block text-xs text-gray-400 dark:text-gray-500">{{ $exam->created_at->format('H:i') }}</span>
                                            </td>
                                            <td class="px-4 py-2 max-w-xs">
                                                <span class="text-gray-600 dark:text-gray-400">{{ $exam->staff?->name ?? '—' }}</span>
                                                @if($exam->last_edited_at)
                                                    <span class="block text-xs text-gray-400 dark:text-gray-500 whitespace-normal break-words">
                                                        Edited: {{ $exam->lastEditedBy?->name ?? '—' }} {{ $exam->last_edited_at->format('d/m/Y H:i') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2" style="min-width:180px">
                                                @if($hasRx)
                                                    <a href="{{ route('examinations.show', $exam) . '#tab-refraction' }}"
                                                       class="text-xs text-gray-700 dark:text-gray-300 whitespace-normal break-words">
                                                        <p><span class="font-medium text-gray-500 dark:text-gray-400">R:</span> {{ $rxR }}</p>
                                                        <p><span class="font-medium text-gray-500 dark:text-gray-400">L:</span> {{ $rxL }}</p>
                                                    </a>
                                                @else
                                                    <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-xs text-gray-600 dark:text-gray-400 max-w-[180px]">
                                                {{ $note ?: '—' }}
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                @if($exam->report_path)
                                                    <a href="{{ route('examinations.report', $exam) }}"
                                                       class="text-xs font-medium text-green-700 hover:text-green-900 hover:underline">
                                                        Download PDF
                                                    </a>
                                                @else
                                                    <span class="text-xs text-gray-300 dark:text-gray-600">
                                                        {{ $exam->signed_at ? 'Generating…' : 'Not signed' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <div class="flex items-center justify-end gap-3">
                                                    <a href="{{ route('examinations.show', $exam) }}" class="text-indigo-600 hover:text-indigo-800 text-xs">Open</a>
                                                    @if($exam->signed_at)
                                                        <span style="padding-left: 10px;" class="text-xs text-gray-300 dark:text-gray-600 cursor-not-allowed" title="Signed examinations cannot be deleted">Delete</span>
                                                    @else
                                                        <form method="POST" action="{{ route('examinations.destroy', $exam) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button style="padding-left: 10px" type="submit" class="text-xs text-red-600 hover:text-red-800"
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
                            </div>{{-- end overflow-x-auto --}}
                        @endif
                    </div>
                </div>
            </div>

            {{-- GOS Forms — full width --}}
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-medium text-gray-800 dark:text-gray-100">GOS Forms</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach(['GOS1' => 'GOS1 — NHS Sight Test', 'GOS3' => 'GOS3 — NHS Optical Voucher', 'GOS6' => 'GOS6 — Domiciliary Visit'] as $type => $label)
                        @php $form = $gosforms[$type] ?? null; $eligible = $form?->effectiveEligibility() ?? false; @endphp
                        <div class="px-5 py-4" x-data="{ open: false }">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $label }}</span>
                                    @if($eligible)
                                        <span style="background:#dcfce7; color:#15803d;" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">Eligible</span>
                                    @else
                                        <span style="background:#fee2e2; color:#b91c1c;" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">Not Eligible</span>
                                    @endif
                                    @if($form && $form->admin_override !== null)
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">Manually overridden</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($eligible)
                                        <a href="{{ route('patients.gos.form', [$patient, $type]) }}"
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
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 italic">Note: {{ $form->override_note }}</p>
                            @endif

                            <div x-show="open" x-cloak class="mt-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Override auto-calculated eligibility for this patient.</p>
                                <div class="flex flex-wrap gap-2 items-end">
                                    <form method="POST" action="{{ route('patients.gos.update', [$patient, $type]) }}" class="flex flex-wrap gap-2 items-end">
                                        @csrf
                                        <input type="hidden" name="admin_override" value="1">
                                        <div>
                                            <label class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Override note (optional)</label>
                                            <input type="text" name="override_note" value="{{ $form?->override_note }}"
                                                placeholder="Reason for override"
                                                class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-56">
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
                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700">
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

            {{-- Documents & Images — full width --}}
            <div class="mt-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-medium text-gray-800 dark:text-gray-100">Documents &amp; Images</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $patient->documents->count() }} {{ Str::plural('file', $patient->documents->count()) }}</span>
                </div>

                {{-- Upload form --}}
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <form method="POST" action="{{ route('patients.documents.store', $patient) }}" enctype="multipart/form-data"
                          class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div class="w-full sm:w-48">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" maxlength="255" required
                                   placeholder="e.g. Referral letter, Fundus photo…"
                                   class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('title')
                                <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">File <span class="text-red-500">*</span> <span class="font-normal text-gray-400">(PDF or image)</span></label>
                            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp" required
                                   class="text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('file')
                                <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Notes <span class="font-normal text-gray-400 dark:text-gray-500">(optional)</span></label>
                            <input type="text" name="description" value="{{ old('description') }}" maxlength="1000"
                                   placeholder="Optional notes…"
                                   class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-indigo-700">
                            Upload
                        </button>
                    </form>
                </div>

                {{-- File list --}}
                @if($patient->documents->isEmpty())
                    <p class="px-5 py-4 text-center text-sm text-gray-400 dark:text-gray-500">No files uploaded yet.</p>
                @else
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($patient->documents->sortByDesc('created_at') as $doc)
                            <li class="px-5 py-3 flex items-start gap-4">
                                {{-- Image thumbnail --}}
                                @if($doc->file_type === 'image')
                                    <a href="{{ route('documents.view', $doc) }}" target="_blank" class="shrink-0">
                                        <img src="{{ route('documents.view', $doc) }}"
                                             alt="{{ $doc->title }}"
                                             class="w-16 h-16 object-cover rounded-md border border-gray-200 dark:border-gray-600">
                                    </a>
                                @else
                                    <div class="shrink-0 w-16 h-16 flex items-center justify-center rounded-md border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                                        <svg class="w-7 h-7 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    </div>
                                @endif

                                {{-- Details --}}
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $doc->title }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $doc->filename }}</p>
                                    @if($doc->description)
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">{{ $doc->description }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ $doc->created_at->format('d/m/Y H:i') }}
                                        @if($doc->uploadedBy)
                                            &middot; {{ $doc->uploadedBy->name }}
                                        @endif
                                    </p>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-3 shrink-0 mt-0.5">
                                    @if($doc->file_type === 'image')
                                        <a href="{{ route('documents.view', $doc) }}" target="_blank"
                                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                                            View
                                        </a>
                                    @endif
                                    <a href="{{ route('documents.download', $doc) }}"
                                       class="text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                                        Download
                                    </a>
                                    <form method="POST" action="{{ route('documents.destroy', $doc) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800"
                                                onclick="return confirm('Delete this file? This cannot be undone.')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
