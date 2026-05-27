@extends('layouts.admin')

@section('content')
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Вывод ошибок -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <strong><i class="bx bx-error-circle me-1"></i> Ошибка!</strong> Пожалуйста, исправьте следующие ошибки:
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Multi Column with Form Separator -->
            <div class="card mb-6">
                <h5 class="card-header">Новости - редактирование</h5>

                <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data" id="editForm">
                    @csrf
                    @method('PATCH')

                    <div class="card-body">
                        <div class="row g-6">
                            <div class="col-md-6 mb-6 mt-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="title" class="form-control @error('title') is-invalid @enderror"
                                           placeholder="Заголовок новости" name="title" value="{{ old('title', $news->title) }}">
                                    <label for="title">Название</label>
                                </div>
                                @error('title')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-6 mt-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="lead" class="form-control @error('lead') is-invalid @enderror"
                                           placeholder="Короткое описание" name="lead" value="{{ old('lead', $news->lead) }}">
                                    <label for="lead">Лид</label>
                                </div>
                                @error('lead')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Quill редактор -->
                            <div class="col-12">
                                <div class="card">
                                    <h5 class="card-header">Контент</h5>
                                    <div class="card-body">
                                        <div id="snow-toolbar">
                                            <span class="ql-formats">
                                                <select class="ql-font"></select>
                                                <select class="ql-size"></select>
                                            </span>
                                            <span class="ql-formats">
                                                <button class="ql-bold"></button>
                                                <button class="ql-italic"></button>
                                                <button class="ql-underline"></button>
                                                <button class="ql-strike"></button>
                                            </span>
                                            <span class="ql-formats">
                                                <select class="ql-color"></select>
                                                <select class="ql-background"></select>
                                            </span>
                                            <span class="ql-formats">
                                                <button class="ql-script" value="sub"></button>
                                                <button class="ql-script" value="super"></button>
                                            </span>
                                            <span class="ql-formats">
                                                <button class="ql-header" value="1"></button>
                                                <button class="ql-header" value="2"></button>
                                                <button class="ql-blockquote"></button>
                                                <button class="ql-code-block"></button>
                                            </span>
                                        </div>
                                        <div id="snow-editor" style="min-height: 300px;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Текущее изображение -->
                            @if($news->image)
                                <div class="col-12 mb-4">
                                    <label class="form-label">Текущее изображение</label>
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($news->image) }}" alt="Current image"
                                             style="max-width: 300px; max-height: 200px;" class="img-thumbnail">
                                        <div class="form-text mt-2">
                                            <small>Загрузите новое изображение, чтобы заменить текущее</small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Загрузка нового изображения -->
                            <div class="col-12 mb-4">
                                <label for="image" class="form-label">Изображение новости</label>
                                <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image">
                                @error('image')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                                @if($news->image)
                                    <div class="form-text mt-2">
                                        <small>Оставьте пустым, чтобы сохранить текущее изображение</small>
                                    </div>
                                @endif
                            </div>

                            <div class="col-12">
                                <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary waves-effect me-2">
                                    Отмена
                                </a>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    Обновить
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>
@endsection

@section('scripts')
    <!-- Подключаем Quill -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        // Ждем полной загрузки страницы
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM загружен');

            // Получаем контент из новости или old значения
            var initialContent = `{{ addslashes(old('content', $news->content)) }}`;
            console.log('Initial content length:', initialContent.length);

            // Инициализируем Quill
            var quill = new Quill('#snow-editor', {
                theme: 'snow',
                modules: {
                    toolbar: '#snow-toolbar'
                },
                placeholder: 'Введите содержание новости...'
            });

            // Устанавливаем начальный контент (декодируем HTML)
            if (initialContent && initialContent !== '') {
                // Декодируем HTML сущности
                var decodedContent = initialContent.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
                quill.root.innerHTML = decodedContent;
                console.log('Контент установлен в редактор');
            }

            // Получаем форму
            var form = document.getElementById('editForm');

            // Создаем скрытое поле, если его нет
            if (!document.querySelector('input[name="content"]')) {
                var hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'content';
                form.appendChild(hiddenInput);
                console.log('Создано скрытое поле content');
            }

            var hiddenInput = document.querySelector('input[name="content"]');

            // Обновляем скрытое поле при изменении контента
            quill.on('text-change', function() {
                hiddenInput.value = quill.root.innerHTML;
                console.log('Контент обновлен, длина:', hiddenInput.value.length);
            });

            // Принудительно обновляем перед отправкой
            form.addEventListener('submit', function(e) {
                // Последнее обновление hidden поля
                hiddenInput.value = quill.root.innerHTML;

                console.log('=== ОТПРАВКА ФОРМЫ ===');
                console.log('Длина контента:', hiddenInput.value.length);
                console.log('Первые 100 символов:', hiddenInput.value.substring(0, 100));

                // Валидация: проверяем что контент не пустой
                if (!hiddenInput.value || hiddenInput.value.trim() === '' || hiddenInput.value === '<p><br></p>') {
                    console.warn('Контент пустой!');
                }

                // Для отладки - показываем в alert (уберите после тестирования)
                // alert('Отправляется контент длиной: ' + hiddenInput.value.length);

                return true;
            });

            // Инициализируем скрытое поле начальным значением
            setTimeout(function() {
                hiddenInput.value = quill.root.innerHTML;
                console.log('Начальное значение hidden поля установлено');
            }, 100);
        });
    </script>
@endsection
