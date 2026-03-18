<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('diary.index', ['date' => $appointment->date->toDateString()]) }}" class="text-gray-400 hover:text-gray-600">&larr;</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Appointment</h2>
            @if($appointment->cancelled_at)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Cancelled</span>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                <div class="mb-4 p-3 bg-gray-50 rounded-md text-sm">
                    <span class="font-medium text-gray-700">Patient:</span>
                    @if($appointment->patient)
                        <a href="{{ route('patients.show', $appointment->patient) }}" class="text-indigo-600 hover:underline ml-1">
                            {{ $appointment->patient->title }} {{ $appointment->patient->first_name }} {{ $appointment->patient->surname }}
                        </a>
                    @else
                        <span class="text-gray-400 ml-1">Unknown</span>
                    @endif
                </div>

                <form method="POST" action="{{ route('appointments.update', $appointment) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="diary_id" value="Diary *" />
                            <select id="diary_id" name="diary_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                @foreach($diaries as $id => $name)
                                    <option value="{{ $id }}" @selected(old('diary_id', $appointment->diary_id) == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('diary_id')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="date" value="Date *" />
                            <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" required
                                value="{{ old('date', $appointment->date->toDateString()) }}" />
                            <x-input-error :messages="$errors->get('date')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="start_time" value="Start Time *" />
                            <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full" required step="300"
                                value="{{ old('start_time', \Carbon\Carbon::parse($appointment->start_time)->format('H:i')) }}" />
                            <x-input-error :messages="$errors->get('start_time')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="length_minutes" value="Length (minutes) *" />
                            <x-text-input id="length_minutes" name="length_minutes" type="number" class="mt-1 block w-full" required min="1"
                                value="{{ old('length_minutes', $appointment->length_minutes) }}" />
                            <x-input-error :messages="$errors->get('length_minutes')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="appointment_type" value="Type *" />
                            <select id="appointment_type" name="appointment_type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                @foreach($appointmentTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(old('appointment_type', $appointment->appointment_type->value) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('appointment_type')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="appointment_status" value="Status *" />
                            <select id="appointment_status" name="appointment_status" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                @foreach($appointmentStatuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('appointment_status', $appointment->appointment_status->value) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('appointment_status')" class="mt-1" />
                        </div>
                        <div class="col-span-2">
                            <x-input-label for="display_text" value="Display Text" />
                            <x-text-input id="display_text" name="display_text" type="text" class="mt-1 block w-full"
                                value="{{ old('display_text', $appointment->display_text) }}"
                                placeholder="Optional note shown on calendar" />
                            <x-input-error :messages="$errors->get('display_text')" class="mt-1" />
                        </div>
                    </div>

                    @if(!$appointment->cancelled_at)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <label class="flex items-center gap-2 text-sm text-red-600">
                                <input type="hidden" name="cancel" value="0">
                                <input type="checkbox" name="cancel" value="1" class="rounded border-gray-300 text-red-600">
                                Cancel this appointment (stamps cancellation timestamp)
                            </label>
                        </div>
                    @endif

                    <div class="mt-5 flex justify-between items-center">
                        <a href="{{ route('diary.index', ['date' => $appointment->date->toDateString()]) }}"
                           class="px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-300 rounded-md hover:bg-gray-50">
                            Back to Diary
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
