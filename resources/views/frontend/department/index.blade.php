@extends('layouts.app')

@section('content')
    <!-- Страница досок -->
    <div id="boards">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-dark">Мои отделы</h1>
                <p class="text-gray-500">Управляйте рабочими пространствами и проектами</p>
            </div>
            <button class="bg-primary text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-secondary transition">
                <i class="fas fa-plus"></i>
                <span>Новый отдел</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6 card-hover cursor-pointer">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-code text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Разработка</h3>
                        <p class="text-gray-500">12 активных задач</p>
                    </div>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Обновлено 2 часа назад</span>
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">ИИ</div>
                        <div class="w-8 h-8 rounded-full bg-green-500 border-2 border-white flex items-center justify-center text-white text-xs">АП</div>
                        <div class="w-8 h-8 rounded-full bg-purple-500 border-2 border-white flex items-center justify-center text-white text-xs">МК</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 card-hover cursor-pointer">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-palette text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Дизайн</h3>
                        <p class="text-gray-500">8 активных задач</p>
                    </div>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Обновлено вчера</span>
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full bg-green-500 border-2 border-white flex items-center justify-center text-white text-xs">СС</div>
                        <div class="w-8 h-8 rounded-full bg-yellow-500 border-2 border-white flex items-center justify-center text-white text-xs">ДВ</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 card-hover cursor-pointer">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-vial text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Тестирование</h3>
                        <p class="text-gray-500">5 активных задач</p>
                    </div>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Обновлено 3 дня назад</span>
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full bg-purple-500 border-2 border-white flex items-center justify-center text-white text-xs">МК</div>
                        <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">ИИ</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
