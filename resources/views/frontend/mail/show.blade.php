@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Хлебные крошки и кнопки действий -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('departments.emails.index', $department) }}"
                           class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left"></i> Назад к списку
                        </a>
                        <div class="text-sm text-gray-500">
                            {{ $department->name }}
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        @if($email->is_draft)
                            <a href="{{ route('departments.emails.edit', [$department, $email]) }}"
                               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Редактировать
                            </a>
                        @endif

                        <form action="{{ route('departments.emails.destroy', [$department, $email]) }}"
                              method="POST"
                              onsubmit="return confirm('Удалить это письмо?')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                Удалить
                            </button>
                        </form>

                        <form action=""
                              method="POST"
                              class="inline">
                            @csrf
                            @if($email->is_archived)
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                                    Разархивировать
                                </button>
                            @else
                                <button type="submit"
                                        class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                                    Архивировать
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Основная информация письма -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Заголовок письма -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        {{ $email->subject }}
                        @if($email->is_draft)
                            <span class="text-sm bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full ml-2">Черновик</span>
                        @endif
                        @if($email->is_archived)
                            <span class="text-sm bg-gray-100 text-gray-800 px-2 py-1 rounded-full ml-2">Архив</span>
                        @endif
                    </h1>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Метки -->
                            @if($email->tags->count() > 0)
                                <div class="flex items-center space-x-2">
                                    @foreach($email->tags as $tag)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                              style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Статус важности -->
                            @if($email->is_important)
                                <span class="text-red-500">
                                <i class="fas fa-star"></i> Важное
                            </span>
                            @endif
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $email->sent_at ? $email->sent_at->format('d.m.Y H:i') : 'Черновик' }}
                        </div>
                    </div>
                </div>

                <!-- Информация об отправителе/получателе -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">От:</div>
                            <div class="font-medium text-gray-900">
                                {{ $email->from_name }}
                                <span class="text-gray-600">&lt;{{ $email->from_email }}&gt;</span>
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-600 mb-1">Кому:</div>
                            <div class="font-medium text-gray-900">
                                {{ $email->to_name }}
                                <span class="text-gray-600">&lt;{{ $email->to_email }}&gt;</span>
                            </div>
                        </div>
                    </div>

                    @if($email->cc)
                        <div class="mt-4">
                            <div class="text-sm text-gray-600 mb-1">Копия:</div>
                            <div class="text-gray-900">{{ $email->cc }}</div>
                        </div>
                    @endif

                    @if($email->bcc)
                        <div class="mt-4">
                            <div class="text-sm text-gray-600 mb-1">Скрытая копия:</div>
                            <div class="text-gray-900">{{ $email->bcc }}</div>
                        </div>
                    @endif
                </div>

                <!-- Вложения -->
                @if($email->files->count() > 0)
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Вложения ({{ $email->files->count() }})
                                @php
                                    $totalSize = 0;
                                    foreach($email->files as $emailFile) {
                                        if($emailFile->file && $emailFile->file->size) {
                                            $totalSize += $emailFile->file->size;
                                        }
                                    }
                                @endphp
                                @if($totalSize > 0)
                                    <span class="text-sm font-normal text-gray-600">
                                • {{ round($totalSize / 1024 / 1024, 2) }} MB
                            </span>
                                @endif
                            </h3>

                            @if($email->files->count() > 0)
                                <a href="#"
                                   class="text-blue-600 hover:text-blue-800 text-sm"
                                   onclick="downloadAllFiles()">
                                    <i class="fas fa-download mr-1"></i> Скачать все
                                </a>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($email->files as $emailFile)
                                @php
                                    $extension = strtolower(pathinfo($emailFile->original_name, PATHINFO_EXTENSION));
                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']);
                                    $icon = 'fa-file';
                                    $type = 'Документ';

                                    if (in_array($extension, ['pdf'])) {
                                        $icon = 'fa-file-pdf';
                                        $type = 'PDF';
                                    } elseif (in_array($extension, ['doc', 'docx'])) {
                                        $icon = 'fa-file-word';
                                        $type = 'Word';
                                    } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                        $icon = 'fa-file-excel';
                                        $type = 'Excel';
                                    } elseif (in_array($extension, ['ppt', 'pptx'])) {
                                        $icon = 'fa-file-powerpoint';
                                        $type = 'PowerPoint';
                                    } elseif (in_array($extension, ['zip', 'rar', '7z', 'tar', 'gz'])) {
                                        $icon = 'fa-file-archive';
                                        $type = 'Архив';
                                    } elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'flac'])) {
                                        $icon = 'fa-file-audio';
                                        $type = 'Аудио';
                                    } elseif (in_array($extension, ['mp4', 'avi', 'mov', 'mkv', 'wmv'])) {
                                        $icon = 'fa-file-video';
                                        $type = 'Видео';
                                    } elseif (in_array($extension, ['txt'])) {
                                        $icon = 'fa-file-alt';
                                        $type = 'Текст';
                                    } elseif ($isImage) {
                                        $icon = 'fa-file-image';
                                        $type = 'Изображение';
                                    }
                                @endphp

                                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                    @if($isImage && $emailFile->file && $emailFile->file->path)
                                        <div class="h-40 bg-gray-100 overflow-hidden">
                                            <img src="{{ Storage::url($emailFile->file->path) }}"
                                                 alt="{{ $emailFile->original_name }}"
                                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-200"
                                                 onclick="openImageModal('{{ Storage::url($emailFile->file->path) }}', '{{ $emailFile->original_name }}')"
                                                 style="cursor: pointer">
                                        </div>
                                    @else
                                        <div class="h-40 bg-gray-100 flex items-center justify-center">
                                            <i class="fas {{ $icon }} text-4xl text-gray-400"></i>
                                        </div>
                                    @endif

                                    <div class="p-4">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 truncate mb-1"
                                                    title="{{ $emailFile->original_name }}">
                                                    {{ $emailFile->original_name }}
                                                </h4>
                                                <div class="flex items-center space-x-3 text-sm text-gray-500 mb-2">
                                                    <span>{{ $type }}</span>
                                                    <span>•</span>
                                                    <span>{{ strtoupper($extension) }}</span>
                                                    @if($emailFile->file && $emailFile->file->size)
                                                        <span>•</span>
                                                        <span>{{ round($emailFile->file->size / 1024) }} KB</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            @if($emailFile->file && $emailFile->file->path)
                                                <a href="{{ route('files.download', $emailFile->file) }}"
                                                   class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded hover:bg-blue-700 transition-colors">
                                                    <i class="fas fa-download mr-2"></i> Скачать
                                                </a>
                                                @if($isImage)
                                                    <button onclick="openImageModal('{{ Storage::url($emailFile->file->path) }}', '{{ $emailFile->original_name }}')"
                                                            class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300 transition-colors">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <span class="text-red-500 text-sm">Файл не найден</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Тело письма -->
                <div class="px-6 py-8">
                    <div class="prose max-w-none">
                        {!! $email->body !!}
                    </div>

                    @if(empty(strip_tags($email->body)))
                        <div class="text-gray-500 italic">Текст письма отсутствует</div>
                    @endif
                </div>

                <!-- Подпись -->
                @if($email->signature)
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="prose max-w-none text-gray-600">
                            {!! $email->signature !!}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Дополнительные действия -->
            <div class="mt-6 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href=""
                       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Ответить
                    </a>
                    <a href=""
                       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Переслать
                    </a>
                </div>

                <div class="text-sm text-gray-500">
                    Создано: {{ $email->created_at->format('d.m.Y H:i') }}
                    @if($email->created_at != $email->updated_at)
                        • Обновлено: {{ $email->updated_at->format('d.m.Y H:i') }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для просмотра изображений -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-5xl max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 id="modalTitle" class="text-lg font-semibold"></h3>
                <button onclick="closeImageModal()"
                        class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-4 overflow-auto max-h-[80vh]">
                <img id="modalImage" src="" alt="" class="max-w-full max-h-full mx-auto">
            </div>
            <div class="p-4 border-t flex justify-between">
                <a id="downloadImageLink" href=""
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-download mr-2"></i> Скачать
                </a>
                <button onclick="closeImageModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Закрыть
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript для модального окна и скачивания всех файлов -->
    <script>
        function openImageModal(imageUrl, fileName) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('modalTitle');
            const downloadLink = document.getElementById('downloadImageLink');

            modalImage.src = imageUrl;
            modalImage.alt = fileName;
            modalTitle.textContent = fileName;
            downloadLink.href = imageUrl;
            downloadLink.download = fileName;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function downloadAllFiles() {
            // Собираем все ссылки для скачивания
            const downloadLinks = document.querySelectorAll('a[href*="/files/download/"]');

            if (downloadLinks.length > 1) {
                if (confirm(`Скачать все ${downloadLinks.length} файлов?`)) {
                    // Открываем каждую ссылку в новой вкладке
                    downloadLinks.forEach(link => {
                        window.open(link.href, '_blank');
                    });
                }
            } else if (downloadLinks.length === 1) {
                downloadLinks[0].click();
            }
        }

        // Закрытие модального окна по ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });

        // Закрытие модального окна по клику вне его
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
    </script>

    <style>
        .prose img {
            max-width: 100%;
            height: auto;
        }

        .prose table {
            border-collapse: collapse;
            width: 100%;
        }

        .prose table th,
        .prose table td {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
        }

        .prose table th {
            background-color: #f9fafb;
        }

        .prose ul {
            list-style-type: disc;
            padding-left: 1.5em;
        }

        .prose ol {
            list-style-type: decimal;
            padding-left: 1.5em;
        }
    </style>
@endsection
