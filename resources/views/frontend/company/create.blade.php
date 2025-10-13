@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-8 w-100">
        <div class="bg-white rounded-lg w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Новая компания</h3>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="taskForm" action="{{route('companies.store')}}" method="post">
                @csrf
                @method('post')
                <div class="mb-4 w-1/2">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Название</label>
                    <input type="text"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                           placeholder="Введите название" name="name">
                </div>

                @error('name')
                <div class="text-danger">{{ $message }}</div>
                @enderror

                <div class="mb-4 w-1/2">
                    <label for="phone-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Номер
                        телефона</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 19 18">
                                <path
                                    d="M18 13.446a3.02 3.02 0 0 0-.946-1.985l-1.4-1.4a3.054 3.054 0 0 0-4.218 0l-.7.7a.983.983 0 0 1-1.39 0l-2.1-2.1a.983.983 0 0 1 0-1.389l.7-.7a2.98 2.98 0 0 0 0-4.217l-1.4-1.4a2.824 2.824 0 0 0-4.218 0c-3.619 3.619-3 8.229 1.752 12.979C6.785 16.639 9.45 18 11.912 18a7.175 7.175 0 0 0 5.139-2.325A2.9 2.9 0 0 0 18 13.446Z"/>
                            </svg>
                        </div>
                        <input type="text" id="phone-input" aria-describedby="helper-text-explanation"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               placeholder="7(***)***-**-**" required name="phone"/>
                    </div>
                    <p id="helper-text-explanation" class="mt-2 text-sm text-gray-500 dark:text-gray-400">Введите номер
                        телефона в указанном формате</p>
                </div>

                @error('phone')
                <div class="text-danger">{{ $message }}</div>
                @enderror

                <div class="mb-4 w-1/2">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Руководитель</label>
                    <select
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                        name="user_id">
                        <option value="{{auth()->user()->id}}">{{auth()->user()->name}}</option>
                    </select>
                </div>

                @error('user_id')
                <div class="text-danger">{{ $message }}</div>
                @enderror

                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelTask"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Создать
                        задачу
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
