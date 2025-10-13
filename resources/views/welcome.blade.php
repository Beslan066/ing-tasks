@extends('layouts.app')

@section('content')
    <!-- Заголовок и кнопки -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-dark">Доска разработки</h1>
            <p class="text-gray-500">Проект "Альфа" • 12 активных задач</p>
        </div>
        <div class="flex space-x-4">
            <button id="newTaskBtn"
                    class="bg-primary text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-secondary transition">
                <i class="fas fa-plus"></i>
                <span>Новая задача</span>
            </button>
            <button
                class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-gray-50 transition">
                <i class="fas fa-filter"></i>
                <span>Фильтр</span>
            </button>
        </div>
    </div>

    <!-- Доска с задачами -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Колонка "Новые" -->
        <div class="bg-gray-100 rounded-lg p-4 board-column" data-status="new">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-700">Новые</h3>
                <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded">3</span>
            </div>

            <div class="space-y-4 task-container" data-status="new">
                <div class="task-card bg-white p-4 rounded-lg shadow cursor-move" draggable="true" data-task="1">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium">Разработать главную страницу</h4>
                        <div class="flex space-x-1">
                            <div
                                class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs">
                                ИИ
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm mb-3">Создать современный дизайн главной страницы с адаптивной
                        версткой</p>
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-1">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Frontend</span>
                        </div>
                        <div class="flex items-center space-x-1 text-gray-500">
                            <i class="far fa-comment"></i>
                            <span class="text-xs">5</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Колонка "В работе" -->
        <div class="bg-gray-100 rounded-lg p-4 board-column" data-status="in-progress">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-700">В работе</h3>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">4</span>
            </div>

            <div class="space-y-4 task-container" data-status="in-progress">
                <div class="task-card bg-white p-4 rounded-lg shadow cursor-move" draggable="true" data-task="3">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium">Рефакторинг кода</h4>
                        <div class="flex space-x-1">
                            <div
                                class="w-6 h-6 rounded-full bg-purple-500 flex items-center justify-center text-white text-xs">
                                МК
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm mb-3">Улучшить структуру кода и оптимизировать
                        производительность</p>
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-1">
                            <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">Оптимизация</span>
                        </div>
                        <div class="flex items-center space-x-1 text-gray-500">
                            <svg class="text-red-600 dark:text-gray-500 w-6 h-6  mx-auto" aria-hidden="true"
                                 fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                      clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="task-card bg-white p-4 rounded-lg shadow cursor-move" draggable="true" data-task="4">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium">Тестирование модулей</h4>
                        <div class="flex space-x-1">
                            <div
                                class="w-6 h-6 rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs">
                                ДВ
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm mb-3">Написать unit-тесты для основных модулей приложения</p>
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-1">
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Тестирование</span>
                        </div>
                        <div class="flex items-center space-x-1 text-gray-500">
                            <i class="far fa-comment"></i>
                            <span class="text-xs">3</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Колонка "На проверке" -->
        <div class="bg-gray-100 rounded-lg p-4 board-column" data-status="review">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-700">На проверке</h3>
                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded">2</span>
            </div>

            <div class="space-y-4 task-container" data-status="review">
                <div class="task-card bg-white p-4 rounded-lg shadow cursor-move" draggable="true" data-task="5">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium">Документация API</h4>
                        <div class="flex space-x-1">
                            <div
                                class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs">
                                ИИ
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm mb-3">Подготовить документацию для REST API endpoints</p>
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-1">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Документация</span>
                        </div>
                        <div class="flex items-center space-x-1 text-gray-500">
                            <i class="far fa-comment"></i>
                            <span class="text-xs">4</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Колонка "Завершено" -->
        <div class="bg-gray-100 rounded-lg p-4 board-column" data-status="done">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-700">Завершено</h3>
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">3</span>
            </div>

            <div class="space-y-4 task-container" data-status="done">
                <div class="task-card bg-white p-4 rounded-lg shadow opacity-70 cursor-move" draggable="true"
                     data-task="6">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium">Дизайн макетов</h4>
                        <div class="flex space-x-1">
                            <div
                                class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center text-white text-xs">
                                СС
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm mb-3">Создать UI/UX дизайн для всех страниц приложения</p>
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-1">
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Дизайн</span>
                        </div>
                        <div class="flex items-center space-x-1 text-gray-500">
                            <i class="far fa-comment"></i>
                            <span class="text-xs">12</span>
                        </div>
                    </div>
                </div>

                <div class="task-card bg-white p-4 rounded-lg shadow opacity-70 cursor-move" draggable="true"
                     data-task="7">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium">Настройка сервера</h4>
                        <div class="flex space-x-1">
                            <div
                                class="w-6 h-6 rounded-full bg-purple-500 flex items-center justify-center text-white text-xs">
                                МК
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm mb-3">Развернуть и настроить сервер для production среды</p>
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-1">
                            <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">DevOps</span>
                        </div>
                        <div class="flex items-center space-x-1 text-gray-500">
                            <i class="far fa-comment"></i>
                            <span class="text-xs">8</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
