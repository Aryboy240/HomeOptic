<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">User Management</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-md text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm px-5 py-4">
                    <div class="flex flex-wrap gap-3 items-end">

                        {{-- Search --}}
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Search</label>
                            <input type="text" name="search" value="{{ $filters['search'] }}"
                                   placeholder="Name or email…"
                                   class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Sort --}}
                        <div class="w-40">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sort</label>
                            <select name="sort"
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="latest"    @selected($filters['sort'] === 'latest')>Latest</option>
                                <option value="oldest"    @selected($filters['sort'] === 'oldest')>Oldest</option>
                                <option value="name_asc"  @selected($filters['sort'] === 'name_asc')>Name A–Z</option>
                                <option value="name_desc" @selected($filters['sort'] === 'name_desc')>Name Z–A</option>
                            </select>
                        </div>

                        {{-- Created from --}}
                        <div class="w-40">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Created from</label>
                            <input type="date" name="created_from" value="{{ $filters['createdFrom'] }}"
                                   class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Created to --}}
                        <div class="w-40">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Created to</label>
                            <input type="date" name="created_to" value="{{ $filters['createdTo'] }}"
                                   class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2 pb-0.5">
                            <button type="submit"
                                    class="px-3 py-2 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700">
                                Apply
                            </button>
                            <a href="{{ route('admin.users.index') }}"
                               class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-medium rounded-md hover:bg-gray-200 dark:hover:bg-gray-600">
                                Reset
                            </a>
                        </div>

                    </div>
                </div>
            </form>

            {{-- Existing users --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Admin Users</h3>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $users->count() }} {{ Str::plural('user', $users->count()) }}</span>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-5 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Name</th>
                            <th class="px-5 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Email</th>
                            <th class="px-5 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Created</th>
                            <th class="px-5 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-5 py-3 text-gray-800 dark:text-gray-200 font-medium">
                                    {{ $user->name }}
                                    @if($user->id === auth()->id())
                                        <span class="ml-1 text-xs text-gray-400">(you)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-5 py-3">
                                    @if($user->id === auth()->id())
                                        <span class="text-xs text-gray-400 cursor-not-allowed" title="You cannot delete your own account">Delete</span>
                                    @elseif(in_array($user->id, $usersWithRecords))
                                        <span class="text-xs text-gray-400 cursor-not-allowed"
                                              title="This user has clinical records attached and cannot be deleted">Has records</span>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-xs text-red-600 hover:text-red-800 hover:underline">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-6 text-center text-sm text-gray-400 dark:text-gray-500">
                                    No users match the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Add new user form --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Create New Admin Account</h3>
                </div>
                <form method="POST" action="{{ route('admin.users.store') }}" class="px-5 py-4 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <input type="password" name="password"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                    </div>

                    <div class="pt-1">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Create User
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
