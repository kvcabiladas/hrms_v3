{{--
@extends('layouts.hrms')

@section('title', 'Onboarding Checklist')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl">
        <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">Your Progress</h3>
                <p class="text-sm text-gray-500">Complete these tasks to finish onboarding.</p>
            </div>
            <div class="text-right">
                <span class="text-2xl font-bold text-green-600">{{ $tasks->where('is_completed', true)->count() }} / {{ $tasks->count() }}</span>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Tasks Completed</p>
            </div>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse($tasks as $task)
            <div class="p-4 flex items-center gap-4 hover:bg-gray-50 transition">
                <div class="flex-shrink-0">
                    @if($task->is_completed)
                        <div class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    @else
                        <div class="w-6 h-6 rounded-full border-2 border-gray-300"></div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="font-medium {{ $task->is_completed ? 'text-gray-500 line-through' : 'text-gray-800' }}">
                        {{ $task->task_name }}
                    </p>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                You have no onboarding tasks assigned.
            </div>
            @endforelse
        </div>
    </div>
@endsection
--}}