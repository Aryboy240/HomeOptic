{{--
    Shared patient form fields.
    Variables: $patient (optional, for edit), $practices, $doctors, $pcts,
               $sexGenders, $patientTypes, $droppedReasons, $howHeardOptions, $domiciliaryReasons
--}}
@php $patient = $patient ?? null; @endphp

{{-- Personal details --}}
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-5">
    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-4">Personal Details</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <x-input-label for="title" value="Title *" />
            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required
                value="{{ old('title', $patient?->title) }}" maxlength="20" />
            <x-input-error :messages="$errors->get('title')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="first_name" value="First Name *" />
            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" required
                value="{{ old('first_name', $patient?->first_name) }}" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
        </div>
        <div class="col-span-2">
            <x-input-label for="surname" value="Surname *" />
            <x-text-input id="surname" name="surname" type="text" class="mt-1 block w-full" required
                value="{{ old('surname', $patient?->surname) }}" />
            <x-input-error :messages="$errors->get('surname')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="date_of_birth" value="Date of Birth *" />
            <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" required
                value="{{ old('date_of_birth', $patient?->date_of_birth?->toDateString()) }}" />
            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="sex_gender" value="Sex / Gender *" />
            <select id="sex_gender" name="sex_gender" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">— Select —</option>
                @foreach($sexGenders as $value => $label)
                    <option value="{{ $value }}" @selected(old('sex_gender', $patient?->sex_gender?->value) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('sex_gender')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                value="{{ old('email', $patient?->email) }}" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="telephone_mobile" value="Mobile" />
            <x-text-input id="telephone_mobile" name="telephone_mobile" type="text" class="mt-1 block w-full"
                value="{{ old('telephone_mobile', $patient?->telephone_mobile) }}" maxlength="20" />
            <x-input-error :messages="$errors->get('telephone_mobile')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="telephone_other" value="Other Phone" />
            <x-text-input id="telephone_other" name="telephone_other" type="text" class="mt-1 block w-full"
                value="{{ old('telephone_other', $patient?->telephone_other) }}" maxlength="20" />
            <x-input-error :messages="$errors->get('telephone_other')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="alt_contact_name" value="Alt. Contact Name" />
            <x-text-input id="alt_contact_name" name="alt_contact_name" type="text" class="mt-1 block w-full"
                value="{{ old('alt_contact_name', $patient?->alt_contact_name) }}" />
            <x-input-error :messages="$errors->get('alt_contact_name')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="alt_tel_number" value="Alt. Contact Phone" />
            <x-text-input id="alt_tel_number" name="alt_tel_number" type="text" class="mt-1 block w-full"
                value="{{ old('alt_tel_number', $patient?->alt_tel_number) }}" maxlength="20" />
            <x-input-error :messages="$errors->get('alt_tel_number')" class="mt-1" />
        </div>
    </div>
</div>

{{-- Address --}}
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-5">
    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-4">Address</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="col-span-2 md:col-span-3">
            <x-input-label for="address_line_1" value="Address Line 1 *" />
            <x-text-input id="address_line_1" name="address_line_1" type="text" class="mt-1 block w-full" required
                value="{{ old('address_line_1', $patient?->address_line_1) }}" />
            <x-input-error :messages="$errors->get('address_line_1')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="town_city" value="Town / City *" />
            <x-text-input id="town_city" name="town_city" type="text" class="mt-1 block w-full" required
                value="{{ old('town_city', $patient?->town_city) }}" />
            <x-input-error :messages="$errors->get('town_city')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="county" value="County" />
            <x-text-input id="county" name="county" type="text" class="mt-1 block w-full"
                value="{{ old('county', $patient?->county) }}" />
            <x-input-error :messages="$errors->get('county')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="post_code" value="Post Code *" />
            <x-text-input id="post_code" name="post_code" type="text" class="mt-1 block w-full" required
                value="{{ old('post_code', $patient?->post_code) }}" maxlength="10" />
            <x-input-error :messages="$errors->get('post_code')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="country" value="Country" />
            <x-text-input id="country" name="country" type="text" class="mt-1 block w-full"
                value="{{ old('country', $patient?->country) }}" />
            <x-input-error :messages="$errors->get('country')" class="mt-1" />
        </div>
    </div>
</div>

{{-- Clinical & Admin --}}
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 mb-5">
    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-4">Clinical &amp; Admin</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <x-input-label for="patient_type" value="Patient Type *" />
            <select id="patient_type" name="patient_type" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">— Select —</option>
                @foreach($patientTypes as $value => $label)
                    <option value="{{ $value }}" @selected(old('patient_type', $patient?->patient_type?->value) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('patient_type')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="practice_id" value="Practice" />
            <select id="practice_id" name="practice_id"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">— None —</option>
                @foreach($practices as $id => $name)
                    <option value="{{ $id }}" @selected(old('practice_id', $patient?->practice_id) == $id)>{{ $name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('practice_id')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="doctor_id" value="GP / Doctor" />
            <select id="doctor_id" name="doctor_id"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">— None —</option>
                @foreach($doctors as $id => $name)
                    <option value="{{ $id }}" @selected(old('doctor_id', $patient?->doctor_id) == $id)>{{ $name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('doctor_id')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="doctor_other" value="GP (if not listed)" />
            <x-text-input id="doctor_other" name="doctor_other" type="text" class="mt-1 block w-full"
                value="{{ old('doctor_other', $patient?->doctor_other) }}" />
            <x-input-error :messages="$errors->get('doctor_other')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="pct_id" value="PCT" />
            <select id="pct_id" name="pct_id"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">— None —</option>
                @foreach($pcts as $id => $name)
                    <option value="{{ $id }}" @selected(old('pct_id', $patient?->pct_id) == $id)>{{ $name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('pct_id')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="how_heard" value="How Heard Of Us" />
            <select id="how_heard" name="how_heard"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">— Select —</option>
                @foreach($howHeardOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('how_heard', $patient?->how_heard?->value) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('how_heard')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="how_heard_other" value="How Heard (other)" />
            <x-text-input id="how_heard_other" name="how_heard_other" type="text" class="mt-1 block w-full"
                value="{{ old('how_heard_other', $patient?->how_heard_other) }}" />
            <x-input-error :messages="$errors->get('how_heard_other')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="dropped_reason" value="Dropped Reason" />
            <select id="dropped_reason" name="dropped_reason"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">— None —</option>
                @foreach($droppedReasons as $value => $label)
                    <option value="{{ $value }}" @selected(old('dropped_reason', $patient?->dropped_reason?->value) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('dropped_reason')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="domiciliary_reason" value="Domiciliary Reason" />
            <select id="domiciliary_reason" name="domiciliary_reason"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">— None —</option>
                @foreach($domiciliaryReasons as $value => $label)
                    <option value="{{ $value }}" @selected(old('domiciliary_reason', $patient?->domiciliary_reason?->value) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('domiciliary_reason')" class="mt-1" />
        </div>
    </div>

    {{-- Flags --}}
    <div class="mt-4 flex flex-wrap gap-6">
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="has_glaucoma" value="0">
            <input type="checkbox" name="has_glaucoma" value="1" @checked(old('has_glaucoma', $patient?->has_glaucoma))
                class="rounded border-gray-300 text-indigo-600">
            Has Glaucoma
        </label>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_diabetic" value="0">
            <input type="checkbox" name="is_diabetic" value="1" @checked(old('is_diabetic', $patient?->is_diabetic))
                class="rounded border-gray-300 text-indigo-600">
            Is Diabetic
        </label>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_nhs" value="0">
            <input type="checkbox" name="is_nhs" value="1" @checked(old('is_nhs', $patient?->is_nhs))
                class="rounded border-gray-300 text-indigo-600">
            NHS Patient
        </label>
    </div>

    {{-- Notes --}}
    <div class="mt-4">
        <x-input-label for="notes" value="Notes" />
        <textarea id="notes" name="notes" rows="3"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('notes', $patient?->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-1" />
    </div>
</div>
