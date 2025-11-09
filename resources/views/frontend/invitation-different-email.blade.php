@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Несовпадение email
                </h2>
                <p class="text-gray-600 mb-4">
                    Это приглашение предназначено для:
                    <strong>{{ $invitation->email }}</strong>
                </p>
                <p class="text-gray-600 mb-6">
                    Вы вошли как: <strong>{{ auth()->user()->email }}</strong>
                </p>
                <div class="space-y-3">
                    <form action="{{ route('logout') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            Выйти и войти с правильным email
                        </button>
                    </form>
                    <a href="{{ route('home') }}"
                       class="block w-full py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Вернуться на главную
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
