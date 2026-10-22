@extends('layouts.app') {{-- Подключение вашего базового шаблона с шапкой и подвалом --}}

@section('title', 'Лицензионное соглашение — МенеджерПлюс')

@section('content')
<style>
    .gradient-text-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>

<main class="container-wide w-full pt-24 mb-20 px-4 md:px-10 mx-auto max-w-[1440px]">
    <!-- Хлебные крошки -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="/" class="hover:text-emerald-600 transition-colors">Главная</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-gray-600">Лицензионное соглашение</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        <!-- Левое боковое меню (Навигация) -->
        <div class="lg:col-span-1 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm sticky top-24">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3 px-3">Юридическая информация</p>
            <nav class="flex lg:flex-col gap-1 overflow-x-auto whitespace-nowrap lg:whitespace-normal">
                <a href="{{ route('licence') }}" class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3 transition-all bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500">
                    <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i> Лицензионная оферта
                </a>
                <a href="{{ route('privacy') }}" class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3 transition-all text-gray-600 hover:bg-gray-50">
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
                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full">Публичная оферта</span>
                <span class="text-xs text-gray-400">Редакция от 22.10.2026</span>
            </div>
            
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Договор-оферта о предоставлении права использования программы для ЭВМ «МенеджерПлюс»</h1>
            
            <div class="text-sm text-gray-500 mb-8 bg-gray-50 p-4 rounded-xl border border-gray-100 leading-relaxed">
                Настоящий документ является официальным предложением (публичной офертой) в соответствии со ст. 437 Гражданского кодекса РФ и определяет условия предоставления доступа к SaaS-платформе менеджерплюс.рф.
            </div>
            
            <div class="space-y-6 text-gray-600 text-sm leading-relaxed">
                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">1. Предмет договора</h3>
                    <p>1.1. Лицензиар предоставляет Лицензиату право использования (простую неисключительную лицензию) Программы «МенеджерПлюс» через веб-интерфейс на условиях удаленного доступа (SaaS) в пределах доступных функциональных возможностей и выбранного Тарифного плана.</p>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">2. Порядок заключения договора (Акцепт)</h3>
                    <p>2.1. Полным и безоговорочным акцептом настоящей оферты (ст. 438 ГК РФ) признается совершение Лицензиатом любого из следующих действий: завершение процедуры регистрации на Сайте с созданием записи в таблице пользователей (флаг <code>is_active = true</code>) или проведение авансового платежа в счет оплаты Лицензии.</p>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">3. Тарифные планы и технические лимиты</h3>
                    <p class="mb-3">3.1. Объем предоставляемых вычислительных ресурсов строго лимитирован архитектурой хранения и типами лицензий в СУБД:</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-4">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="font-semibold text-gray-900 text-xs mb-1">Базовый (basic)</p>
                            <span class="text-xs text-gray-500">Диск: 2 ГБ<br>Файл до: 100 МБ</span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="font-semibold text-gray-900 text-xs mb-1">Оптимальный (optimal)</p>
                            <span class="text-xs text-gray-500">Диск: 50 ГБ<br>Файл до: 500 МБ</span>
                        </div>
                        <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                            <p class="font-semibold text-emerald-900 text-xs mb-1">Премиум (premium)</p>
                            <span class="text-xs text-emerald-700">Диск: 1000 ГБ<br>Файл до: 1 ГБ</span>
                        </div>
                    </div>
                    <p>3.2. При превышении лимита хранилища (флаг <code>is_storage_limit_exceeded</code>) возможность загрузки новых файлов блокируется до момента очистки пространства или смены тарифа.</p>
                    <p class="mt-2">3.3. Докупка мест для сотрудников (<code>AdditionalUserPurchase</code>) тарифицируется отдельно и действует строго до наступления даты экспирации (<code>expires_at</code>).</p>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">4. Порядок расчетных операций и Автопродление</h3>
                    <p>4.1. Обязательства по оплате считаются исполненными в момент изменения статуса платежа в БД на «completed» на основании успешной верификации вебхука платежного провайдера.</p>
                    <p class="mt-2">4.2. При включенной функции автопродления (<code>auto_renew = true</code>) Лицензиат дает согласие на автоматическое списание средств за 1 день до окончания подписки.</p>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">5. Ограничение ответственности</h3>
                    <p>5.1. Платформа предоставляется на условиях «As Is» («Как есть»). Лицензиар не несет ответственности за упущенную выгоду бизнеса, срывы дедлайнов в задачах (<code>Task</code>) или событиях (<code>Event</code>), а также за безопасность сторонних SMTP-серверов, данные которых внесены в модель <code>SmtpSetting</code>.</p>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
