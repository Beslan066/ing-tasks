@extends('layouts.app')

@section('title', 'Правила использования сервиса — МенеджерПлюс')

@section('content')
<main class="container-wide w-full pt-24 mb-20 px-4 md:px-10 mx-auto max-w-[1440px]">
    <!-- Хлебные крошки -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="/" class="hover:text-emerald-600 transition-colors">Главная</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-gray-600">Правила сервиса</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        <!-- Левое боковое меню (Навигация) -->
        <div class="lg:col-span-1 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm sticky top-24">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3 px-3">Юридическая информация</p>
            <nav class="flex lg:flex-col gap-1 overflow-x-auto whitespace-nowrap lg:whitespace-normal">
                <a href="{{ route('legal.licence') }}" class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3 transition-all text-gray-600 hover:bg-gray-50">
                    <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i> Лицензионная оферта
                </a>
                <a href="{{ route('legal.privacy') }}" class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3 transition-all text-gray-600 hover:bg-gray-50">
                    <i data-lucide="shield-check" class="w-4 h-4 shrink-0"></i> Конфиденциальность
                </a>
                <a href="{{ route('legal.terms') }}" class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3 transition-all bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500">
                    <i data-lucide="scale" class="w-4 h-4 shrink-0"></i> Правила сервиса
                </a>
            </nav>
        </div>

        <!-- Контентная зона -->
        <div class="lg:col-span-3 bg-white p-6 md:p-10 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">Terms of Service</span>
                <span class="text-xs text-gray-400">Редакция от 22.10.2026</span>
            </div>
            
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Пользовательское соглашение и правила платформы МенеджерПлюс</h1>
            
            <div class="space-y-6 text-gray-600 text-sm leading-relaxed">
                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">1. Обязанности сторон при отправке приглашений (Инвайтов)</h3>
                    <p>1.1. Платформа предоставляет инструмент приглашения новых сотрудников организации по email (модель <code>Invitation</code>). Инициатор отправки приглашения гарантирует, что им получено законное предварительное согласие субъекта на ввод его email-адреса в систему МенеджерПлюс. Сервис выступает исключительно техническим инструментом автоматизации рассылки.</p>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">2. Правила размещения контента и внутренняя модерация</h3>
                    <p>2.1. Внутри дисковых пространств и модулей связи Платформы строго запрещено хранение, пересылка и публикация материалов, содержащих порнографию, экстремистские призывы, вредоносное ПО, фишинговые ссылки и объекты, умышленно нарушающие авторские права третьих лиц.</p>
                    <p class="mt-2">2.2. Администрация оставляет за собой право использовать встроенные модули модерации контента (флаг одобрения <code>is_approved = false</code> в таблице медиафайлов) для блокировки или полного удаления любых пользовательских объектов, нарушающих законодательство РФ, без предварительного уведомления.</p>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">3. Контроль доступов и блокировки</h3>
                    <p>3.1. За грубые нарушения настоящих Правил Лицензиар имеет право перевести учетную запись нарушителя в неактивный статус (флаг <code>is_active = false</code>) либо полностью ограничить доступ к Пространству Компании без возврата денежных средств.</p>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
