@extends('layouts.hrms')

@section('title', 'Recruitment')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($jobs as $job)
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-4">
                <h3 class="font-bold text-lg text-gray-800">{{ $job->title }}</h3>
                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs">{{ $job->type }}</span>
            </div>
            <p class="text-sm text-gray-500 mb-4">{{ $job->department->name }} • {{ $job->location }}</p>
            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <div class="text-sm">
                    <span class="font-bold text-gray-800">{{ $job->candidates_count }}</span> Applicants
                </div>
                <a href="{{ route('recruitment.show', $job->id) }}" class="text-green-600 hover:underline text-sm font-medium">View Details</a>
            </div>
        </div>
        @endforeach
    </div>
@endsection