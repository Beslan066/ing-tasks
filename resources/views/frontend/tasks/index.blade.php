@extends('layouts.app')

@section('content')
    <!-- Страница задач -->
    <div id="tasks">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-dark">Все задачи</h1>
                <p class="text-gray-500">Просмотр и управление всеми задачами</p>
            </div>
            <div class="flex space-x-4">
                <button class="bg-primary text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-secondary transition">
                    <i class="fas fa-plus"></i>
                    <span>Новая задача</span>
                </button>
                <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-gray-50 transition">
                    <i class="fas fa-filter"></i>
                    <span>Фильтр</span>
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Задача</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Исполнитель</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Срок</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Приоритет</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr class="hover:bg-gray-50 cursor-pointer">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Разработать главную страницу</div>
                                <div class="text-sm text-gray-500">Разработка</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Новая</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Иван Иванов</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">15.10.2023</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Высокий</span>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 cursor-pointer">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Интеграция с API</div>
                                <div class="text-sm text-gray-500">Разработка</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">В работе</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Анна Петрова</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">20.10.2023</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Средний</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
