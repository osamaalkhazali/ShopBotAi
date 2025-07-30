@extends('admin.layouts.admin')

@section('title', 'Edit Admin')

@section('breadcrumbs')
<a href="{{ route('admin.admins') }}" class="text-gray-700 hover:text-emerald-600">Manage Admins</a>
<span class="mx-2">/</span>
<span>Edit Admin</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Admin</h1>
            <p class="text-gray-600 mt-1">Update administrator account information.</p>
        </div>
        <a href="{{ route('admin.admins') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ $admin->name }}" required
                           class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ $admin->email }}" required
                           class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if(Auth::guard('admin')->user()->role === 'super_admin' && Auth::guard('admin')->id() !== $admin->id)
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select id="role" name="role"
                       class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    <option value="admin" {{ $admin->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ $admin->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                @if(isset($errors) && $errors->has('role'))
                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('role') }}</p>
                @endif
            </div>
            @endif

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="password" id="password"
                       class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password.</p>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @error('password_confirmation')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-gray-200">
                <div class="flex justify-end">
                    <a href="{{ route('admin.admins') }}"
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm
                              font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2
                              focus:ring-offset-2 focus:ring-emerald-500 mr-3">
                        Cancel
                    </a>
                    <button type="submit"
                           class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm
                                  font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700
                                  focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        Update Admin
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(Auth::guard('admin')->id() !== $admin->id &&
        !(Auth::guard('admin')->user()->role !== 'super_admin' && $admin->role === 'super_admin'))
    <div class="mt-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Danger Zone</h2>
            <div class="border-t border-b border-gray-200 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Delete Admin Account</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Permanently remove this admin account and all associated data.
                            This action cannot be undone.
                        </p>
                    </div>
                    <button type="button"
                            onclick="confirmDelete('{{ $admin->id }}')"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Confirm Modal -->
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50  items-center justify-center hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="text-center mb-4">
                <div id="modal-icon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i id="modal-icon-i" class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 id="modal-title" class="text-lg leading-6 font-medium text-gray-900">Confirm Delete</h3>
                <div class="mt-2">
                    <p id="modal-message" class="text-sm text-gray-500">Are you sure you want to delete this admin? This action cannot be undone.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="closeConfirmModal()" class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" id="confirm-button" class="bg-red-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-red-700">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden delete form -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

@section('scripts')
<script>
    // Confirmation modal functions
    function confirmDelete(id) {
        const modal = document.getElementById('confirm-modal');
        const confirmButton = document.getElementById('confirm-button');

        // Set confirm action
        confirmButton.onclick = function() {
            const form = document.getElementById('delete-form');
            form.action = '/admin/admins/' + id;
            form.submit();
        };

        // Show modal
        modal.style.display = 'flex';
    }

    function closeConfirmModal() {
        document.getElementById('confirm-modal').style.display = 'none';
    }
</script>
@endsection
@endsection
