@extends('admin.layouts.admin')

@section('title', 'Create Admin')

@section('breadcrumbs')
<a href="{{ route('admin.admins') }}" class="text-gray-700 hover:text-emerald-600">Manage Admins</a>
<span class="mx-2">/</span>
<span>Create Admin</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Create Admin</h1>
            <p class="text-gray-600 mt-1">Create a new administrator account.</p>
        </div>
        <a href="{{ route('admin.admins') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back
        </a>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('admin.register.submit') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full
                                  shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full
                                  shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full
                                  shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full
                                  shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <div class="mt-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative bg-white border rounded-lg shadow-sm p-4 flex cursor-pointer
                                    focus:outline-none hover:bg-gray-50 transition duration-150">
                            <div class="flex items-center h-5">
                                <input id="role_admin" name="role" type="radio" value="admin" checked
                                       class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                            </div>
                            <div class="ml-3 flex flex-col">
                                <span class="block text-sm font-medium text-gray-900">Admin</span>
                                <span class="block text-xs text-gray-500">
                                    Can manage website content and user information.
                                </span>
                            </div>
                        </div>

                        <div class="relative bg-white border rounded-lg shadow-sm p-4 flex cursor-pointer
                                    focus:outline-none hover:bg-gray-50 transition duration-150">
                            <div class="flex items-center h-5">
                                <input id="role_super_admin" name="role" type="radio" value="super_admin"
                                       class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                            </div>
                            <div class="ml-3 flex flex-col">
                                <span class="block text-sm font-medium text-gray-900">Super Admin</span>
                                <span class="block text-xs text-gray-500">
                                    Has full access to all features, including admin management.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                        Create Admin
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Password Tips -->
    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-key text-yellow-600"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Password Requirements</h3>
                <div class="mt-1 text-sm text-yellow-700">
                    <ul class="list-disc space-y-1 pl-5">
                        <li>At least 8 characters long</li>
                        <li>Should include uppercase and lowercase letters</li>
                        <li>Should include at least one number</li>
                        <li>Should include at least one special character</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
