@extends('layouts.app')

@section('content')
    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
    @endphp

    <div id="news" class="container ">
        <!-- Заголовок -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
            <div>
                @if($backgroundEnabled && $backgroundImage)
                    <h2 class="text-3xl font-bold text-white">Новости и поддержка</h2>
                @else
                    <h2 class="text-3xl font-bold text-[#16a34a]">Новости и поддержка</h2>
                @endif
            </div>
        </div>

        <!-- Блок новостей + форма поддержки в две колонки -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
            <!-- КОЛОНКА 1: НОВОСТИ С ПАГИНАЦИЕЙ -->
            @if($backgroundEnabled && $backgroundImage)
                <div class="backdrop-blur-md bg-black/30 rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
                    <h3 class="text-xl font-bold text-white border-b border-white/30 pb-2 mb-4">📰 Последние новости</h3>
                    <div class="space-y-4 custom-scroll">
                        @if(isset($newsList) && count($newsList) > 0)
                            @foreach($newsList as $news)
                                <div class="border-b border-white/20 pb-3 last:border-0 cursor-pointer transition hover:bg-white/5 rounded p-2 -mx-2"
                                     onclick="openNewsModal({
                                         title: '{{ addslashes($news['title']) }}',
                                         lead: '{{ addslashes($news['lead']) }}',
                                         content: '{{ addslashes($news['content']) }}',
                                         date: '{{ $news['date'] ?? '—' }}',
                                         author: '{{ addslashes($news['author'] ?? '') }}'
                                     })">
                                    <div class="flex justify-between items-start gap-2">
                                        <h4 class="font-semibold text-white">{{ $news['title'] }}</h4>
                                        <span class="text-xs text-white/60 whitespace-nowrap">{{ $news['date'] ?? '—' }}</span>
                                    </div>
                                    <p class="text-white/80 text-sm mt-1 line-clamp-2">{{ $news['lead'] }}</p>
                                    <p class="text-white/80 text-sm mt-1 line-clamp-2">{{ $news['content'] }}</p>
                                    @if(!empty($news['author']))
                                        <p class="text-white/50 text-xs mt-1">Автор: {{ $news['author'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-white/70 text-center py-8">Новостей пока нет.</p>
                        @endif
                    </div>
                    <!-- Пагинация -->
                    @if(method_exists($newsList, 'links'))
                        <div class="mt-6">
                            {{ $newsList->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
                    <h3 class="text-xl font-bold text-gray-800 border-b border-gray-200 pb-2 mb-4">📰 Последние новости</h3>
                    <div class="space-y-4">
                        @forelse($newsList ?? [] as $news)
                            <div class="border-b border-gray-100 pb-3 last:border-0 cursor-pointer transition hover:bg-gray-50 rounded p-2 -mx-2"
                                 onclick="openNewsModal({
                                     title: '{{ addslashes($news['title']) }}',
                                     content: '{{ addslashes($news['content']) }}',
                                     lead: '{{ addslashes($news['lead']) }}',
                                     date: '{{ $news['date'] ?? '—' }}',
                                     author: '{{ addslashes($news['author'] ?? '') }}'
                                 })">
                                <div class="flex justify-between items-start gap-2">
                                    <h4 class="font-semibold text-gray-800">{{ $news['title'] }}</h4>
                                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ $news['date'] ?? '—' }}</span>
                                </div>
                                <p class="text-gray-600 text-sm mt-1 line-clamp-2">{{ $news['lead'] }}</p>
                                <p class="text-gray-600 text-sm mt-1 line-clamp-2">{{ $news['content'] }}</p>
                                @if(!empty($news['author']))
                                    <p class="text-gray-400 text-xs mt-1">Автор: {{ $news['author'] }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-8">Новостей пока нет.</p>
                        @endforelse
                    </div>
                    <!-- Пагинация -->
                    @if(isset($newsList) && method_exists($newsList, 'links'))
                        <div class="mt-6">
                            {{ $newsList->links() }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- КОЛОНКА 2: УМЕНЬШЕННАЯ ФОРМА ОБРАТНОЙ СВЯЗИ С ФАЙЛОМ -->
            @if($backgroundEnabled && $backgroundImage)
                <div class="backdrop-blur-md bg-black/30 rounded-lg shadow-sm md:shadow-md p-4 md:p-5">
                    <h3 class="text-lg font-bold text-white border-b border-white/30 pb-2 mb-3">✉️ Поддержка</h3>
                    <form action="{{route('support.send')}}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <label for="name" class="block text-white/90 text-xs font-medium mb-1">Имя *</label>
                            <input type="text" name="name" id="name" required
                                   class="w-full px-2 py-1.5 text-sm bg-white/10 border border-white/30 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-[#16a34a] focus:border-transparent">
                        </div>
                        <div>
                            <label for="email" class="block text-white/90 text-xs font-medium mb-1">Email *</label>
                            <input type="email" name="email" id="email" required
                                   class="w-full px-2 py-1.5 text-sm bg-white/10 border border-white/30 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-[#16a34a]">
                        </div>
                        <div>
                            <label for="subject" class="block text-white/90 text-xs font-medium mb-1">Тема *</label>
                            <input type="text" name="subject" id="subject" required
                                   class="w-full px-2 py-1.5 text-sm bg-white/10 border border-white/30 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-[#16a34a]">
                        </div>
                        <div>
                            <label for="message" class="block text-white/90 text-xs font-medium mb-1">Сообщение *</label>
                            <textarea name="message" id="message" rows="3" required
                                      class="w-full px-2 py-1.5 text-sm bg-white/10 border border-white/30 rounded-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-[#16a34a]"></textarea>
                        </div>
                        <div>
                            <label for="attachment" class="block text-white/90 text-xs font-medium mb-1">Файл</label>
                            <input type="file" name="attachment" id="attachment"
                                   class="w-full text-xs text-white/70 file:mr-2 file:py-1   file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-[#16a34a] file:text-white hover:file:bg-[#15803d] cursor-pointer">
                            <p class="text-white/50 text-[10px] mt-1">Max 10MB (jpg, png, pdf, zip, doc)</p>
                        </div>
                        <button type="submit"
                                class=" bg-[#16a34a] hover:bg-[#15803d] text-white font-medium py-1.5 px-4 rounded-md text-sm transition duration-200">
                            Отправить
                        </button>
                    </form>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Ошибка!</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 mt-2" role="alert">
                            <strong class="font-bold">Успешно!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-5">
                    <h3 class="text-lg font-bold text-gray-800 border-b border-gray-200 pb-2 mb-3">✉️ Поддержка</h3>
                    <form action="{{route('support.send')}}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <label for="name" class="block text-gray-700 text-xs font-medium mb-1">Имя *</label>
                            <input type="text" name="name" id="name" required
                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#16a34a] focus:border-transparent">
                        </div>
                        <div>
                            <label for="email" class="block text-gray-700 text-xs font-medium mb-1">Email *</label>
                            <input type="email" name="email" id="email" required
                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#16a34a]">
                        </div>
                        <div>
                            <label for="subject" class="block text-gray-700 text-xs font-medium mb-1">Тема *</label>
                            <input type="text" name="subject" id="subject" required
                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#16a34a]">
                        </div>
                        <div>
                            <label for="message" class="block text-gray-700 text-xs font-medium mb-1">Сообщение *</label>
                            <textarea name="message" id="message" rows="3" required
                                      class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#16a34a]"></textarea>
                        </div>
                        <div>
                            <label for="attachment" class="block text-gray-700 text-xs font-medium mb-1">Файл</label>
                            <input type="file" name="attachment" id="attachment"
                                   class="w-full text-xs text-gray-600 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-[#16a34a] file:text-white hover:file:bg-[#15803d] cursor-pointer">
                            <p class="text-gray-400 text-[10px] mt-1">Max 10MB (jpg, png, pdf, zip, doc)</p>
                        </div>
                        <div>
                            <button type="submit"
                                    class="bg-[#16a34a] hover:bg-[#15803d] text-white font-medium py-1.5 px-4 rounded-lg text-sm transition">
                                Отправить
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- МОДАЛЬНОЕ ОКНО ДЛЯ НОВОСТЕЙ -->
    <div id="newsModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70 transition-all duration-300">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-between items-center">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-800 dark:text-white pr-8"></h3>
                <button onclick="closeNewsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-2xl leading-none">&times;</button>
            </div>
            <div class="px-6 py-4">
                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                    <span id="modalDate"></span>
                    <span id="modalAuthor"></span>
                </div>
                <div id="modalContent" class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap"></div>
            </div>
            <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 px-6 py-3 flex justify-end border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeNewsModal()" class="px-4 py-2 bg-[#16a34a] hover:bg-[#15803d] text-white rounded-md text-sm transition">Закрыть</button>
            </div>
        </div>
    </div>

    <!-- JavaScript для модального окна -->
    <script>
        function openNewsModal(news) {
            document.getElementById('modalTitle').innerText = news.title || 'Новость';
            document.getElementById('modalDate').innerHTML = news.date ? '📅 ' + news.date : '';
            document.getElementById('modalAuthor').innerHTML = news.author ? '✍️ ' + news.author : '';
            document.getElementById('modalContent').innerText = news.content || 'Нет содержимого.';
            document.getElementById('newsModal').classList.remove('hidden');
            document.getElementById('newsModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeNewsModal() {
            document.getElementById('newsModal').classList.add('hidden');
            document.getElementById('newsModal').classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Закрытие при клике на фон
        document.getElementById('newsModal').addEventListener('click', function(e) {
            if (e.target === this) closeNewsModal();
        });

        // Закрытие по Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeNewsModal();
        });
    </script>

    <!-- Дополнительный CSS -->
    <style>
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection
