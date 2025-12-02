{{--
    @extends('layouts.hrms')

@section('title', 'Company Documents')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <!-- Add New Document Card -->
        <div class="border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center p-6 text-gray-400 hover:border-green-500 hover:text-green-500 cursor-pointer transition h-48">
            <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span class="text-sm font-medium">Upload Document</span>
        </div>

        @forelse($documents as $doc)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col h-48 justify-between">
            <div class="flex items-start justify-between">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <button class="text-gray-400 hover:text-gray-600">•••</button>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 truncate" title="{{ $doc->title }}">{{ $doc->title }}</h3>
                <p class="text-xs text-gray-500 mt-1">Uploaded {{ $doc->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-10 text-gray-500">
            No documents found.
        </div>
        @endforelse
    </div>
@endsection

--}}