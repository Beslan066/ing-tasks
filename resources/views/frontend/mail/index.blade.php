@extends('layouts.app')

@section('content')
    <div id="mail-page" class="page">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Почта отдела: <span id="department-email-title">Все письма</span></h2>
                <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-secondary transition-colors">
                    <i class="fas fa-plus mr-2"></i> Написать письмо
                </button>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">От</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тема</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50 cursor-pointer">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="avatar bg-blue-500">ИП</div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Иван Петров</div>
                                    <div class="text-sm text-gray-500">ivan@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Обсуждение нового проекта</div>
                            <div class="text-sm text-gray-500">Привет, давайте обсудим детали нового проекта...</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">10:30</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-primary hover:text-secondary mr-3"><i class="fas fa-reply"></i></button>
                            <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 cursor-pointer">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="avatar bg-green-500">АС</div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Анна Сидорова</div>
                                    <div class="text-sm text-gray-500">anna@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Отчет за март</div>
                            <div class="text-sm text-gray-500">Представляю вашему вниманию отчет за март...</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Вчера</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-primary hover:text-secondary mr-3"><i class="fas fa-reply"></i></button>
                            <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
