@extends('layouts.hrms')

@section('title', 'Company News')

@section('content')
    <div class="space-y-6 max-w-4xl">
        @forelse($announcements as $news)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold">
                    A
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Admin</h4>
                    <p class="text-xs text-gray-500">{{ $news->created_at->format('M d, Y • h:i A') }}</p>
                </div>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $news->title }}</h3>
            <p class="text-gray-600 leading-relaxed">{{ $news->content }}</p>
        </div>
        @empty
        <div class="text-center py-10 text-gray-500">
            No announcements yet.
        </div>
        @endforelse
    </div>
@endsection