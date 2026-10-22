@extends('layouts.main')

@section('title', 'Политика конфиденциальности — МенеджерПлюс')

@section('content')
<section>
    <!-- Хлебные крошки -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="/" class="hover:text-emerald-600 transition-colors">Главная</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-gray-600">Политика конфиденциальности</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        <!-- Левое боковое меню (Навигация) -->
        <div class="lg:col-span-1 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm sticky top-24">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3 px-3">Юридическая информация</p>
            <nav class="flex lg:flex-col gap-1 overflow-x-auto whitespace-nowrap lg:whitespace-normal">
                <a href="{{ route('licence') }}" class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3 transition-all text-gray-600 hover:bg-gray-50">
                    <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i> Лицензионная оферта
                </a>
                <a href="{{ route('privacy') }}" class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3 transition-all bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500">
                    <i data-lucide="shield-check" class="w-4 h-4 shrink-0"></i> Конфиденциальность
                </a>
                <a href="{{ route('terms') }}" class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3 transition-all text-gray-600 hover:bg-gray-50">
                    <i data-lucide="scale" class="w-4 h-4 shrink-0"></i> Правила сервиса
                </a>
            </nav>
        </div>

        <!-- Контентная зона -->
        <div class="lg:col-span-3 bg-white p-6 md:p-10 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">152-ФЗ РФ</span>
                <span class="text-xs text-gray-400">Редакция от 22.10.2026</span>
            </div>
            
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Политика в отношении обработки персональных данных</h1>
            
            <div class="space-y-6 text-gray-600 text-sm leading-relaxed">
                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">1. Категории собираемых персональных данных</h3>
                    <p class="mb-3">В строгом соответствии со структурой используемой СУБД, Оператор обрабатывает следующие массивы информации:</p>
                    <ul class="list-disc pl-5 space-y-2 text-gray-600">
                        <li><strong>Данные учетных записей:</strong> Имя (<code>name</code>), рабочий email (<code>email</code>), аватары профилей.</li>
                        <li><strong>Данные внешних интеграций (OAuth):</strong> Цифровые ID учетных записей внешних провайдеров авторизации.</li>
                        <li><strong>Фингерпринты девайсов:</strong> IP-адрес соединения, тип ОС, версия браузера, отпечаток устройства (<code>device_fingerprint</code>).</li>
                        <li><strong>Данные геолокации:</strong> Страна, город, точные координаты (<code>latitude</code>, <code>longitude</code>) и физический адрес.</li>
                        <li><strong>Поведенческие логи сессий:</strong> Сведения о переходах между внутренними URL (<code>from_url</code> в <code>to_url</code>) и общее время нахождения в системе (<code>total_time_seconds</code>).</li>
                        <li><strong>Рабочий контент:</strong> Содержимое задач, переписка в мессенджере (<code>Message</code>), комментарии (<code>TaskComment</code>) и корреспонденция (<code>Email</code>).</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">2. Технические цели обработки</h3>
                    <p>2.1. Сбор технических данных и гео-меток девайсов осуществляется исключительно для пресечения фрода, несанкционированных доступов и обеспечения безопасности аккаунтов на основе сквозной авторизационной проверки.</p>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">3. Криптографическая защита и локализация</h3>
                    <p>3.1. Все базы данных Платформы физически размещены на серверах провайдеров внутри территориальных границ Российской Федерации.</p>
                    <p class="mt-2">3.2. Содержимое текстовых сообщений пользователей хранится в БД в зашифрованном виде с применением симметричного сквозного кодирования (флаг <code>is_encrypted = true</code>), исключающего чтение данных сторонними лицами.</p>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">4. Порядок уничтожения данных</h3>
                    <p>4.1. При отзыве согласия на обработку или направлении запроса на удаление аккаунта, Платформа производит тотальное физическое удаление информации (метод <code>forceDelete()</code>) в течение 30 календарных дней.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
