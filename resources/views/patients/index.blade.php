<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Find Patient</h2>
            <a href="{{ route('patients.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-indigo-700">
                + New Patient
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Search form --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-6">
                <form method="GET" action="{{ route('patients.index') }}">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
                            <input type="text" name="first_name" value="{{ request('first_name') }}"
                                class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Surname</label>
                            <input type="text" name="surname" value="{{ request('surname') }}"
                                class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Patient ID</label>
                            <input type="number" name="patient_id" value="{{ request('patient_id') }}"
                                class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ request('date_of_birth') }}"
                                class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Post Code</label>
                            <input type="text" name="post_code" value="{{ request('post_code') }}"
                                class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sex / Gender</label>
                            <select name="sex_gender" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">— Any —</option>
                                @foreach($sexGenders as $value => $label)
                                    <option value="{{ $value }}" @selected(request('sex_gender') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Patient Type</label>
                            <select name="patient_type" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">— Any —</option>
                                @foreach($patientTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(request('patient_type') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-3">
                            <label class="flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300 pb-1">
                                <input type="checkbox" name="has_glaucoma" value="1" @checked(request('has_glaucoma'))
                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600">
                                Glaucoma
                            </label>
                            <label class="flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300 pb-1">
                                <input type="checkbox" name="is_diabetic" value="1" @checked(request('is_diabetic'))
                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600">
                                Diabetic
                            </label>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Search</button>
                        <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">Clear</a>
                        <div class="sm:ml-auto flex items-center gap-2">
                            <label class="text-xs font-medium text-gray-600 dark:text-gray-400 whitespace-nowrap">Sort by</label>
                            <select name="sort" onchange="this.form.submit()"
                                class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="id_desc"    @selected(request('sort', 'id_desc') === 'id_desc')>Latest</option>
                                <option value="id_asc"     @selected(request('sort') === 'id_asc')>Oldest</option>
                                <option value="surname_asc"  @selected(request('sort') === 'surname_asc')>Surname A–Z</option>
                                <option value="surname_desc" @selected(request('sort') === 'surname_desc')>Surname Z–A</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Results --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                @if($results->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-center py-10">No patients found matching your search.</p>
                @else
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-400">
                        {{ $results->total() }} {{ Str::plural('patient', $results->total()) }} found
                    </div>
                        <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[600px]">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-400">ID</th>
                                    <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-400">Name</th>
                                    <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-400">DOB</th>
                                    <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-400">Post Code</th>
                                    <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-400">Type</th>
                                    <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-400">Flags</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($results as $patient)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-4 py-2 text-gray-500 dark:text-gray-400 font-mono">{{ $patient->id }}</td>
                                        <td class="px-4 py-2 font-medium">
                                            <a href="{{ route('patients.show', $patient) }}" class="text-indigo-600 hover:underline">
                                                {{ $patient->title }} {{ $patient->first_name }} {{ $patient->surname }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $patient->date_of_birth->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $patient->post_code }}</td>
                                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $patient->patient_type?->label() }}</td>
                                        <td class="px-4 py-2">
                                            @if($patient->has_glaucoma)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mr-1">G</span>
                                            @endif
                                            @if($patient->is_diabetic)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">D</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('patients.show', $patient) }}" class="text-indigo-600 hover:text-indigo-800 text-xs">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>{{-- end overflow-x-auto --}}
                        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                            {{ $results->withQueryString()->links() }}
                        </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
