<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('patients.show', $examination->patient) }}" class="text-gray-400 hover:text-gray-600">&larr;</a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
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

    <div class="py-6" x-data="{
        tab: 'history',
        init() {
            const hash = window.location.hash;
            if (hash === '#tab-ophthalmoscopy') this.tab = 'ophthalmoscopy';
            else if (hash === '#tab-investigative') this.tab = 'investigative';
            else if (hash === '#tab-refraction') this.tab = 'refraction';
            else this.tab = 'history';
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tab bar --}}
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-6">
                    @foreach([
                        'history'       => 'History & Symptoms',
                        'ophthalmoscopy'=> 'Ophthalmoscopy',
                        'investigative' => 'Investigative',
                        'refraction'    => 'Refraction',
                    ] as $key => $label)
                        <button type="button" @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}'
                                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300'"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                            {{ $label }}
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- ── Tab 1: History & Symptoms ─────────────────────────────────────── --}}
            <div id="tab-history" x-show="tab === 'history'" x-cloak>
                <form method="POST" action="{{ route('examinations.history.update', $examination) }}">
                    @csrf
                    @method('PUT')
                    @php $hs = $examination->historySymptoms; @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">GOS Eligibility</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="gos_eligibility" value="GOS Eligibility *" />
                                <select id="gos_eligibility" name="gos_eligibility" required
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
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

                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Last Examination</h3>
                        <div class="flex flex-wrap gap-6 mb-3">
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="hidden" name="last_exam_first" value="0">
                                <input type="checkbox" name="last_exam_first" value="1" @checked(old('last_exam_first', $hs?->last_exam_first))
                                    class="rounded border-gray-300 text-indigo-600">
                                First ever exam
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
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

                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">History</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="reason_for_visit" value="Reason for Visit" />
                                <textarea id="reason_for_visit" name="reason_for_visit" rows="2" x-ref="reason_for_visit"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('reason_for_visit', $hs?->reason_for_visit) }}</textarea>
                                <button type="button" @click="$refs.reason_for_visit.value = 'Last test c/o blurry\nDV / NV\nNo headaches, diplopia or pain\nNo flashes, floaters, shadows or any other symptoms\nPhotosensitivity no'"
                                    class="mt-1 text-xs text-gray-400 hover:text-gray-600">Set default</button>
                            </div>
                            <div>
                                <x-input-label for="poh" value="Past Ocular History (POH)" />
                                <textarea id="poh" name="poh" rows="2" x-ref="poh"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('poh', $hs?->poh) }}</textarea>
                                <button type="button" @click="$refs.poh.value = 'No HES\nno squints or lazy eye\nno infections, ops or injuries\nno cats or glauc'"
                                    class="mt-1 text-xs text-gray-400 hover:text-gray-600">Set default</button>
                            </div>
                            <div>
                                <x-input-label for="gh" value="General Health (GH)" />
                                <textarea id="gh" name="gh" rows="2" x-ref="gh"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('gh', $hs?->gh) }}</textarea>
                                <button type="button" @click="$refs.gh.value = 'No Heath issues\nNo T1/T2 Diabetes'"
                                    class="mt-1 text-xs text-gray-400 hover:text-gray-600">Set default</button>
                            </div>
                            <div>
                                <x-input-label for="medication_notes" value="Medication Notes" />
                                <textarea id="medication_notes" name="medication_notes" rows="2" x-ref="medication_notes"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('medication_notes', $hs?->medication_notes) }}</textarea>
                                <button type="button" @click="$refs.medication_notes.value = 'Nil'"
                                    class="mt-1 text-xs text-gray-400 hover:text-gray-600">Set default</button>
                            </div>
                            <div>
                                <x-input-label for="fh" value="Family History (FH)" />
                                <textarea id="fh" name="fh" rows="2" x-ref="fh"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('fh', $hs?->fh) }}</textarea>
                                <button type="button" @click="$refs.fh.value = 'No T1/T2 Diabetes'"
                                    class="mt-1 text-xs text-gray-400 hover:text-gray-600">Set default</button>
                            </div>
                            <div>
                                <x-input-label for="foh" value="Family Ocular History (FOH)" />
                                <textarea id="foh" name="foh" rows="2" x-ref="foh"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('foh', $hs?->foh) }}</textarea>
                                <button type="button" @click="$refs.foh.value = 'No FH of Glaucoma\nNo FH of AMD\nNo FH of Strabismus\nNo Amblyopia or any other eye conditions'"
                                    class="mt-1 text-xs text-gray-400 hover:text-gray-600">Set default</button>
                            </div>
                            <div>
                                <x-input-label for="other_notes" value="Other Notes" />
                                <textarea id="other_notes" name="other_notes" rows="2"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('other_notes', $hs?->other_notes) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-6">
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="hidden" name="has_glaucoma" value="0">
                                <input type="checkbox" name="has_glaucoma" value="1" @checked(old('has_glaucoma', $hs?->has_glaucoma))
                                    class="rounded border-gray-300 text-indigo-600">
                                Has Glaucoma
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="hidden" name="has_fhg" value="0">
                                <input type="checkbox" name="has_fhg" value="1" @checked(old('has_fhg', $hs?->has_fhg))
                                    class="rounded border-gray-300 text-indigo-600">
                                Family History of Glaucoma
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="hidden" name="is_diabetic" value="0">
                                <input type="checkbox" name="is_diabetic" value="1" @checked(old('is_diabetic', $hs?->is_diabetic))
                                    class="rounded border-gray-300 text-indigo-600">
                                Is Diabetic
                            </label>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
                        <x-input-label value="Medications" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 mb-2">Tick all that apply</p>
                        @php
                            $checkedMeds = old('medications', $hs?->medications ?? []);
                            $medList = [
                                'ADCAL'                    => 'adcal',
                                'ALENDRONATE'              => 'alendronate',
                                'ALLOPURINOL'              => 'allopurinol',
                                'AMITRIPTYLINE'            => 'amitriptyline',
                                'AMLODIPINE'               => 'amlodipine',
                                'ASPIRIN'                  => 'aspirin',
                                'ATENOLOL'                 => 'atenolol',
                                'ATIVAN'                   => 'ativan',
                                'ATROVENT'                 => 'atrovent',
                                'BENDROFLUAZIDE'           => 'bendrofluazide',
                                'BENDROFLUMETHIAZIDE'      => 'bendroflumethiazide',
                                'BETOPTIC'                 => 'betoptic',
                                'BISOPROLOL FUMARATE'      => 'bisoprolol_fumarate',
                                'BRUFEN'                   => 'brufen',
                                'CANDESARTAN'              => 'candesartan',
                                'CARBAMAZEPINE'            => 'carbamazepine',
                                'CLOBAZAM'                 => 'clobazam',
                                'CO-CODAMOL'               => 'co-codamol',
                                'CO-DYDRAMOL'              => 'co-dydramol',
                                'COMBIVENT'                => 'combivent',
                                'DIAZEPAM'                 => 'diazepam',
                                'DIGOXIN'                  => 'digoxin',
                                'DOXAZOSIN'                => 'doxazosin',
                                'EPANUTUN'                 => 'epanutun',
                                'EPILIM'                   => 'epilim',
                                'FUROSEMIDE'               => 'furosemide',
                                'GABAPENTIN'               => 'gabapentin',
                                'GLICLAZIDE'               => 'gliclazide',
                                'HYDROCHLOROQUINE'         => 'hydrochloroquine',
                                'INSULIN'                  => 'insulin',
                                'IRBESARTAN'               => 'irbesartan',
                                'LANSOPRAZOLE'             => 'lansoprazole',
                                'LISINOPRIL'               => 'lisinopril',
                                'LOSARTAN'                 => 'losartan',
                                'METFORMIN HYDROCHLORIDE'  => 'metformin_hydrochloride',
                                'OMEPRAZOLE'               => 'omeprazole',
                                'PARACETAMOL'              => 'paracetamol',
                                'PRIMIDONE'                => 'primidone',
                                'PROCYCLIDINE'             => 'procyclidine',
                                'RAMIPRIL'                 => 'ramipril',
                                'RANITIDINE'               => 'ranitidine',
                                'RISPERIDONE'              => 'risperidone',
                                'SALBUTAMOL'               => 'salbutamol',
                                'STATIN'                   => 'statin',
                                'TAMSULOSIN'               => 'tamsulosin',
                                'TEGRETOL'                 => 'tegretol',
                                'THYROXINE'                => 'thyroxine',
                                'TIMOLOL'                  => 'timolol',
                                'TIMOPTOL'                 => 'timoptol',
                                'TRAMADOL'                 => 'tramadol',
                                'VENTOLIN'                 => 'ventolin',
                                'WARFARIN'                 => 'warfarin',
                                'XALATAN'                  => 'xalatan',
                            ];
                        @endphp
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-1.5 mt-1">
                            @foreach($medList as $label => $value)
                                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                    <input type="checkbox" name="medications[]" value="{{ $value }}"
                                        @checked(in_array($value, (array) $checkedMeds))
                                        class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    {{ $label }}
                                </label>
                            @endforeach
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
            <div id="tab-ophthalmoscopy" x-show="tab === 'ophthalmoscopy'" x-cloak>
                <form method="POST" action="{{ route('examinations.ophthalmoscopy.update', $examination) }}">
                    @csrf
                    @method('PUT')
                    @php $oph = $examination->ophthalmoscopy; @endphp

                    @php
                    $ophFields = [
                        'pupils'           => ['label' => 'Pupils',           'default' => 'Distance ,consensual and near all normal', 'options' => ['PERRL','PERRLA','RRL','Distance ,consensual and near all normal','Argyll robertson pupil','Distorted','Holmes aldie pupil','Horners syndrome','No rapd (marcus gunn)','Rapd (marcus gunn) present in']],
                        'lids_lashes'      => ['label' => 'Lids & Lashes',    'default' => 'Clear',                                   'options' => ['Clear','Blepharitis','Ectropion','Entropion','External hordeolum (stye)','Internal hordeolum (cyst of moll)','Meibomian cyst (chalazion)','Meibomian gland dysfunction','Ptosis','Trichiasis','Xantalasma','(cyst of zeis)']],
                        'lashes'           => ['label' => 'Lashes',           'default' => 'Clear',                                   'options' => ['Clear','Blepharitis','Trichiasis']],
                        'conjunc'          => ['label' => 'Conjunctiva',      'default' => 'Clear',                                   'options' => ['Clear','Chemosis','Conjunctival folds','Conjunctivitis','Difuse injection','Follicles','Localised injection']],
                        'cornea'           => ['label' => 'Cornea',           'default' => 'Clear',                                   'options' => ['Clear','Cillary injection','Forign body scarring','Fuchs endothelial dystrophy','Hazy','Keratic precipitates','Keratitis','Opacities','Scarring']],
                        'sclera'           => ['label' => 'Sclera',           'default' => 'Clear',                                   'options' => ['Clear','Diffuse scleritis','Nodular scleritis','Thinning']],
                        'ant_ch'           => ['label' => 'Anterior Chamber', 'default' => 'WHITE And QUIET',                         'options' => ['AQUEOUS FLARE','VAN HERICK GRADE 1','VAN HERICK GRADE 2','VAN HERICK GRADE 3','VAN HERICK GRADE 4','WHITE And QUIET']],
                        'media'            => ['label' => 'Media',            'default' => 'Clear',                                   'options' => ['Clear','Aphakic','Asteroid hyalosis','Cataract','Congenital cataract','Cortical cataract','Early lens changes','Hazy','IOL','Nuclear sclerosis','Posterior capsular cataract','Posterior subcapsular cataract','Traumatic cataract','Vitreous floaters']],
                        'cd'               => ['label' => 'C/D Ratio',        'default' => null,                                      'options' => ['0.1','0.15','0.2','0.25','0.3','0.35','0.4','0.45','0.5','0.55','0.6','0.65','0.7','0.75','0.8','0.85','0.9','Flat','Not seen']],
                        'av'               => ['label' => 'A/V Ratio',        'default' => null,                                      'options' => ['1/2','1/3','2/3 regular','Arteriosclerotic grade 1','Arteriosclerotic grade 2','Arteriosclerotic grade 3','Arteriosclerotic grade 4','Nipping','Not seen','Tortuous']],
                        'fundus_periphery' => ['label' => 'Fundus Periphery', 'default' => 'clear as best seen',                      'options' => ['clear as best seen','Flat','Not seen','NPDR - mild','NPDR - moderate','NPDR - severe','PDR','Flat and healthy','Chorioretinal scarring','Lattice degeneration','Peripheral retinal degeneration','Retinal detacment scarring','Retinoschisis']],
                        'macular'          => ['label' => 'Macular',          'default' => 'Clear and healthy',                       'options' => ['Clear and healthy','Clear reflexes','Dry / atrophic armd','Dull','Hard drusen','Macular hole','Maculopathy','Reflexes not seen','Rpe defects','Soft drusen','Wet / neovascular armd']],
                        'ret_grading'      => ['label' => 'Ret. Grading',     'default' => null,                                      'options' => ['ROMO','RIMO','RIMI','RIMNR','R2MO','R2M1','R2MNR','R3MO','R3M1','R3MNR']],
                    ];
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4"
                         x-data="{
                             copyRtoL() {
                                 const fields = ['pupils','lids_lashes','lashes','conjunc','cornea','sclera','ant_ch','media','cd','av','fundus_periphery','macular','ret_grading'];
                                 fields.forEach(f => {
                                     const r = document.querySelector('[name=right_' + f + ']');
                                     const l = document.querySelector('[name=left_'  + f + ']');
                                     if (r && l) l.value = r.value;
                                 });
                             },
                             setDefaults(eye) {
                                 const defaults = {
                                     pupils:           'Distance ,consensual and near all normal',
                                     lids_lashes:      'Clear',
                                     lashes:           'Clear',
                                     conjunc:          'Clear',
                                     cornea:           'Clear',
                                     sclera:           'Clear',
                                     ant_ch:           'WHITE And QUIET',
                                     media:            'Clear',
                                     fundus_periphery: 'clear as best seen',
                                     macular:          'Clear and healthy',
                                 };
                                 Object.entries(defaults).forEach(([f, v]) => {
                                     const el = document.querySelector('[name=' + eye + '_' + f + ']');
                                     if (el) el.value = v;
                                 });
                             }
                         }">

                        <div class="mb-4">
                            <x-input-label for="ophthalmoscopy_notes" value="Test Method" />
                            <textarea id="ophthalmoscopy_notes" name="ophthalmoscopy_notes" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('ophthalmoscopy_notes', $oph?->ophthalmoscopy_notes) }}</textarea>
                        </div>

                        {{-- Right Eye --}}
                        <div class="flex items-center justify-between mt-5 mb-3">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Right Eye (R)</h3>
                            <button type="button" @click="setDefaults('right')"
                                class="inline-flex items-center px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-medium rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                                Set Default (R)
                            </button>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($ophFields as $field => $cfg)
                                @php $name = "right_{$field}"; $saved = old($name, $oph?->$name); @endphp
                                <div>
                                    <x-input-label :for="$name" :value="$cfg['label']" />
                                    <select id="{{ $name }}" name="{{ $name }}"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        <option value="">— Select —</option>
                                        @foreach($cfg['options'] as $opt)
                                            <option value="{{ $opt }}" @selected($saved === $opt)>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>

                        {{-- Copy button --}}
                        <div class="mt-4 flex items-center gap-2">
                            <button type="button" @click="copyRtoL()"
                                class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700">
                                Copy Right &rarr; Left
                            </button>
                            <span class="text-xs text-gray-400">Copies all Right Eye values to Left Eye</span>
                        </div>

                        {{-- Left Eye --}}
                        <div class="flex items-center justify-between mt-5 mb-3">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Left Eye (L)</h3>
                            <button type="button" @click="setDefaults('left')"
                                class="inline-flex items-center px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-medium rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                                Set Default (L)
                            </button>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($ophFields as $field => $cfg)
                                @php $name = "left_{$field}"; $saved = old($name, $oph?->$name); @endphp
                                <div>
                                    <x-input-label :for="$name" :value="$cfg['label']" />
                                    <select id="{{ $name }}" name="{{ $name }}"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        <option value="">— Select —</option>
                                        @foreach($cfg['options'] as $opt)
                                            <option value="{{ $opt }}" @selected($saved === $opt)>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Save Ophthalmoscopy
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── Tab 3: Investigative ─────────────────────────────────────────── --}}
            <div id="tab-investigative" x-show="tab === 'investigative'" x-cloak>
                <form method="POST" action="{{ route('examinations.investigative.update', $examination) }}">
                    @csrf
                    @method('PUT')
                    @php $inv = $examination->investigative; @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Drops</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="flex items-center gap-2 pt-5">
                                <input type="hidden" name="drops_used" value="0">
                                <input type="checkbox" id="drops_used" name="drops_used" value="1" @checked(old('drops_used', $inv?->drops_used))
                                    class="rounded border-gray-300 text-indigo-600">
                                <label for="drops_used" class="text-sm text-gray-700 dark:text-gray-300">Drops Used</label>
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
                                <x-input-label for="drops_more_info" value="Drops Instilled" />
                                <x-text-input id="drops_more_info" name="drops_more_info" type="text" class="mt-1 block w-full"
                                    value="{{ old('drops_more_info', $inv?->drops_more_info) }}" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4"
                         x-data="{
                             setNow(refName) {
                                 const now = new Date();
                                 const hh = String(now.getHours()).padStart(2, '0');
                                 const mm = String(now.getMinutes()).padStart(2, '0');
                                 this.$refs[refName].value = hh + ':' + mm;
                             }
                         }">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">IOP</h3>

                        {{-- Pre IOP --}}
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Pre IOP</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <x-input-label for="pre_iop_method" value="Method" />
                                <select id="pre_iop_method" name="pre_iop_method"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">— Select —</option>
                                    @foreach(\App\Enums\IopMethod::options() as $value => $label)
                                        <option value="{{ $value }}" @selected(old('pre_iop_method', $inv?->pre_iop_method?->value) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="pre_iop_r" value="Pre IOP R" />
                                <x-text-input id="pre_iop_r" name="pre_iop_r" type="text" class="mt-1 block w-full"
                                    value="{{ old('pre_iop_r', $inv?->pre_iop_r) }}" />
                            </div>
                            <div>
                                <x-input-label for="pre_iop_l" value="Pre IOP L" />
                                <x-text-input id="pre_iop_l" name="pre_iop_l" type="text" class="mt-1 block w-full"
                                    value="{{ old('pre_iop_l', $inv?->pre_iop_l) }}" />
                            </div>
                            <div>
                                <x-input-label for="pre_iop_time" value="Time" />
                                <div class="mt-1 flex items-center gap-2">
                                    <x-text-input id="pre_iop_time" name="pre_iop_time" type="time" class="block w-full"
                                        placeholder="HH:MM" x-ref="pre_iop_time"
                                        value="{{ old('pre_iop_time', $inv?->pre_iop_time) }}" />
                                    <button type="button" @click="setNow('pre_iop_time')"
                                        class="shrink-0 px-2.5 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700" style="padding: 0.375rem;">
                                        Now
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Post IOP --}}
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Post IOP</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="post_iop_method" value="Method" />
                                <select id="post_iop_method" name="post_iop_method"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">— Select —</option>
                                    @foreach(\App\Enums\IopMethod::options() as $value => $label)
                                        <option value="{{ $value }}" @selected(old('post_iop_method', $inv?->post_iop_method?->value) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="post_iop_r" value="Post IOP R" />
                                <x-text-input id="post_iop_r" name="post_iop_r" type="text" class="mt-1 block w-full"
                                    value="{{ old('post_iop_r', $inv?->post_iop_r) }}" />
                            </div>
                            <div>
                                <x-input-label for="post_iop_l" value="Post IOP L" />
                                <x-text-input id="post_iop_l" name="post_iop_l" type="text" class="mt-1 block w-full"
                                    value="{{ old('post_iop_l', $inv?->post_iop_l) }}" />
                            </div>
                            <div>
                                <x-input-label for="post_iop_time" value="Time" />
                                <div class="mt-1 flex items-center gap-2">
                                    <x-text-input id="post_iop_time" name="post_iop_time" type="time" class="block w-full"
                                        placeholder="HH:MM" x-ref="post_iop_time"
                                        value="{{ old('post_iop_time', $inv?->post_iop_time) }}" />
                                    <button type="button" @click="setNow('post_iop_time')"
                                        class="shrink-0 px-2.5 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700" style="padding: 0.375rem;">
                                        Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $ctOptions = [
                            '' => '— Select —',
                            'Orthophoria' => 'Orthophoria',
                            'Esophoria' => 'Esophoria',
                            'Exophoria' => 'Exophoria',
                            'Hyperphoria' => 'Hyperphoria',
                            'Hypophoria' => 'Hypophoria',
                            'Esotropia' => 'Esotropia',
                            'Exotropia' => 'Exotropia',
                            'Hypertropia' => 'Hypertropia',
                            'Hypotropia' => 'Hypotropia',
                            'Not performed' => 'Not performed',
                            'Unable to perform' => 'Unable to perform',
                            'Latent' => 'Latent',
                            'Manifest' => 'Manifest',
                            'Alternating' => 'Alternating',
                            'Normal' => 'Normal',
                        ];
                        $ombOptions = ['' => '— Select —', 'No slip' => 'No slip'];
                        $vfOptions  = ['' => '— Select —', 'Full' => 'Full', 'Not possible' => 'Not possible', 'See plot' => 'See plot', 'Normal' => 'Normal', 'Abnormal' => 'Abnormal'];
                        $motOptions = ['' => '— Select —', 'Full & smooth' => 'Full & smooth'];
                        $amslerOptions = ['' => '— Select —', 'Normal' => 'Normal', 'Abnormal' => 'Abnormal', 'Not performed' => 'Not performed'];
                        $cvOptions  = ['' => '— Select —', 'All seen' => 'All seen', 'Normal' => 'Normal', 'Mild deutan' => 'Mild deutan', 'Mild protan' => 'Mild protan', 'Severe deutan' => 'Severe deutan', 'Severe protan' => 'Severe protan'];
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Cover Test</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach([
                                'ct_with_rx'      => 'CT With Rx (Distance)',
                                'ct_with_rx_near' => 'CT With Rx (Near)',
                                'ct_without_rx'   => 'CT Without Rx (Distance)',
                                'ct_without_rx_near' => 'CT Without Rx (Near)',
                            ] as $field => $label)
                                <div>
                                    <x-input-label :for="$field" :value="$label" />
                                    <select id="{{ $field }}" name="{{ $field }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @foreach($ctOptions as $val => $opt)
                                            <option value="{{ $val }}" @selected(old($field, $inv?->$field) === $val)>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Further Tests</h3>

                        {{-- OMB --}}
                        <div class="mb-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">OMB</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach(['omb_h' => 'OMB H', 'omb_v' => 'OMB V', 'omb_near_h' => 'OMB Near H', 'omb_near_v' => 'OMB Near V'] as $field => $label)
                                    <div>
                                        <x-input-label :for="$field" :value="$label" />
                                        <select id="{{ $field }}" name="{{ $field }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            @foreach($ombOptions as $val => $opt)
                                                <option value="{{ $val }}" @selected(old($field, $inv?->$field) === $val)>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Keratometry --}}
                        <div class="mb-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Keratometry</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach(['keratometry_r' => 'Keratometry R', 'keratometry_l' => 'Keratometry L'] as $field => $label)
                                    <div>
                                        <x-input-label :for="$field" :value="$label" />
                                        <x-text-input :id="$field" :name="$field" type="text" class="mt-1 block w-full"
                                            :value="old($field, $inv?->$field)" />
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Visual Fields --}}
                        <div class="mb-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Visual Fields</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach(['visual_fields_r' => 'Visual Fields R', 'visual_fields_l' => 'Visual Fields L'] as $field => $label)
                                    <div>
                                        <x-input-label :for="$field" :value="$label" />
                                        <select id="{{ $field }}" name="{{ $field }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            @foreach($vfOptions as $val => $opt)
                                                <option value="{{ $val }}" @selected(old($field, $inv?->$field) === $val)>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- NPC, Motility, Stereopsis --}}
                        <div class="mb-4">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <x-input-label for="npc" value="NPC" />
                                    <x-text-input id="npc" name="npc" type="text" class="mt-1 block w-full"
                                        :value="old('npc', $inv?->npc)" />
                                </div>
                                <div>
                                    <x-input-label for="motility" value="Motility" />
                                    <select id="motility" name="motility"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @foreach($motOptions as $val => $opt)
                                            <option value="{{ $val }}" @selected(old('motility', $inv?->motility) === $val)>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="stereopsis" value="Stereopsis" />
                                    <x-text-input id="stereopsis" name="stereopsis" type="text" class="mt-1 block w-full"
                                        :value="old('stereopsis', $inv?->stereopsis)" />
                                </div>
                            </div>
                        </div>

                        {{-- Amsler --}}
                        <div class="mb-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Amsler</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <x-input-label for="amsler_r" value="Amsler R" />
                                    <select id="amsler_r" name="amsler_r"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @foreach($amslerOptions as $val => $opt)
                                            <option value="{{ $val }}" @selected(old('amsler_r', $inv?->amsler_r) === $val)>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="amsler_r_notes" value="Amsler R Notes" />
                                    <x-text-input id="amsler_r_notes" name="amsler_r_notes" type="text" class="mt-1 block w-full"
                                        :value="old('amsler_r_notes', $inv?->amsler_r_notes)" />
                                </div>
                                <div>
                                    <x-input-label for="amsler_l" value="Amsler L" />
                                    <select id="amsler_l" name="amsler_l"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @foreach($amslerOptions as $val => $opt)
                                            <option value="{{ $val }}" @selected(old('amsler_l', $inv?->amsler_l) === $val)>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="amsler_l_notes" value="Amsler L Notes" />
                                    <x-text-input id="amsler_l_notes" name="amsler_l_notes" type="text" class="mt-1 block w-full"
                                        :value="old('amsler_l_notes', $inv?->amsler_l_notes)" />
                                </div>
                            </div>
                        </div>

                        {{-- Colour Vision --}}
                        <div class="mb-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Colour Vision</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <x-input-label for="colour_vision" value="Result" />
                                    <select id="colour_vision" name="colour_vision"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @foreach($cvOptions as $val => $opt)
                                            <option value="{{ $val }}" @selected(old('colour_vision', $inv?->colour_vision) === $val)>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <x-input-label for="colour_vision_notes" value="Notes" />
                                    <x-text-input id="colour_vision_notes" name="colour_vision_notes" type="text" class="mt-1 block w-full"
                                        :value="old('colour_vision_notes', $inv?->colour_vision_notes)" />
                                </div>
                            </div>
                        </div>

                        {{-- Amplitude of Accommodation --}}
                        <div>
                            <x-input-label for="amplitude_of_accommodation" value="Amplitude of Accommodation" />
                            <x-text-input id="amplitude_of_accommodation" name="amplitude_of_accommodation" type="text" class="mt-1 block w-48"
                                :value="old('amplitude_of_accommodation', $inv?->amplitude_of_accommodation)" />
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
            <div id="tab-refraction" x-show="tab === 'refraction'" x-cloak>
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
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
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
                                            @php
                                                $cpName = "{$p}_{$eye}_prism";
                                                $cdName = "{$p}_{$eye}_prism_dir";
                                                $cpRaw  = old($cpName, (string)($rx?->$cpName ?? ''));
                                                $cdRaw  = old($cdName, (string)($rx?->$cdName ?? ''));
                                                [$cpS, $cpV, $cpH] = str_contains($cpRaw, '/') ? ['', ...explode('/', $cpRaw, 2)] : [$cpRaw, '', ''];
                                            @endphp
                                            <tr x-data="{
                                                diag: ['up_in','up_out','down_in','down_out'],
                                                isDiag(d) { return this.diag.includes(d); },
                                                dir: '{{ $cdRaw }}', single: '{{ $cpS }}', pv: '{{ $cpV }}', ph: '{{ $cpH }}',
                                                get combined() { return this.isDiag(this.dir) ? (this.pv && this.ph ? this.pv+'/'+this.ph : '') : this.single; }
                                            }">
                                                <td class="border border-gray-200 px-2 py-1 font-bold text-gray-600">{{ $eyeLabel }}</td>
                                                @foreach(['sph' => 'number', 'cyl' => 'number', 'axis' => 'number'] as $field => $type)
                                                    @php $name = "{$p}_{$eye}_{$field}"; @endphp
                                                    <td class="border border-gray-200 p-0.5">
                                                        <input type="number" name="{{ $name }}" step="{{ $field === 'axis' ? '1' : '0.25' }}"
                                                            value="{{ old($name, $rx?->$name) }}"
                                                            class="w-full px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                    </td>
                                                @endforeach
                                                <td class="border border-gray-200 p-0.5">
                                                    <input type="hidden" name="{{ $cpName }}" :value="combined">
                                                    <input type="number" x-show="!isDiag(dir)" x-model="single" step="0.25"
                                                        class="w-full px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                    <span x-show="isDiag(dir)" class="inline-flex gap-0.5 items-center">
                                                        <input type="number" x-model="pv" step="0.25" placeholder="V"
                                                            class="w-9 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                        <span class="text-gray-400 text-xs">/</span>
                                                        <input type="number" x-model="ph" step="0.25" placeholder="H"
                                                            class="w-9 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                    </span>
                                                </td>
                                                <td class="border border-gray-200 p-0.5">
                                                    <select x-model="dir" name="{{ $cdName }}"
                                                        class="w-full px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded bg-transparent">
                                                        <option value=""></option>
                                                        @foreach($prismDirections as $v => $l)
                                                            <option value="{{ $v }}">{{ $l }}</option>
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
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('current_comments', $rx?->current_comments) }}</textarea>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    {{-- Retinoscopy --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
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
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
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
                                        @php
                                            $subjDistPrism = "subj_{$eye}_prism";
                                            $subjDistDir   = "subj_{$eye}_prism_dir";
                                            $subjNearAdd   = "subj_{$eye}_near_add";
                                            $subjNearPrism = "subj_{$eye}_near_prism";
                                            $subjNearDir   = "subj_{$eye}_near_prism_dir";
                                            $subjIntAdd    = "subj_{$eye}_int_add";
                                            $subjIntPrism  = "subj_{$eye}_int_prism";
                                            $subjIntDir    = "subj_{$eye}_int_prism_dir";
                                            $dpRaw = old($subjDistPrism, (string)($rx?->$subjDistPrism ?? ''));
                                            $ddRaw = old($subjDistDir,   (string)($rx?->$subjDistDir   ?? ''));
                                            $npRaw = old($subjNearPrism, (string)($rx?->$subjNearPrism ?? ''));
                                            $ndRaw = old($subjNearDir,   (string)($rx?->$subjNearDir   ?? ''));
                                            $ipRaw = old($subjIntPrism,  (string)($rx?->$subjIntPrism  ?? ''));
                                            $idRaw = old($subjIntDir,    (string)($rx?->$subjIntDir    ?? ''));
                                            [$dpS, $dpV, $dpH] = str_contains($dpRaw, '/') ? ['', ...explode('/', $dpRaw, 2)] : [$dpRaw, '', ''];
                                            [$npS, $npV, $npH] = str_contains($npRaw, '/') ? ['', ...explode('/', $npRaw, 2)] : [$npRaw, '', ''];
                                            [$ipS, $ipV, $ipH] = str_contains($ipRaw, '/') ? ['', ...explode('/', $ipRaw, 2)] : [$ipRaw, '', ''];
                                        @endphp
                                        <tr x-data="{
                                            diag: ['up_in','up_out','down_in','down_out'],
                                            isDiag(d) { return this.diag.includes(d); },
                                            distDir: '{{ $ddRaw }}', distS: '{{ $dpS }}', distV: '{{ $dpV }}', distH: '{{ $dpH }}',
                                            nearDir: '{{ $ndRaw }}', nearS: '{{ $npS }}', nearV: '{{ $npV }}', nearH: '{{ $npH }}',
                                            intDir:  '{{ $idRaw }}', intS:  '{{ $ipS }}', intV:  '{{ $ipV }}', intH:  '{{ $ipH }}',
                                            comb(s, v, h, d) { return this.isDiag(d) ? (v && h ? v+'/'+h : '') : s; }
                                        }">
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
                                            {{-- Distance Prism --}}
                                            <td class="border border-gray-200 p-0.5">
                                                <input type="hidden" name="{{ $subjDistPrism }}" :value="comb(distS, distV, distH, distDir)">
                                                <input type="number" x-show="!isDiag(distDir)" x-model="distS" step="0.25"
                                                    class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                <span x-show="isDiag(distDir)" class="inline-flex gap-0.5 items-center">
                                                    <input type="number" x-model="distV" step="0.25" placeholder="V"
                                                        class="w-9 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                    <span class="text-gray-400 text-xs">/</span>
                                                    <input type="number" x-model="distH" step="0.25" placeholder="H"
                                                        class="w-9 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                </span>
                                            </td>
                                            {{-- Distance Dir --}}
                                            <td class="border border-gray-200 p-0.5">
                                                <select x-model="distDir" name="{{ $subjDistDir }}"
                                                    class="w-20 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded bg-transparent">
                                                    <option value=""></option>
                                                    @foreach($prismDirections as $v => $l)
                                                        <option value="{{ $v }}">{{ $l }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            @php $vaName = "subj_{$eye}_va"; @endphp
                                            <td class="border border-gray-200 p-0.5">
                                                <input type="text" name="{{ $vaName }}" value="{{ old($vaName, $rx?->$vaName) }}"
                                                    class="w-16 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                            </td>
                                            {{-- Near Add --}}
                                            <td class="border border-gray-200 p-0.5">
                                                <input type="number" name="{{ $subjNearAdd }}" step="0.25"
                                                    value="{{ old($subjNearAdd, $rx?->$subjNearAdd) }}"
                                                    class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                            </td>
                                            {{-- Near Prism --}}
                                            <td class="border border-gray-200 p-0.5">
                                                <input type="hidden" name="{{ $subjNearPrism }}" :value="comb(nearS, nearV, nearH, nearDir)">
                                                <input type="number" x-show="!isDiag(nearDir)" x-model="nearS" step="0.25"
                                                    class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                <span x-show="isDiag(nearDir)" class="inline-flex gap-0.5 items-center">
                                                    <input type="number" x-model="nearV" step="0.25" placeholder="V"
                                                        class="w-9 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                    <span class="text-gray-400 text-xs">/</span>
                                                    <input type="number" x-model="nearH" step="0.25" placeholder="H"
                                                        class="w-9 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                </span>
                                            </td>
                                            {{-- Near Dir --}}
                                            <td class="border border-gray-200 p-0.5">
                                                <select x-model="nearDir" name="{{ $subjNearDir }}"
                                                    class="w-20 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded bg-transparent">
                                                    <option value=""></option>
                                                    @foreach($prismDirections as $v => $l)
                                                        <option value="{{ $v }}">{{ $l }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            @php $nearAcuityName = "subj_{$eye}_near_acuity"; @endphp
                                            <td class="border border-gray-200 p-0.5">
                                                <select name="{{ $nearAcuityName }}"
                                                    class="w-20 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded bg-transparent">
                                                    @foreach(['' => '', 'N.4' => 'N.4', 'N.5' => 'N.5', 'N.6' => 'N.6', 'N.8' => 'N.8', 'N.10' => 'N.10', 'N.12' => 'N.12', 'N.14' => 'N.14', 'N.18' => 'N.18', 'N.24' => 'N.24', 'N.30' => 'N.30', 'N.48' => 'N.48', 'N.64' => 'N.64', 'N.80' => 'N.80'] as $val => $opt)
                                                        <option value="{{ $val }}" @selected(old($nearAcuityName, $rx?->$nearAcuityName) === $val)>{{ $opt }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            {{-- Int Add --}}
                                            <td class="border border-gray-200 p-0.5">
                                                <input type="number" name="{{ $subjIntAdd }}" step="0.25"
                                                    value="{{ old($subjIntAdd, $rx?->$subjIntAdd) }}"
                                                    class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                            </td>
                                            {{-- Int Prism --}}
                                            <td class="border border-gray-200 p-0.5">
                                                <input type="hidden" name="{{ $subjIntPrism }}" :value="comb(intS, intV, intH, intDir)">
                                                <input type="number" x-show="!isDiag(intDir)" x-model="intS" step="0.25"
                                                    class="w-14 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                <span x-show="isDiag(intDir)" class="inline-flex gap-0.5 items-center">
                                                    <input type="number" x-model="intV" step="0.25" placeholder="V"
                                                        class="w-9 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                    <span class="text-gray-400 text-xs">/</span>
                                                    <input type="number" x-model="intH" step="0.25" placeholder="H"
                                                        class="w-9 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded">
                                                </span>
                                            </td>
                                            {{-- Int Dir --}}
                                            <td class="border border-gray-200 p-0.5">
                                                <select x-model="intDir" name="{{ $subjIntDir }}"
                                                    class="w-20 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded bg-transparent">
                                                    <option value=""></option>
                                                    @foreach($prismDirections as $v => $l)
                                                        <option value="{{ $v }}">{{ $l }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            @php $intAcuityName = "subj_{$eye}_int_acuity"; @endphp
                                            <td class="border border-gray-200 p-0.5">
                                                <select name="{{ $intAcuityName }}"
                                                    class="w-20 px-1 py-0.5 text-xs border-0 focus:ring-1 focus:ring-indigo-500 rounded bg-transparent">
                                                    @foreach(['' => '', 'N.4' => 'N.4', 'N.5' => 'N.5', 'N.6' => 'N.6', 'N.8' => 'N.8', 'N.10' => 'N.10', 'N.12' => 'N.12', 'N.14' => 'N.14', 'N.18' => 'N.18', 'N.24' => 'N.24', 'N.30' => 'N.30', 'N.48' => 'N.48', 'N.64' => 'N.64', 'N.80' => 'N.80'] as $val => $opt)
                                                        <option value="{{ $val }}" @selected(old($intAcuityName, $rx?->$intAcuityName) === $val)>{{ $opt }}</option>
                                                    @endforeach
                                                </select>
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
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('subj_notes', $rx?->subj_notes) }}</textarea>
                        </div>
                    </div>

                    {{-- Outcome & Recommendations --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Outcome &amp; Recommendations</h3>
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
                                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="hidden" name="{{ $field }}" value="0">
                                    <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $rx?->$field))
                                        class="rounded border-gray-300 text-indigo-600">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- NHS & Retest --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">NHS &amp; Retest</h3>
                        @php
                            $svVouchers  = array_filter(\App\Enums\NhsVoucherType::options(), fn($v) => in_array($v, ['A','B','C','D']), ARRAY_FILTER_USE_KEY);
                            $bifVouchers = array_filter(\App\Enums\NhsVoucherType::options(), fn($v) => in_array($v, ['E','F','G','H']), ARRAY_FILTER_USE_KEY);
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label for="nhs_voucher_dist" value="NHS Voucher (Distance)" />
                                <select id="nhs_voucher_dist" name="nhs_voucher_dist"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">— None —</option>
                                    @foreach($svVouchers as $value => $label)
                                        <option value="{{ $value }}" @selected(old('nhs_voucher_dist', $rx?->nhs_voucher_dist?->value) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="nhs_voucher_near" value="NHS Voucher (Near)" />
                                <select id="nhs_voucher_near" name="nhs_voucher_near"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">— None —</option>
                                    @foreach($svVouchers as $value => $label)
                                        <option value="{{ $value }}" @selected(old('nhs_voucher_near', $rx?->nhs_voucher_near?->value) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="nhs_voucher_bifocal" value="NHS Voucher (Bifocal / Varifocal)" />
                                <select id="nhs_voucher_bifocal" name="nhs_voucher_bifocal"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">— None —</option>
                                    @foreach($bifVouchers as $value => $label)
                                        <option value="{{ $value }}" @selected(old('nhs_voucher_bifocal', $rx?->nhs_voucher_bifocal?->value) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="retest_after" value="Retest After" />
                                <x-text-input id="retest_after" name="retest_after" type="text" class="mt-1 block w-full"
                                    placeholder="e.g. 2 years" value="{{ old('retest_after', $rx?->retest_after) }}" />
                            </div>
                            <div>
                                <x-input-label for="retest_patient_type" value="Retest Patient Type" />
                                <select id="retest_patient_type" name="retest_patient_type"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
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
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('examination_comment', $rx?->examination_comment) }}</textarea>
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
