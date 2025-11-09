@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        Приглашение в компанию
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Вас приглашают присоединиться к компании
                        <strong>{{ $invitation->company->name }}</strong>
                    </p>

                    @if(auth()->check())
                        <p class="text-sm text-gray-500 mb-6">
                            Вы вошли как: {{ auth()->user()->email }}
                        </p>

                        <form action="{{ route('invitation.process', $invitation->token) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Принять приглашение
                            </button>
                        </form>
                    @else
                        <div class="space-y-4">
                            <a href="{{ route('login') }}?invitation={{ $invitation->token }}"
                               class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Войти и принять приглашение
                            </a>
                            <a href="{{ route('register') }}?invitation={{ $invitation->token }}"
                               class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Зарегистрироваться и принять
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
