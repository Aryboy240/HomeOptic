<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Notifications</h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $notifications->count() }} {{ $status !== 'all' || $dateFrom || $dateTo ? 'filtered' : 'total' }}
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Filter bar --}}
            <form method="GET" action="{{ route('notifications.index') }}"
                  class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 mb-6 flex flex-wrap items-end gap-4">

                {{-- Status tabs --}}
                <div class="flex items-center gap-1 flex-wrap">
                    @foreach(['all' => 'All', 'pending' => 'Awaiting', 'approved' => 'Approved', 'declined' => 'Declined'] as $value => $label)
                        @php
                            $tabUrl = route('notifications.index', array_filter([
                                'status'    => $value !== 'all' ? $value : null,
                                'date_from' => $dateFrom,
                                'date_to'   => $dateTo,
                                'sort'      => $sort !== 'desc' ? $sort : null,
                            ]));
                        @endphp
                        <a href="{{ $tabUrl }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors
                               {{ $status === $value
                                   ? 'bg-blue-600 text-white'
                                   : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <div class="flex-1 min-w-0"></div>

                {{-- Date range --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <label class="text-xs text-gray-500 dark:text-gray-400 font-medium">Appt date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" onchange="this.form.submit()"
                           class="px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="text-xs text-gray-400">—</span>
                    <input type="date" name="date_to" value="{{ $dateTo }}" onchange="this.form.submit()"
                           class="px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Sort --}}
                <select style="width: 120px;" name="sort" onchange="this.form.submit()"
                        class="px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="desc" {{ $sort === 'desc' ? 'selected' : '' }}>Newest first</option>
                    <option value="asc"  {{ $sort === 'asc'  ? 'selected' : '' }}>Oldest first</option>
                </select>

                {{-- Carry status through date/sort form submissions --}}
                @if($status !== 'all')
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif

                @if($status !== 'all' || $dateFrom || $dateTo || $sort !== 'desc')
                    <a href="{{ route('notifications.index') }}"
                       class="px-3 py-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        Clear
                    </a>
                @endif
            </form>

            @if($notifications->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 text-center text-gray-400">
                    {{ ($status !== 'all' || $dateFrom || $dateTo) ? 'No notifications match the current filters.' : 'No notifications yet.' }}
                </div>
            @else
                <div class="space-y-4">
                    @foreach($notifications as $notification)
                        @php $booking = $notification->pendingBooking; $pfd = $booking?->patient_form_data ?? []; @endphp

                        <div x-data="{
                                status: '{{ $booking?->status ?? '' }}',
                                loading: false,
                                error: '',
                                decide(action) {
                                    if (!confirm('Are you sure you want to ' + action + ' this booking?')) return;
                                    this.loading = true;
                                    this.error = '';
                                    const bookingId = {{ $booking?->id ?? 'null' }};
                                    const url = '/admin/pending-bookings/' + bookingId + '/' + action;
                                    fetch(url, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'Accept': 'application/json',
                                        }
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (data.ok) { this.status = data.status; }
                                        else { this.error = data.error || 'Something went wrong.'; }
                                    })
                                    .catch(() => { this.error = 'Request failed. Please try again.'; })
                                    .finally(() => { this.loading = false; });
                                }
                             }"
                             class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

                            <div class="p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <div class="flex-shrink-0 mt-0.5">
                                            @if($notification->type === 'booking_request')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                                                    Booking Request
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                    {{ $notification->type }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $notification->title }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $notification->body }}</p>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-400 flex-shrink-0 whitespace-nowrap">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                @if($booking)
                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs mb-4">
                                            <div>
                                                <p class="text-gray-400 font-medium mb-0.5">Patient</p>
                                                <p class="text-gray-800 dark:text-gray-200 font-semibold">
                                                    {{ trim(($pfd['first_name'] ?? '') . ' ' . ($pfd['surname'] ?? '')) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-gray-400 font-medium mb-0.5">Date</p>
                                                <p class="text-gray-800 dark:text-gray-200 font-semibold">
                                                    {{ $booking->appointment_date->format('j M Y') }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-gray-400 font-medium mb-0.5">Time</p>
                                                <p class="text-gray-800 dark:text-gray-200 font-semibold">
                                                    {{ substr($booking->appointment_time, 0, 5) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-gray-400 font-medium mb-0.5">Type</p>
                                                <p class="text-gray-800 dark:text-gray-200 font-semibold">
                                                    {{ \App\Enums\AppointmentType::from($booking->appointment_type)->label() }}
                                                </p>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <p class="text-gray-400 font-medium mb-0.5">Address</p>
                                                <p class="text-gray-800 dark:text-gray-200">
                                                    {{ $pfd['address_line_1'] ?? '' }}, {{ $pfd['town_city'] ?? '' }}, {{ $pfd['post_code'] ?? '' }}
                                                </p>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <p class="text-gray-400 font-medium mb-0.5">Customer Email</p>
                                                <p class="text-gray-800 dark:text-gray-200">{{ $booking->customer_email }}</p>
                                            </div>
                                            @if($booking->reason)
                                            <div class="sm:col-span-4">
                                                <p class="text-gray-400 font-medium mb-0.5">Reason</p>
                                                <p class="text-gray-800 dark:text-gray-200">{{ $booking->reason }}</p>
                                            </div>
                                            @endif
                                        </div>

                                        {{-- Decision area --}}
                                        <div>
                                            <template x-if="status === 'pending'">
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <button type="button"
                                                            @click="decide('approve')"
                                                            :disabled="loading"
                                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-50">
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                        </svg>
                                                        Approve
                                                    </button>
                                                    <button type="button"
                                                            @click="decide('decline')"
                                                            :disabled="loading"
                                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-50">
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                        </svg>
                                                        Decline
                                                    </button>
                                                    <span x-show="loading" class="text-sm text-gray-400">Processing…</span>
                                                    <span x-show="error" x-text="error" class="text-sm text-red-500"></span>
                                                </div>
                                            </template>

                                            <template x-if="status === 'approved'">
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-sm font-semibold rounded-lg">
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                                        </svg>
                                                        Approved — patient &amp; appointment created
                                                    </span>
                                                    @if($booking->patient_id)
                                                        <a href="{{ route('patients.show', $booking->patient_id) }}"
                                                           class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View Patient</a>
                                                    @endif
                                                </div>
                                            </template>

                                            <template x-if="status === 'declined'">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-sm font-semibold rounded-lg">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                                                    </svg>
                                                    Declined
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
