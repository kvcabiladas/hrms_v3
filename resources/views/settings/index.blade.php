@extends('layouts.hrms')

@section('title', 'Company Settings')

@section('content')
    <div class="max-w-2xl bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                <input type="text" name="name" value="{{ $company->name ?? '' }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                <input type="email" name="email" value="{{ $company->email ?? '' }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                <input type="url" name="website" value="{{ $company->website ?? '' }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
            </div>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg">Save Changes</button>
        </form>
    </div>
@endsection