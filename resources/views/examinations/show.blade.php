<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('patients.show', $examination->patient) }}" class="text-gray-400 hover:text-gray-600">&larr;</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Examination — {{ $examination->patient->first_name }} {{ $examination->patient->surname }}
                    <span class="text-gray-500 font-normal text-base ml-2">{{ $examination->examined_at->format('d/m/Y') }}</span>
                </h2>
                @if($examination->signed_at)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        Signed {{ $examination->signed_at->format('d/m/Y H:i') }}
                    </span>
                @endif
            </div>
            @if(!$examination->signed_at)
                <form method="POST" action="{{ route('examinations.sign', $examination) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" onclick="return confirm('Sign off this examination? This will generate the PDF report.')"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-green-700">
                        Sign Off
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6" x-data="{ tab: '{{ request()->get('tab', 'history') }}' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tab bar --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-6">
                    @foreach([
                        'history'       => 'History & Symptoms',
                        'ophthalmoscopy'=> 'Ophthalmoscopy',
                        'investigative' => 'Investigative',
                        'refraction'    => 'Refraction',
                    ] as $key => $label)
                        <button type="button" @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}'
                                ? 'border-indigo-500 text-indigo-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                            {{ $label }}
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- ── Tab 1: History & Symptoms ─────────────────────────────────────── --}}
            <div x-show="tab === 'history'" x-cloak>
                <form method="POST" action="{{ route('examinations.history.update', $examination) }}">
                    @csrf
                    @method('PUT')
                    @php $hs = $examination->historySymptoms; @endphp

                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">GOS Eligibility</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="gos_eligibility" value="GOS Eligibility *" />
                                <select id="gos_eligibility" name="gos_eligibility" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    @foreach($gosEligibilities as $value => $label)
                                        <option value="{{ $value }}" @selected(old('gos_eligibility', $hs?->gos_eligibility) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('gos_eligibility')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="gos_establishment_name" value="Establishment Name" />
                                <x-text-input id="gos_establishment_name" name="gos_establishment_name" type="text" class="mt-1 block w-full"
                                    value="{{ old('gos_establishment_name', $hs?->gos_establishment_name) }}" />
                            </div>
                            <div>
                                <x-input-label for="gos_establishment_town" value="Establishment Town" />
                                <x-text-input id="gos_establishment_town" name="gos_establishment_town" type="text" class="mt-1 block w-full"
                                    value="{{ old('gos_establishment_town', $hs?->gos_establishment_town) }}" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Last Examination</h3>
                        <div class="flex flex-wrap gap-6 mb-3">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="hidden" name="last_exam_first" value="0">
                                <input type="checkbox" name="last_exam_first" value="1" @checked(old('last_exam_first', $hs?->last_exam_first))
                                    class="rounded border-gray-300 text-indigo-600">
                                First ever exam
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="hidden" name="last_exam_not_known" value="0">
                                <input type="checkbox" name="last_exam_not_known" value="1" @checked(old('last_exam_not_known', $hs?->last_exam_not_known))
                                    class="rounded border-gray-300 text-indigo-600">
                                Date not known
                            </label>
                        </div>
                        <div class="w-48">
                            <x-input-label for="last_exam_date" value="Last Exam Date" />
                            <x-text-input id="last_exam_date" name="last_exam_date" type="date" class="mt-1 block w-full"
                                value="{{ old('last_exam_date', $hs?->last_exam_date?->toDateString()) }}" />
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">History</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach([
                                'reason_for_visit' => 'Reason for Visit',
                                'poh'              => 'Past Ocular History (POH)',
                                'gh'               => 'General Health (GH)',
                                'medication_notes' => 'Medication Notes',
                                'fh'               => 'Family History (FH)',
                                'foh'              => 'Family Ocular History (FOH)',
                                'other_notes'      => 'Other Notes',
                            ] as $field => $label)
                                <div>
                                    <x-input-label :for="$field" :value="$label" />
                                    <textarea :id="$field" name="{{ $field }}" rows="2"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old($field, $hs?->$field) }}</textarea>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 flex flex-wrap gap-6">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="hidden" name="has_glaucoma" value="0">
                                <input type="checkbox" name="has_glaucoma" value="1" @checked(old('has_glaucoma', $hs?->has_glaucoma))
                                    class="rounded border-gray-300 text-indigo-600">
                                Has Glaucoma
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="hidden" name="has_fhg" value="0">
                                <input type="checkbox" name="has_fhg" value="1" @checked(old('has_fhg', $hs?->has_fhg))
                                    class="rounded border-gray-300 text-indigo-600">
                                Family History of Glaucoma
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="hidden" name="is_diabetic" value="0">
                                <input type="checkbox" name="is_diabetic" value="1" @checked(old('is_diabetic', $hs?->is_diabetic))
                                    class="rounded border-gray-300 text-indigo-600">
                                Is Diabetic
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Save History &amp; Symptoms
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── Tab 2: Ophthalmoscopy ─────────────────────────────────────────── --}}
            <div x-show="tab === 'ophthalmoscopy'" x-cloak>
                <form method="POST" action="{{ route('examinations.ophthalmoscopy.update', $examination) }}">
                    @csrf
                    @method('PUT')
                    @php $oph = $examination->ophthalmoscopy; @endphp

                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <div class="mb-4">
                            <x-input-label for="ophthalmoscopy_notes" value="General Notes" />
                            <textarea id="ophthalmoscopy_notes" name="ophthalmoscopy_notes" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('ophthalmoscopy_notes', $oph?->ophthalmoscopy_notes) }}</textarea>
                        </div>

                        @foreach(['right' => 'Right Eye (R)', 'left' => 'Left Eye (L)'] as $side => $sideLabel)
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3 mt-5">{{ $sideLabel }}</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach([
                                    'pupils'          => 'Pupils',
                                    'lids_lashes'     => 'Lids & Lashes',
                                    'conjunc'         => 'Conjunctiva',
                                    'cornea'          => 'Cornea',
                                    'sclera'          => 'Sclera',
                                    'ant_ch'          => 'Anterior Chamber',
                                    'media'           => 'Media',
                                    'cd'              => 'C/D Ratio',
                                    'av'              => 'A/V Ratio',
                                    'fundus_periphery'=> 'Fundus Periphery',
                                    'macular'         => 'Macular',
                                    'ret_grading'     => 'Ret. Grading',
                                ] as $field => $label)
                                    @php $name = "{$side}_{$field}"; @endphp
                                    <div>
                                        <x-input-label :for="$name" :value="$label" />
                                        <x-text-input :id="$name" :name="$name" type="text" class="mt-1 block w-full"
                                            :value="old($name, $oph?->$name)" />
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Save Ophthalmoscopy
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── Tab 3: Investigative ─────────────────────────────────────────── --}}
            <div x-show="tab === 'investigative'" x-cloak>
                <form method="POST" action="{{ route('examinations.investigative.update', $examination) }}">
                    @csrf
                    @method('PUT')
                    @php $inv = $examination->investigative; @endphp

                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Drops</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="flex items-center gap-2 pt-5">
                                <input type="hidden" name="drops_used" value="0">
                                <input type="checkbox" id="drops_used" name="drops_used" value="1" @checked(old('drops_used', $inv?->drops_used))
                                    class="rounded border-gray-300 text-indigo-600">
                                <label for="drops_used" class="text-sm text-gray-700">Drops Used</label>
                            </div>
                            <div>
                                <x-input-label for="drops_detail_batch" value="Batch Detail" />
                                <x-text-input id="drops_detail_batch" name="drops_detail_batch" type="text" class="mt-1 block w-full"
                                    value="{{ old('drops_detail_batch', $inv?->drops_detail_batch) }}" />
                            </div>
                            <div>
                                <x-input-label for="drops_expiry" value="Expiry Date" />
                                <x-text-input id="drops_expiry" name="drops_expiry" type="date" class="mt-1 block w-full"
                                    value="{{ old('drops_expiry', $inv?->drops_expiry?->toDateString()) }}" />
                            </div>
                            <div>
                                <x-input-label for="drops_more_info" value="More Info" />
                                <x-text-input id="drops_more_info" name="drops_more_info" type="text" class="mt-1 block w-full"
                                    value="{{ old('drops_more_info', $inv?->drops_more_info) }}" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">IOP</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach(['pre_iop_r' => 'Pre IOP R', 'pre_iop_l' => 'Pre IOP L', 'post_iop_r' => 'Post IOP R', 'post_iop_l' => 'Post IOP L'] as $field => $label)
                                <div>
                                    <x-input-label :for="$field" :value="$label" />
                                    <x-text-input :id="$field" :name="$field" type="text" class="mt-1 block w-full"
                                        :value="old($field, $inv?->$field)" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Cover Test</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach([
                                'ct_with_rx' => 'CT With Rx (Distance)',
                                'ct_with_rx_near' => 'CT With Rx (Near)',
                                'ct_without_rx' => 'CT Without Rx (Distance)',
                                'ct_without_rx_near' => 'CT Without Rx (Near)',
                            ] as $field => $label)
                                <div>
                                    <x-input-label :for="$field" :value="$label" />
                                    <x-text-input :id="$field" :name="$field" type="text" class="mt-1 block w-full"
                                        :value="old($field, $inv?->$field)" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Further Tests</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach([
                                'visual_fields_r'            => 'Visual Fields R',
                                'visual_fields_l'            => 'Visual Fields L',
                                'motility'                   => 'Motility',
                                'amsler_r'                   => 'Amsler R',
                                'amsler_l'                   => 'Amsler L',
                                'omb_h'                      => 'OMB H',
                                'omb_v'                      => 'OMB V',
                                'omb_near_h'                 => 'OMB Near H',
                                'omb_near_v'                 => 'OMB Near V',
                                'keratometry_r'              => 'Keratometry R',
                                'keratometry_l'              => 'Keratometry L',
                                'npc'                        => 'NPC',
                                'stereopsis'                 => 'Stereopsis',
                                'colour_vision'              => 'Colour Vision',
                                'amplitude_of_accommodation' => 'Amplitude of Accommodation',
                            ] as $field => $label)
                                <div>
                                    <x-input-label :for="$field" :value="$label" />
                                    <x-text-input :id="$field" :name="$field" type="text" class="mt-1 block w-full"
                                        :value="old($field, $inv?->$field)" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Save Investigative Techniques
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── Tab 4: Refraction ───────────────────────────────────────────── --}}
            <div x-show="tab === 'refraction'" x-cloak>
                <form method="POST" action="{{ route('examinations.refraction.update', $examination) }}">
                    @csrf
                    @method('PUT')
                    @php $rx = $examination->refraction; @endphp

                    {{-- Rx section macro: renders an 8-column Rx row table --}}
                    @php
                        function rxRow(string $prefix, ?object $rx): string { return ''; } // placeholder — see inline below
                    @endphp

                    @foreach([
                        ['prefix' => 'current',    'title' => 'Current Rx'],
                        ['prefix' => 'prev_other', 'title' => 'Previous Rx (Other)'],
                    ] as $section)
                        @php $p = $section['prefix']; @endphp
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">{{ $section['title'] }}</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="border border-gray-200 px-2 py-1 text-left w-8"></th>
                                            <th class="border border-gray-200 px-2 py-1">Sph</th>
                                            <th class="border border-gray-200 px-2 py-1">Cyl</th>
                                            <th class="border border-gray-200 px-2 py-1">Axis</th>
                                            <th class="border border-gray-200 px-2 py-1">Prism</th>
                                            <th class="border border-gray-200 px-2 py-1">Dir</th>
                                            <th class="border border-gray-200 px-2 py-1">Add</th>
                                            <th class="border border-gray-200 px-2 py-1">VA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(['r' => 'R', 'l' => 'L'] as $eye => $eyeLabel)
                                            <tr>
                                                <td class="border border-gray-200 px-2 py-1 font-bold text-gray-600">{{ $eyeLabel }}</td>
                                                @foreach(['sph' => 'number', 'cyl' => 'number', 'axis' => 'number', 'prism' => 'number'] as $field => $type)
                                                    @php $name = "{$p}_{$eye}_{$field}"; @endphp
                                                    <td class="border border-gray-200 p-0.5">
                                                        <input type="number" name="{{ $name }}" step="{{ $field === 'axis' ? '1' : '0.25' }}"
                                                            value="{{ old($name, $rx?->$name) }}"
                                                            class="w-full px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                    </td>
                                                @endforeach
                                                @php $dirName = "{$p}_{$eye}_prism_dir"; @endphp
                                                <td class="border border-gray-200 p-0.5">
                                                    <select name="{{ $dirName }}" class="w-full px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                        <option value="">—</option>
                                                        @foreach($prismDirections as $v => $l)
                                                            <option value="{{ $v }}" @selected(old($dirName, $rx?->$dirName) === $v)>{{ $l }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                @foreach(['add' => 'number', 'va' => 'text'] as $field => $type)
                                                    @php $name = "{$p}_{$eye}_{$field}"; @endphp
                                                    <td class="border border-gray-200 p-0.5">
                                                        <input type="{{ $type }}" name="{{ $name }}"
                                                            step="{{ $field === 'add' ? '0.25' : '' }}"
                                                            value="{{ old($name, $rx?->$name) }}"
                                                            class="w-full px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($p === 'current')
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
                                    @foreach(['current_pd_r' => 'PD R', 'current_pd_l' => 'PD L', 'current_bvd' => 'BVD', 'current_bin_bcva' => 'Bin BCVA'] as $field => $label)
                                        <div>
                                            <x-input-label :for="$field" :value="$label" />
                                            <x-text-input :id="$field" :name="$field" type="text" class="mt-1 block w-full text-sm"
                                                :value="old($field, $rx?->$field)" />
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    <x-input-label for="current_comments" value="Comments" />
                                    <textarea id="current_comments" name="current_comments" rows="2"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('current_comments', $rx?->current_comments) }}</textarea>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    {{-- Retinoscopy --}}
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Retinoscopy</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach(['retino_r_value' => 'Right', 'retino_l_value' => 'Left'] as $field => $label)
                                <div>
                                    <x-input-label :for="$field" :value="$label" />
                                    <x-text-input :id="$field" :name="$field" type="text" class="mt-1 block w-full"
                                        :value="old($field, $rx?->$field)" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Subjective --}}
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Subjective Refraction</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs border-collapse">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="border border-gray-200 px-2 py-1 text-left w-8"></th>
                                        <th class="border border-gray-200 px-2 py-1">UAV</th>
                                        <th class="border border-gray-200 px-2 py-1">Sph</th>
                                        <th class="border border-gray-200 px-2 py-1">Cyl</th>
                                        <th class="border border-gray-200 px-2 py-1">Axis</th>
                                        <th class="border border-gray-200 px-2 py-1">Prism</th>
                                        <th class="border border-gray-200 px-2 py-1">Dir</th>
                                        <th class="border border-gray-200 px-2 py-1">VA</th>
                                        <th class="border border-gray-200 px-2 py-1">Near Add</th>
                                        <th class="border border-gray-200 px-2 py-1">Near Prism</th>
                                        <th class="border border-gray-200 px-2 py-1">Near Dir</th>
                                        <th class="border border-gray-200 px-2 py-1">Near VA</th>
                                        <th class="border border-gray-200 px-2 py-1">Int Add</th>
                                        <th class="border border-gray-200 px-2 py-1">Int Prism</th>
                                        <th class="border border-gray-200 px-2 py-1">Int Dir</th>
                                        <th class="border border-gray-200 px-2 py-1">Int VA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['r' => 'R', 'l' => 'L'] as $eye => $eyeLabel)
                                        <tr>
                                            <td class="border border-gray-200 px-2 py-1 font-bold text-gray-600">{{ $eyeLabel }}</td>
                                            @php $uavName = "subj_{$eye}_uav"; @endphp
                                            <td class="border border-gray-200 p-0.5">
                                                <input type="text" name="{{ $uavName }}" value="{{ old($uavName, $rx?->$uavName) }}"
                                                    class="w-16 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                            </td>
                                            @foreach(['sph', 'cyl', 'axis'] as $field)
                                                @php $name = "subj_{$eye}_{$field}"; @endphp
                                                <td class="border border-gray-200 p-0.5">
                                                    <input type="number" name="{{ $name }}" step="{{ $field === 'axis' ? '1' : '0.25' }}"
                                                        value="{{ old($name, $rx?->$name) }}"
                                                        class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                </td>
                                            @endforeach
                                            @php $prismName = "subj_{$eye}_prism"; $dirName = "subj_{$eye}_prism_dir"; @endphp
                                            <td class="border border-gray-200 p-0.5">
                                                <input type="number" name="{{ $prismName }}" step="0.25"
                                                    value="{{ old($prismName, $rx?->$prismName) }}"
                                                    class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                            </td>
                                            <td class="border border-gray-200 p-0.5">
                                                <select name="{{ $dirName }}" class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                    <option value="">—</option>
                                                    @foreach($prismDirections as $v => $l)
                                                        <option value="{{ $v }}" @selected(old($dirName, $rx?->$dirName) === $v)>{{ $l }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            @php $vaName = "subj_{$eye}_va"; @endphp
                                            <td class="border border-gray-200 p-0.5">
                                                <input type="text" name="{{ $vaName }}" value="{{ old($vaName, $rx?->$vaName) }}"
                                                    class="w-16 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                            </td>
                                            {{-- Near --}}
                                            @foreach(['near_add', 'near_prism'] as $field)
                                                @php $name = "subj_{$eye}_{$field}"; @endphp
                                                <td class="border border-gray-200 p-0.5">
                                                    <input type="number" name="{{ $name }}" step="0.25"
                                                        value="{{ old($name, $rx?->$name) }}"
                                                        class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                </td>
                                            @endforeach
                                            @php $nearDirName = "subj_{$eye}_near_prism_dir"; @endphp
                                            <td class="border border-gray-200 p-0.5">
                                                <select name="{{ $nearDirName }}" class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                    <option value="">—</option>
                                                    @foreach($prismDirections as $v => $l)
                                                        <option value="{{ $v }}" @selected(old($nearDirName, $rx?->$nearDirName) === $v)>{{ $l }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            @php $nearAcuityName = "subj_{$eye}_near_acuity"; @endphp
                                            <td class="border border-gray-200 p-0.5">
                                                <input type="text" name="{{ $nearAcuityName }}" value="{{ old($nearAcuityName, $rx?->$nearAcuityName) }}"
                                                    class="w-16 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                            </td>
                                            {{-- Intermediate --}}
                                            @foreach(['int_add', 'int_prism'] as $field)
                                                @php $name = "subj_{$eye}_{$field}"; @endphp
                                                <td class="border border-gray-200 p-0.5">
                                                    <input type="number" name="{{ $name }}" step="0.25"
                                                        value="{{ old($name, $rx?->$name) }}"
                                                        class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                </td>
                                            @endforeach
                                            @php $intDirName = "subj_{$eye}_int_prism_dir"; @endphp
                                            <td class="border border-gray-200 p-0.5">
                                                <select name="{{ $intDirName }}" class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                    <option value="">—</option>
                                                    @foreach($prismDirections as $v => $l)
                                                        <option value="{{ $v }}" @selected(old($intDirName, $rx?->$intDirName) === $v)>{{ $l }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            @php $intAcuityName = "subj_{$eye}_int_acuity"; @endphp
                                            <td class="border border-gray-200 p-0.5">
                                                <input type="text" name="{{ $intAcuityName }}" value="{{ old($intAcuityName, $rx?->$intAcuityName) }}"
                                                    class="w-16 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
                            @foreach(['subj_pd_r' => 'PD R', 'subj_pd_l' => 'PD L', 'subj_pd_combined' => 'PD Combined', 'subj_bvd' => 'BVD', 'subj_bin_bcva' => 'Bin BCVA'] as $field => $label)
                                <div>
                                    <x-input-label :for="$field" :value="$label" />
                                    <x-text-input :id="$field" :name="$field" type="text" class="mt-1 block w-full text-sm"
                                        :value="old($field, $rx?->$field)" />
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <x-input-label for="subj_notes" value="Subjective Notes" />
                            <textarea id="subj_notes" name="subj_notes" rows="2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('subj_notes', $rx?->subj_notes) }}</textarea>
                        </div>
                    </div>

                    {{-- Outcome & Recommendations --}}
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Outcome &amp; Recommendations</h3>
                        <div class="mb-4">
                            <x-input-label for="outcome" value="Outcome" />
                            <select id="outcome" name="outcome"
                                class="mt-1 block w-full md:w-64 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">— Select —</option>
                                @foreach($examOutcomes as $value => $label)
                                    <option value="{{ $value }}" @selected(old('outcome', $rx?->outcome?->value) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach([
                                'rec_distance'     => 'Distance',
                                'rec_near'         => 'Near',
                                'rec_intermediate' => 'Intermediate',
                                'rec_high_index'   => 'High Index',
                                'rec_bifocals'     => 'Bifocals',
                                'rec_varifocals'   => 'Varifocals',
                                'rec_occupational' => 'Occupational',
                                'rec_min_sub'      => 'Min Sub',
                                'rec_photochromic' => 'Photochromic',
                                'rec_hardcoat'     => 'Hardcoat',
                                'rec_tint'         => 'Tint',
                                'rec_mar'          => 'MAR',
                            ] as $field => $label)
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="hidden" name="{{ $field }}" value="0">
                                    <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $rx?->$field))
                                        class="rounded border-gray-300 text-indigo-600">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- NHS & Retest --}}
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">NHS &amp; Retest</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="nhs_voucher_dist" value="NHS Voucher (Distance)" />
                                <x-text-input id="nhs_voucher_dist" name="nhs_voucher_dist" type="text" class="mt-1 block w-full"
                                    value="{{ old('nhs_voucher_dist', $rx?->nhs_voucher_dist) }}" />
                            </div>
                            <div>
                                <x-input-label for="nhs_voucher_near" value="NHS Voucher (Near)" />
                                <x-text-input id="nhs_voucher_near" name="nhs_voucher_near" type="text" class="mt-1 block w-full"
                                    value="{{ old('nhs_voucher_near', $rx?->nhs_voucher_near) }}" />
                            </div>
                            <div>
                                <x-input-label for="retest_after" value="Retest After" />
                                <x-text-input id="retest_after" name="retest_after" type="text" class="mt-1 block w-full"
                                    placeholder="e.g. 2 years" value="{{ old('retest_after', $rx?->retest_after) }}" />
                            </div>
                            <div>
                                <x-input-label for="retest_patient_type" value="Retest Patient Type" />
                                <select id="retest_patient_type" name="retest_patient_type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">— Select —</option>
                                    @foreach($patientTypes as $value => $label)
                                        <option value="{{ $value }}" @selected(old('retest_patient_type', $rx?->retest_patient_type?->value) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="examination_comment" value="Examination Comment" />
                            <textarea id="examination_comment" name="examination_comment" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('examination_comment', $rx?->examination_comment) }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Save Refraction
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
