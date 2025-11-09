<!-- resources/views/tasks/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Мои задачи</h1>
            <button onclick="openTaskModal()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary font-medium">
                <i class="fas fa-plus mr-2"></i>Новая задача
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($tasks as $task)
                <div class="bg-white rounded-lg shadow-md p-4 task-card" data-task-id="{{ $task->id }}">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-semibold text-lg text-gray-800">{{ $task->name }}</h3>
                        <span class="px-2 py-1 text-xs rounded-full {{ $task->status === 'выполнена' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $task->status }}
                    </span>
                    </div>

                    @if($task->description)
                        <p class="text-gray-600 text-sm mb-3">{{ Str::limit($task->description, 100) }}</p>
                    @endif

                    <div class="space-y-2 text-sm text-gray-500 mb-3">
                        @if($task->department)
                            <div class="flex items-center">
                                <i class="fas fa-building mr-2"></i>
                                {{ $task->department->name }}
                            </div>
                        @endif

                        @if($task->user)
                            <div class="flex items-center">
                                <i class="fas fa-user mr-2"></i>
                                {{ $task->user->name }}
                            </div>
                        @endif

                        @if($task->deadline)
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2"></i>
                                {{ $task->deadline->format('d.m.Y H:i') }}
                            </div>
                        @endif
                    </div>

                    <!-- Файлы задачи -->
                    @if($task->files->count() > 0)
                        <div class="border-t pt-3">
                            <h4 class="font-medium text-sm text-gray-700 mb-2">
                                <i class="fas fa-paperclip mr-1"></i>
                                Файлы
                            </h4>
                            <div class="space-y-1">
                                @foreach($task->files as $file)
                                    <div class="flex items-center justify-between text-xs bg-gray-50 p-2 rounded">
                                        <div class="flex items-center truncate">
                                            <i class="fas fa-file text-gray-400 mr-2"></i>
                                            <span class="truncate">{{ $file->name }}</span>
                                        </div>
                                        <span class="text-gray-500">{{ $file->getFormattedSize() }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mt-4 pt-3 border-t">
                    <span class="text-xs text-gray-500">
                        {{ $task->created_at->format('d.m.Y') }}
                    </span>
                        <div class="flex space-x-2">
                            <button onclick="editTask({{ $task->id }})" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteTask({{ $task->id }})" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($tasks->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-tasks text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Задачи не найдены</p>
                <button onclick="openTaskModal()" class="mt-4 px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary font-medium">
                    Создать первую задачу
                </button>
            </div>
        @endif
    </div>
@endsection
