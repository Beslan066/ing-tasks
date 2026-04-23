@extends('layouts.app')

@section('content')
    <div class="h-[calc(100vh-120px)]">
        <div class="bg-white rounded-lg shadow-sm p-4 h-full flex flex-col">
            <div class="flex justify-between items-center mb-4 pb-3 border-b">
                <h1 class="text-2xl font-bold text-gray-800">Яндекс Телемост</h1>
                <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>
            <div class="flex-1">
                <iframe class="w-full h-full rounded-lg border-0"
                        src="https://telemost.yandex.ru/"
                        allow="camera; microphone; fullscreen; display-capture">
                </iframe>
            </div>
        </div>
    </div>
@endsection
