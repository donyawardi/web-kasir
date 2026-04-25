<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Role</h2>
    </x-slot>

<div class="py-6 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Edit Role: {{ ucfirst($role->name) }}</h2>
                    <p class="text-sm text-gray-400">Ubah nama dan permissions untuk role ini.</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-100">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-semibold text-red-700">Terdapat kesalahan:</span>
                    </div>
                    <ul class="list-disc pl-5 text-sm text-red-600 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('roles.update', $role->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Role</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}"
                        @class([
                            'block w-full rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500',
                            'border-red-300 bg-red-50' => $errors->has('name'),
                            'border-gray-200' => !$errors->has('name')
                        ]) required>
                    @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Permissions</label>
                    @if($permissions->count())
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($permissions as $permission)
                                <label for="permission_{{ $permission->id }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/30 cursor-pointer transition group has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-50">
                                    <input class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        id="permission_{{ $permission->id }}"
                                        {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Belum ada permission.</p>
                    @endif
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Update
                    </button>
                    <a href="{{ route('roles.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>