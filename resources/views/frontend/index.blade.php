<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>МенеджерПлюс — современная система управления командами</title>
    <meta name="description" content="Современное проектное управление. Agile-доски, корпоративный чат, всё в онлайн и мобильных приложениях." />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap');
        * { font-family: 'Inter', -apple-system, system-ui, sans-serif; }

        body {
            background: #f8fafc;
            color: #1e293b;
        }

        .gradient-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .gradient-text-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            box-shadow: 0 8px 30px rgba(16, 185, 129, 0.35);
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 1.5px solid #e2e8f0;
            color: #475569;
            transition: all 0.3s ease;
        }
        .btn-outline:hover {
            border-color: #10b981;
            color: #10b981;
            background: rgba(16, 185, 129, 0.05);
        }

        /* Стиль для фичей как на скриншоте — иконка слева, текст справа */
        .feature-row {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            background: white;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: default;
        }
        .feature-row:hover {
            border-color: #10b981;
            box-shadow: 0 4px 16px -6px rgba(16, 185, 129, 0.15);
            transform: translateY(-2px);
        }
        .feature-row .icon {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            flex-shrink: 0;
        }
        .feature-row .icon svg {
            width: 22px;
            height: 22px;
        }
        .feature-row .info {
            flex: 1;
        }
        .feature-row .info .title {
            font-weight: 600;
            font-size: 15px;
            color: #0f172a;
            line-height: 1.3;
        }
        .feature-row .info .desc {
            font-size: 13px;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.4;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            border: 1px solid #f1f5f9;
        }
        .card-hover:hover {
            transform: translateY(-6px);
            border-color: #10b981;
            box-shadow: 0 20px 40px -12px rgba(16, 185, 129, 0.2);
        }

        .image-placeholder {
            background: #f1f4f9;
            border: 2px dashed #dce3ef;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 300px;
            transition: all 0.3s ease;
        }
        .image-placeholder:hover {
            border-color: #10b981;
            background: #f8fafc;
        }
        .image-placeholder .placeholder-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            color: #94a3b8;
        }
        .image-placeholder .placeholder-content svg {
            width: 56px;
            height: 56px;
            stroke: #10b981;
            opacity: 0.5;
        }
        .image-placeholder .placeholder-content span {
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
            background: white;
            padding: 6px 20px;
            border-radius: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .pricing-card {
            background: white;
            border: 1px solid #f1f5f9;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .pricing-card:hover {
            transform: scale(1.02);
            border-color: #10b981;
        }
        .pricing-card.popular {
            border: 2px solid #10b981;
            position: relative;
        }
        .pricing-card.popular .badge {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .faq-item {
            border-bottom: 1px solid #f1f5f9;
        }
        .faq-item:last-child {
            border-bottom: none;
        }

        .testimonial-card {
            background: white;
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }
        .testimonial-card:hover {
            border-color: #10b981;
            box-shadow: 0 12px 30px -12px rgba(16, 185, 129, 0.12);
        }

        .stat-number {
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .container-wide {
            max-width: 1440px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 24px;
            padding-right: 24px;
        }

        @media (min-width: 640px) {
            .container-wide { padding-left: 40px; padding-right: 40px; }
        }
        @media (min-width: 1024px) {
            .container-wide { padding-left: 60px; padding-right: 60px; }
        }

        input, textarea, select {
            background: white;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #10b981;
            outline: none;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
        input::placeholder, textarea::placeholder {
            color: #94a3b8;
        }
    </style>
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="fixed top-0 w-full bg-white/90 backdrop-blur-md border-b border-gray-100/80 z-50">
    <div class="container-wide">
        <div class="flex items-center justify-between h-16">
            <a href="#" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background: linear-gradient(135deg, #10b981, #059669);">
                    МП
                </div>
                <span class="text-xl font-bold text-gray-800 tracking-tight">Менеджер<span style="background: linear-gradient(135deg, #10b981, #059669); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Плюс</span></span>
            </a>

            <nav class="hidden lg:flex items-center gap-8">
                <a href="#" class="text-sm font-medium text-gray-600 hover:text-[#10b981] transition">Новости</a>
                <a href="#" class="text-sm font-medium text-gray-600 hover:text-[#10b981] transition">Документы</a>
                <a href="#" class="text-sm font-medium text-gray-600 hover:text-[#10b981] transition">О нас</a>
                <a href="#" class="text-sm font-medium text-gray-600 hover:text-[#10b981] transition">Контакты</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="#" class="hidden sm:inline-block text-sm font-medium text-gray-600 hover:text-[#10b981] transition px-4 py-2 rounded-lg">Войти</a>
                <a href="#" class="btn-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-[#10b981]/25">
                    Начать
                </a>
            </div>
        </div>
    </div>
</header>

<main class="pt-16">

    <!-- ===== HERO ===== -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10" style="background: radial-gradient(ellipse at 30% 20%, rgba(16, 185, 129, 0.06) 0%, transparent 60%), radial-gradient(ellipse at 70% 80%, rgba(16, 185, 129, 0.04) 0%, transparent 50%);"></div>
        <div class="container-wide pt-16 pb-12 lg:pt-24 lg:pb-16">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-[#ecfdf5] text-[#10b981] text-xs font-semibold px-4 py-1.5 rounded-full border border-[#a7f3d0]/60 mb-6">
                        <span class="w-2 h-2 rounded-full" style="background: #10b981;"></span>
                        Выведите свой менеджмент на новый уровень
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 leading-[1.1]">
                        Современная система управления
                        <span class="gradient-text-primary">командами</span> и
                        <span class="gradient-text-primary">задачами</span>
                    </h1>
                    <p class="mt-6 text-lg text-gray-500 max-w-lg leading-relaxed">
                        Agile-доски, корпоративный чат, аналитика и хранилище — всё в одном месте.
                        Безопасное коробочное решение для больших проектов и бесплатная версия для начинающих.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="#" class="btn-primary text-white px-8 py-3.5 rounded-xl font-semibold shadow-lg shadow-[#10b981]/25 flex items-center gap-2">
                            <i data-lucide="rocket" class="w-5 h-5"></i>
                            Начать бесплатно
                        </a>
                        <a href="#" class="btn-outline text-gray-600 px-8 py-3.5 rounded-xl font-semibold flex items-center gap-2">
                            <i data-lucide="play-circle" class="w-5 h-5"></i>
                            Смотреть демо
                        </a>
                    </div>

                </div>
                <div class="relative">
                    <div class="rounded-2xl p-3 bg-white border border-gray-200/80 shadow-xl shadow-gray-200/50">
                        <div class="image-placeholder" style="min-height: 360px;">
                            <div class="placeholder-content">
                                <i data-lucide="layout-dashboard"></i>
                                <span>Скриншот вашей доски проектов</span>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full blur-3xl opacity-30 -z-10" style="background: #10b981;"></div>
                    <div class="absolute -bottom-8 -left-8 w-32 h-32 rounded-full blur-3xl opacity-20 -z-10" style="background: #10b981;"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== КЛИЕНТЫ ===== -->
    <section class="border-t border-gray-100/80 bg-white/50">
        <div class="container-wide py-10">
            <p class="text-center text-xs font-semibold text-gray-400 uppercase tracking-widest mb-6">Доверяют ведущие компании</p>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-8 items-center justify-items-center opacity-40 grayscale hover:grayscale-0 transition-all duration-500">
                <div class="text-gray-400 font-bold text-xl">Company 1</div>
                <div class="text-gray-400 font-bold text-xl">Company 2</div>
                <div class="text-gray-400 font-bold text-xl">Company 3</div>
                <div class="text-gray-400 font-bold text-xl">Company 4</div>
                <div class="text-gray-400 font-bold text-xl">Company 5</div>
                <div class="text-gray-400 font-bold text-xl">Company 6</div>
            </div>
        </div>
    </section>

    <!-- ===== БЛОК С ФИЧАМИ (как на скриншоте — иконка слева, текст справа) ===== -->
    <section class="py-16 bg-white">
        <div class="container-wide">
            <div class="grid md:grid-cols-2 gap-4 max-w-4xl mx-auto">
                <!-- Задачи и проекты -->
                <div class="feature-row">
                    <div class="icon">
                        <i data-lucide="clipboard-list"></i>
                    </div>
                    <div class="info">
                        <div class="title">Задачи и проекты</div>
                        <div class="desc">Управляйте задачами и проектами в одном месте</div>
                    </div>
                </div>

                <!-- Совместная работа -->
                <div class="feature-row">
                    <div class="icon">
                        <i data-lucide="users"></i>
                    </div>
                    <div class="info">
                        <div class="title">Совместная работа</div>
                        <div class="desc">Работайте вместе над общими проектами</div>
                    </div>
                </div>

                <!-- Мессенджер -->
                <div class="feature-row">
                    <div class="icon">
                        <i data-lucide="message-square"></i>
                    </div>
                    <div class="info">
                        <div class="title">Мессенджер</div>
                        <div class="desc">Общайтесь в корпоративном чате</div>
                    </div>
                </div>

                <!-- Аналитика -->
                <div class="feature-row">
                    <div class="icon">
                        <i data-lucide="bar-chart-3"></i>
                    </div>
                    <div class="info">
                        <div class="title">Аналитика</div>
                        <div class="desc">Отслеживайте прогресс и эффективность</div>
                    </div>
                </div>

                <!-- Инструменты -->
                <div class="feature-row">
                    <div class="icon">
                        <i data-lucide="tool"></i>
                    </div>
                    <div class="info">
                        <div class="title">Инструменты</div>
                        <div class="desc">Полный набор для продуктивной работы</div>
                    </div>
                </div>

                <!-- Хранилище -->
                <div class="feature-row">
                    <div class="icon">
                        <i data-lucide="hard-drive"></i>
                    </div>
                    <div class="info">
                        <div class="title">Хранилище</div>
                        <div class="desc">Все файлы под рукой в облаке</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== БЛОК "СОЗДАВАЙТЕ КОМАНДЫ" ===== -->
    <section class="py-20 bg-gray-50/50">
        <div class="container-wide">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-[#10b981] font-semibold text-sm tracking-widest uppercase">Управление</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">
                        Создавайте <span class="gradient-text-primary">команды</span> и назначайте <span class="gradient-text-primary">задачи</span>
                    </h2>
                    <p class="mt-5 text-gray-500 text-lg leading-relaxed">
                        Создавайте команды для любых масштабов: от личного планирования до корпоративного управления.
                        Создавайте отделы, добавляйте участников и ставьте задачи, чтобы всё было под контролем.
                    </p>
                    <ul class="mt-6 space-y-3">
                        <li class="flex items-start gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-[#10b981] mt-0.5"></i>
                            <span class="text-gray-700">Неограниченное количество команд</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-[#10b981] mt-0.5"></i>
                            <span class="text-gray-700">Гибкие настройки ролей и прав</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-[#10b981] mt-0.5"></i>
                            <span class="text-gray-700">Умное назначение задач</span>
                        </li>
                    </ul>
                </div>
                <div class="rounded-2xl p-3 bg-white border border-gray-200/80 shadow-xl shadow-gray-200/50">
                    <div class="image-placeholder" style="min-height: 320px;">
                        <div class="placeholder-content">
                            <i data-lucide="users-round"></i>
                            <span>Скриншот управления командой</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== БЛОК "ПРОДУКТИВНОСТЬ" ===== -->
    <section class="py-20 bg-white">
        <div class="container-wide">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1">
                    <div class="rounded-2xl p-3 bg-white border border-gray-200/80 shadow-xl shadow-gray-200/50">
                        <div class="image-placeholder" style="min-height: 320px;">
                            <div class="placeholder-content">
                                <i data-lucide="activity"></i>
                                <span>Скриншот дашборда аналитики</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <span class="text-[#10b981] font-semibold text-sm tracking-widest uppercase">Аналитика</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">
                        Отслеживайте активность и <span class="gradient-text-primary">продуктивность</span>
                    </h2>
                    <p class="mt-5 text-gray-500 text-lg leading-relaxed">
                        Отслеживайте прогресс и эффективность — для себя или всей команды.
                        Знайте, кто чем занят и всё ли идёт по плану.
                    </p>
                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-2xl font-bold text-gray-900"><span class="stat-number">87%</span></p>
                            <p class="text-sm text-gray-500">Рост продуктивности</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-2xl font-bold text-gray-900"><span class="stat-number">2.4x</span></p>
                            <p class="text-sm text-gray-500">Быстрее завершение</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== БЛОК "ОСТАВАЙТЕСЬ НА СВЯЗИ" ===== -->
    <section class="py-20 bg-gray-50/50">
        <div class="container-wide">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-[#10b981] font-semibold text-sm tracking-widest uppercase">Коммуникация</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">
                        Оставайтесь <span class="gradient-text-primary">на связи</span>
                    </h2>
                    <p class="mt-5 text-gray-500 text-lg leading-relaxed">
                        Пишите сообщения коллегам и комментируйте задачи — вся коммуникация в одном месте.
                        Корпоративный мессенджер держит вас на связи, а уведомления не дадут упустить важное.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <span class="inline-flex items-center gap-2 bg-[#ecfdf5] text-[#10b981] px-4 py-2 rounded-full text-sm font-medium">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> Общие чаты
                        </span>
                        <span class="inline-flex items-center gap-2 bg-[#ecfdf5] text-[#10b981] px-4 py-2 rounded-full text-sm font-medium">
                            <i data-lucide="at-sign" class="w-4 h-4"></i> Упоминания
                        </span>
                        <span class="inline-flex items-center gap-2 bg-[#ecfdf5] text-[#10b981] px-4 py-2 rounded-full text-sm font-medium">
                            <i data-lucide="bell" class="w-4 h-4"></i> Уведомления
                        </span>
                    </div>
                </div>
                <div class="rounded-2xl p-3 bg-white border border-gray-200/80 shadow-xl shadow-gray-200/50">
                    <div class="image-placeholder" style="min-height: 320px;">
                        <div class="placeholder-content">
                            <i data-lucide="message-square"></i>
                            <span>Скриншот корпоративного чата</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== О НАС + СТАТИСТИКА ===== -->
    <section class="py-20 bg-white">
        <div class="container-wide">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[#10b981] font-semibold text-sm tracking-widest uppercase">О нас</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">
                    МенеджерПлюс — <span class="gradient-text-primary">быстроразвивающийся продукт</span>
                </h2>
                <p class="mt-4 text-gray-500 text-lg leading-relaxed">
                    Разработан на основе передовых технологий. Платформа предоставляет все необходимые инструменты
                    для комфортной и продуктивной работы. Мы уделяем большое внимание конфиденциальности и безопасности
                    ваших данных.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center p-8 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-4xl font-bold text-gray-900"><span class="stat-number">15K+</span></p>
                    <p class="text-gray-500 mt-2 font-medium">Пользователей</p>
                </div>
                <div class="text-center p-8 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-4xl font-bold text-gray-900"><span class="stat-number">2.4K+</span></p>
                    <p class="text-gray-500 mt-2 font-medium">Компаний</p>
                </div>
                <div class="text-center p-8 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-4xl font-bold text-gray-900"><span class="stat-number">6</span></p>
                    <p class="text-gray-500 mt-2 font-medium">Тарифных планов</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ТАРИФЫ ===== -->
    <section class="py-20 bg-gray-50/50">
        <div class="container-wide">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[#10b981] font-semibold text-sm tracking-widest uppercase">Тарифы</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">
                    Выберите <span class="gradient-text-primary">свой план</span>
                </h2>
            </div>
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Базовый -->
                <div class="pricing-card rounded-2xl p-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800">Базовый</h3>
                        <span class="text-sm font-semibold text-gray-400">Для старта</span>
                    </div>
                    <div class="mt-4 flex items-baseline gap-1">
                        <span class="text-5xl font-bold text-gray-900">0</span>
                        <span class="text-xl font-semibold text-gray-400">₽</span>
                        <span class="text-gray-400 ml-2">/месяц</span>
                    </div>
                    <ul class="mt-6 space-y-3 text-sm">
                        <li class="flex items-center gap-3 text-gray-600"><i data-lucide="check" class="w-4 h-4 text-[#10b981]"></i> До 5 пользователей</li>
                        <li class="flex items-center gap-3 text-gray-600"><i data-lucide="check" class="w-4 h-4 text-[#10b981]"></i> До 2 ГБ хранилище</li>
                        <li class="flex items-center gap-3 text-gray-600"><i data-lucide="check" class="w-4 h-4 text-[#10b981]"></i> Аналитика и трекинг</li>
                        <li class="flex items-center gap-3 text-gray-400"><i data-lucide="x" class="w-4 h-4 text-gray-300"></i> Приоритетная поддержка</li>
                        <li class="flex items-center gap-3 text-gray-400"><i data-lucide="x" class="w-4 h-4 text-gray-300"></i> Полный набор инструментов</li>
                    </ul>
                    <a href="#" class="mt-8 block w-full text-center border-2 border-gray-200 text-gray-600 font-semibold py-3 rounded-xl hover:border-[#10b981] hover:text-[#10b981] transition">
                        Попробовать
                    </a>
                </div>

                <!-- Оптимальный -->
                <div class="pricing-card popular rounded-2xl p-8">
                    <div class="badge absolute -top-3 left-1/2 -translate-x-1/2 text-white text-xs font-bold px-4 py-1 rounded-full">Рекомендуем</div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800">Оптимальный</h3>
                        <span class="text-sm font-semibold text-[#10b981]">Популярный</span>
                    </div>
                    <div class="mt-4 flex items-baseline gap-1">
                        <span class="text-5xl font-bold text-gray-900">1 490</span>
                        <span class="text-xl font-semibold text-gray-400">₽</span>
                        <span class="text-gray-400 ml-2">/месяц</span>
                    </div>
                    <ul class="mt-6 space-y-3 text-sm">
                        <li class="flex items-center gap-3 text-gray-600"><i data-lucide="check" class="w-4 h-4 text-[#10b981]"></i> До 15 пользователей</li>
                        <li class="flex items-center gap-3 text-gray-600"><i data-lucide="check" class="w-4 h-4 text-[#10b981]"></i> До 1 ТБ хранилище</li>
                        <li class="flex items-center gap-3 text-gray-600"><i data-lucide="check" class="w-4 h-4 text-[#10b981]"></i> Корпоративный мессенджер</li>
                        <li class="flex items-center gap-3 text-gray-600"><i data-lucide="check" class="w-4 h-4 text-[#10b981]"></i> Приоритетная поддержка 24/7</li>
                        <li class="flex items-center gap-3 text-gray-600"><i data-lucide="check" class="w-4 h-4 text-[#10b981]"></i> Полный набор инструментов</li>
                    </ul>
                    <a href="#" class="mt-8 block w-full text-center btn-primary text-white font-semibold py-3 rounded-xl shadow-lg shadow-[#10b981]/25">
                        Попробовать
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ЧАВО ===== -->
    <section class="py-20 bg-white">
        <div class="container-wide">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[#10b981] font-semibold text-sm tracking-widest uppercase">ЧАВО</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">
                    Часто задаваемые <span class="gradient-text-primary">вопросы</span>
                </h2>
            </div>
            <div class="max-w-3xl mx-auto divide-y divide-gray-100">
                <div class="py-6 faq-item">
                    <button class="w-full flex items-center justify-between text-left group" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-[#10b981] transition">Подходит ли платформа для личного использования?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-hover:text-[#10b981] transition flex-shrink-0 ml-4"></i>
                    </button>
                    <div class="hidden mt-3 text-gray-500 leading-relaxed">
                        Да, платформа идеально подходит для личного использования, просто создайте команду и начните работать.
                    </div>
                </div>
                <div class="py-6 faq-item">
                    <button class="w-full flex items-center justify-between text-left group" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-[#10b981] transition">Можно ли оплачивать подписку по договору?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-hover:text-[#10b981] transition flex-shrink-0 ml-4"></i>
                    </button>
                    <div class="hidden mt-3 text-gray-500 leading-relaxed">
                        Да! Мы предоставляем возможность оплаты счетов после составления договора. Свяжитесь с нашими специалистами, они составят необходимые документы.
                        <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Email для связи:</span>
                            <a href="mailto:mail@managerplus.ru" class="text-[#10b981] font-semibold hover:underline ml-2">mail@managerplus.ru</a>
                        </div>
                    </div>
                </div>
                <div class="py-6 faq-item">
                    <button class="w-full flex items-center justify-between text-left group" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-[#10b981] transition">Какие инструменты входят в полный набор?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-hover:text-[#10b981] transition flex-shrink-0 ml-4"></i>
                    </button>
                    <div class="hidden mt-3 text-gray-500 leading-relaxed">
                        Полный набор включает: управление задачами, канбан-доски, корпоративный чат, аналитику, файловое хранилище, календари, интеграции и многое другое.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ОТЗЫВЫ ===== -->
    <section class="py-20 bg-gray-50/50">
        <div class="container-wide">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[#10b981] font-semibold text-sm tracking-widest uppercase">Отзывы</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">
                    Что говорят <span class="gradient-text-primary">пользователи</span>
                </h2>
            </div>
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="testimonial-card rounded-2xl p-8">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg" style="background: linear-gradient(135deg, #10b981, #059669);">ДС</div>
                        <div>
                            <p class="font-semibold text-gray-800">Дмитрий Смирнов</p>
                            <p class="text-sm text-gray-400">CEO, TechStart</p>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed">МенеджерПлюс полностью изменил наш подход к управлению проектами. Команда стала более организованной, а задачи выполняются в срок.</p>
                    <div class="mt-3 flex text-[#fbbf24]">
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    </div>
                </div>
                <div class="testimonial-card rounded-2xl p-8">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg" style="background: linear-gradient(135deg, #10b981, #059669);">АК</div>
                        <div>
                            <p class="font-semibold text-gray-800">Анна Кузнецова</p>
                            <p class="text-sm text-gray-400">Product Manager, CloudPro</p>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed">Интуитивно понятный интерфейс и мощная аналитика. Мы сократили время на планирование на 40%.</p>
                    <div class="mt-3 flex text-[#fbbf24]">
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="py-20 bg-white">
        <div class="container-wide">
            <div class="rounded-3xl p-12 text-center text-white relative overflow-hidden" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full blur-3xl opacity-20" style="background: #10b981;"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full blur-3xl opacity-20" style="background: #10b981;"></div>
                <div class="relative z-10">
                    <h2 class="text-3xl sm:text-4xl font-bold">Начните уже сегодня</h2>
                    <p class="mt-4 text-white/80 max-w-lg mx-auto text-lg">Повысьте вашу продуктивность. Вам доступна бесплатная версия до 5 пользователей.</p>
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a href="#" class="bg-white text-[#10b981] px-8 py-3.5 rounded-xl font-semibold hover:shadow-xl transition shadow-lg flex items-center gap-2">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                            Зарегистрироваться
                        </a>
                        <a href="#" class="bg-white/20 text-white px-8 py-3.5 rounded-xl font-semibold hover:bg-white/30 transition border border-white/20 flex items-center gap-2">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                            Связаться с нами
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== КОНТАКТЫ ===== -->
    <section class="py-20 bg-gray-50/50">
        <div class="container-wide">
            <div class="grid lg:grid-cols-2 gap-16">
                <div>
                    <span class="text-[#10b981] font-semibold text-sm tracking-widest uppercase">Контакты</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">
                        Оставьте <span class="gradient-text-primary">заявку</span>
                    </h2>
                    <form class="mt-8 space-y-4">
                        <div>
                            <input type="text" placeholder="Ваше ФИО" class="w-full px-5 py-3.5 rounded-xl border border-gray-200 bg-white focus:border-[#10b981] focus:ring-2 focus:ring-[#10b981]/20 outline-none transition text-gray-800 placeholder:text-gray-400" />
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <input type="email" placeholder="Email" class="px-5 py-3.5 rounded-xl border border-gray-200 bg-white focus:border-[#10b981] focus:ring-2 focus:ring-[#10b981]/20 outline-none transition text-gray-800 placeholder:text-gray-400" />
                            <input type="tel" placeholder="Номер телефона" class="px-5 py-3.5 rounded-xl border border-gray-200 bg-white focus:border-[#10b981] focus:ring-2 focus:ring-[#10b981]/20 outline-none transition text-gray-800 placeholder:text-gray-400" />
                        </div>
                        <div>
                            <textarea placeholder="Сообщение" rows="4" class="w-full px-5 py-3.5 rounded-xl border border-gray-200 bg-white focus:border-[#10b981] focus:ring-2 focus:ring-[#10b981]/20 outline-none transition text-gray-800 placeholder:text-gray-400 resize-none"></textarea>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#10b981] focus:ring-[#10b981]" checked />
                                Согласен с условиями использования
                            </label>
                            <button type="submit" class="btn-primary text-white px-8 py-3.5 rounded-xl font-semibold shadow-lg shadow-[#10b981]/25 flex items-center gap-2 ml-auto">
                                Отправить
                                <i data-lucide="send" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-8 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">Наш адрес</h3>
                        <p class="text-gray-500 mt-2">290 Maryam Springs 260, Courbevoie, Paris, France</p>
                    </div>
                    <div class="bg-white rounded-2xl p-8 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">Email</h3>
                        <a href="mailto:mail@managerplus.ru" class="text-[#10b981] font-semibold hover:underline mt-2 block">mail@managerplus.ru</a>
                    </div>
                    <div class="bg-white rounded-2xl p-8 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">Телефон</h3>
                        <a href="tel:+7009423346343" class="text-[#10b981] font-semibold hover:underline mt-2 block">+7 009 423 346 343</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- ===== ПОДВАЛ ===== -->
<footer class="bg-white border-t border-gray-100">
    <div class="container-wide py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="col-span-2 md:col-span-1">
                <a href="#" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white font-bold text-xs" style="background: linear-gradient(135deg, #10b981, #059669);">
                        МП
                    </div>
                    <span class="text-lg font-bold text-gray-800">Менеджер<span style="background: linear-gradient(135deg, #10b981, #059669); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Плюс</span></span>
                </a>
                <p class="mt-4 text-sm text-gray-400 max-w-xs">Современная система управления командами и задачами.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 text-sm">Продукт</h4>
                <ul class="mt-3 space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-[#10b981] transition">Новости</a></li>
                    <li><a href="#" class="hover:text-[#10b981] transition">Документы</a></li>
                    <li><a href="#" class="hover:text-[#10b981] transition">Тарифы</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 text-sm">Компания</h4>
                <ul class="mt-3 space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-[#10b981] transition">О нас</a></li>
                    <li><a href="#" class="hover:text-[#10b981] transition">Блог</a></li>
                    <li><a href="#" class="hover:text-[#10b981] transition">Контакты</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 text-sm">Подписка</h4>
                <p class="mt-3 text-sm text-gray-400">Подпишитесь, чтобы получать последние новости и обновления.</p>
                <div class="mt-3 flex">
                    <input type="email" placeholder="Email" class="flex-1 px-4 py-2.5 rounded-l-xl border border-r-0 border-gray-200 bg-white focus:border-[#10b981] focus:ring-2 focus:ring-[#10b981]/20 outline-none transition text-gray-800 placeholder:text-gray-400 text-sm" />
                    <button class="btn-primary text-white px-4 py-2.5 rounded-r-xl">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-100 mt-8 pt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-400">
            <span>© 2026 МенеджерПлюс. Все права защищены.</span>
            <div class="flex gap-4 mt-3 sm:mt-0">
                <a href="#" class="hover:text-[#10b981] transition"><i data-lucide="twitter" class="w-4 h-4"></i></a>
                <a href="#" class="hover:text-[#10b981] transition"><i data-lucide="github" class="w-4 h-4"></i></a>
                <a href="#" class="hover:text-[#10b981] transition"><i data-lucide="youtube" class="w-4 h-4"></i></a>
                <a href="#" class="hover:text-[#10b981] transition"><i data-lucide="linkedin" class="w-4 h-4"></i></a>
            </div>
        </div>
    </div>
</footer>

<script>
    lucide.createIcons();
</script>

</body>
</html>
