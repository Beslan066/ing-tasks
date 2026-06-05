@extends('layouts.app')

@section('title', 'Задача #' . $task->id . ' - ' . $task->name)

@section('content')
    <div class="container mx-auto px-4 py-8">
        {{-- Та же самая верстка из модального окна --}}
        @include('partials.modal.task.modal_content', [
            'task' => $task,
            'comments' => $comments,
            'files' => $files,
            'subtasks' => $subtasks,
            'currentUser' => $currentUser,
            'canComment' => $canComment
        ])
    </div>
@endsection

@push('scripts')
    <script>
        // Добавляем кнопки сбоку для страницы
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.container');
            if (container) {
                const buttonsHtml = `
                <div class="fixed left-4 top-1/2 -translate-y-1/2 flex flex-col gap-3 z-50">
                    <button onclick="copyTaskLink()"
                            class="w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-all duration-200 hover:scale-110"
                            title="Копировать ссылку">
                        <i class="fas fa-link"></i>
                    </button>
                    <button onclick="window.print()"
                            class="w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-all duration-200 hover:scale-110"
                            title="Печать">
                        <i class="fas fa-print"></i>
                    </button>
                    <button onclick="window.history.back()"
                            class="w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-600 hover:text-red-600 hover:bg-red-50 transition-all duration-200 hover:scale-110"
                            title="Назад">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </div>
            `;
                document.body.insertAdjacentHTML('beforeend', buttonsHtml);
            }
        });

        function copyTaskLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('Ссылка скопирована!');
            });
        }
    </script>
@endpush
