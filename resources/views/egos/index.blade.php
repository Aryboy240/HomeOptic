<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">eGOS Claims</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-md text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filter bar --}}
            <form method="GET" action="{{ route('egos.index') }}" class="mb-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-4">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Form Type</label>
                        <select name="form_type" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Types</option>
                            @foreach($formTypes as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['form_type'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Patient</label>
                        <input type="text" name="patient" value="{{ $filters['patient'] ?? '' }}"
                               placeholder="Name or ID"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="mt-3 flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                        Search
                    </button>
                    <a href="{{ route('egos.index') }}"
                       class="px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                        Clear
                    </a>
                </div>
            </form>

            {{-- Batch action area + table --}}
            <div x-data="{
                selected: [],
                toggle(id) {
                    const idx = this.selected.indexOf(id);
                    idx === -1 ? this.selected.push(id) : this.selected.splice(idx, 1);
                },
                toggleAll(ids) {
                    this.selected = this.selected.length === ids.length ? [] : [...ids];
                },
                isSelected(id) { return this.selected.includes(id); }
            }">

                {{-- Batch action bar --}}
                <div x-show="selected.length > 0" x-cloak
                     class="mb-3 p-3 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-lg flex items-center gap-4">
                    <span class="text-sm font-medium text-indigo-800 dark:text-indigo-300" x-text="selected.length + ' submission(s) selected'"></span>

                    <form method="POST" action="{{ route('egos.batch-submit') }}" class="inline"
                          @submit="$el.querySelectorAll('input[name=\'ids[]\']').forEach(el => el.remove());
                                   selected.forEach(id => { const inp = document.createElement('input'); inp.type='hidden'; inp.name='ids[]'; inp.value=id; $el.appendChild(inp); })">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700">
                            Batch Submit
                        </button>
                    </form>

                    <form method="POST" action="{{ route('egos.batch-paid') }}" class="inline"
                          @submit="$el.querySelectorAll('input[name=\'ids[]\']').forEach(el => el.remove());
                                   selected.forEach(id => { const inp = document.createElement('input'); inp.type='hidden'; inp.name='ids[]'; inp.value=id; $el.appendChild(inp); })">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700">
                            Batch Mark Paid
                        </button>
                    </form>
                </div>

                {{-- Results table --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

                    @php
                        $allIds = $submissions->pluck('id')->toArray();
                        $statusBadge = [
                            'unsubmitted'          => 'background:#f3f4f6; color:#374151;',
                            'awaiting_confirmation' => 'background:#dbeafe; color:#1e40af;',
                            'accepted'             => 'background:#dcfce7; color:#166534;',
                            'rejected'             => 'background:#fee2e2; color:#991b1b;',
                        ];
                    @endphp

                    @if($submissions->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-center py-12 text-sm">No submissions match the current filters.</p>
                    @else
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-4 py-2 text-left w-8">
                                        <input type="checkbox"
                                               @change="toggleAll({{ json_encode($allIds) }})"
                                               :checked="selected.length === {{ count($allIds) }} && {{ count($allIds) }} > 0"
                                               class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    </th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Patient</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Form</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Voucher Value</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Batch Ref</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Submitted</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Status</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($submissions as $sub)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50" :class="isSelected({{ $sub->id }}) ? 'bg-indigo-50/40 dark:bg-indigo-900/20' : ''">
                                        <td class="px-4 py-2">
                                            <input type="checkbox"
                                                   @change="toggle({{ $sub->id }})"
                                                   :checked="isSelected({{ $sub->id }})"
                                                   class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                        </td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('patients.show', $sub->patient) }}"
                                               class="font-medium text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400">
                                                {{ $sub->patient->first_name }} {{ $sub->patient->surname }}
                                            </a>
                                            <span class="ml-1 text-xs text-gray-400">#{{ $sub->patient_id }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-gray-700 dark:text-gray-300 font-medium">{{ $sub->form_type }}</td>
                                        <td class="px-4 py-2 text-gray-700 dark:text-gray-300">
                                            {{ $sub->voucher_value !== null ? '£' . number_format($sub->voucher_value, 2) : '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                            {{ $sub->batch_reference ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-500 dark:text-gray-400 text-xs">
                                            {{ $sub->submitted_at?->format('d/m/Y') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                  style="{{ $statusBadge[$sub->status->value] ?? '' }}">
                                                {{ $sub->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('patients.gos.form', [$sub->patient, $sub->form_type]) . '?from=egos' }}"
                                                   class="text-xs text-indigo-600 hover:text-indigo-800 hover:underline">
                                                    View Form
                                                </a>
                                                @if($sub->status === \App\Enums\GosSubmissionStatus::Unsubmitted)
                                                    <form method="POST"
                                                          action="{{ route('egos.destroy', $sub) }}"
                                                          onsubmit="return confirm('Are you sure you want to delete this submission? This cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="text-xs text-red-600 hover:text-red-800 hover:underline"
                                                                style="padding: 0 10px 0 10px;">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-gray-400 dark:text-gray-600 cursor-not-allowed" title="Cannot delete a submitted form">Delete</span>
                                                @endif
                                                <form method="POST"
                                                      action="{{ route('egos.status', $sub) }}"
                                                      x-data>
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status"
                                                            @change="$el.closest('form').submit()"
                                                            class="text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-0.5">
                                                        @foreach($statuses as $value => $label)
                                                            <option value="{{ $value }}"
                                                                    @selected($sub->status->value === $value)>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        @if($submissions->hasPages())
                            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                                {{ $submissions->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
