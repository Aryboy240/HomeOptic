<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('patients.show', $patient) }}" class="text-gray-400 hover:text-gray-600">&larr;</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Patient — {{ $patient->title }} {{ $patient->first_name }} {{ $patient->surname }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('patients.update', $patient) }}">
                @csrf
                @method('PUT')

                @include('patients._form')

                <div class="flex justify-end gap-3">
                    <a href="{{ route('patients.show', $patient) }}" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
