@extends('admin.layouts.admin')

@section('title', 'Edit User')

@section('breadcrumbs')
<a href="{{ route('admin.users') }}" class="text-emerald-500 hover:text-emerald-700">Manage Users</a>
<span class="mx-2">/</span>
<span>Edit User: {{ $user->name }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit User</h1>
        <p class="text-gray-600 mt-1">Update user information.</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ $user->name }}" required
                           class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ $user->email }}" required
                           class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Change Password</h3>
                    <p class="text-gray-500 text-sm mb-4">Leave password fields empty if you don't want to change the password.</p>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" id="password"
                               class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password.</p>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation Field -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- User Status Information -->
                <div class="bg-gray-50 p-4 rounded-md mt-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">User Status Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs text-gray-500">Registration Date</span>
                            <span class="block text-sm">{{ $user->created_at ? $user->created_at->format('M d, Y h:i A') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500">Last Updated</span>
                            <span class="block text-sm">{{ $user->updated_at ? $user->updated_at->format('M d, Y h:i A') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-5 border-t border-gray-200">
                    <a href="{{ route('admin.users') }}" class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="bg-emerald-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-emerald-700">
                        Update User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
