@extends('layouts.main')

@section('title', ($post->title ?? 'Новость') . ' — Новости МенеджерПлюс')

@section('content')

    <!-- ===== HERO ===== -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10" style="background: radial-gradient(ellipse at 30% 20%, rgba(16, 185, 129, 0.06) 0%, transparent 60%), radial-gradient(ellipse at 70% 80%, rgba(16, 185, 129, 0.04) 0%, transparent 50%);"></div>
        <div class="container-wide pt-16 pb-10 lg:pt-24 lg:pb-12">
            <div class="max-w-3xl">
                <!-- Хлебные крошки -->
                <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ url('/') }}" class="hover:text-[#10b981] transition">Главная</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <a href="{{ route('news') }}" class="hover:text-[#10b981] transition">Новости</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <span class="text-gray-600 truncate max-w-[200px] sm:max-w-xs">{{ $post->title ?? 'Новость' }}</span>
                </nav>

                <div class="flex items-center gap-3 text-xs font-semibold text-[#10b981] mb-4">
                    <span class="bg-[#ecfdf5] px-2.5 py-1 rounded-full">{{ $post->category ?? 'Продукт' }}</span>
                    <time class="text-gray-400 font-normal">{{ $post->published_at ?? '12 ноября 2026' }}</time>
                    <span class="text-gray-400 font-normal flex items-center gap-1">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> 5 мин чтения
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900 leading-[1.15]">
                    {{ $post->title ?? 'Новый дашборд аналитики: больше метрик и гибкие отчёты' }}
                </h1>

                <p class="mt-5 text-lg text-gray-500 max-w-2xl leading-relaxed">
                    {{ $post->excerpt ?? 'Рассказываем, что изменилось в разделе аналитики: новые графики, фильтры по командам и экспорт отчётов в Excel.' }}
                </p>
            </div>
        </div>
    </section>

    <!-- ===== ОБЛОЖКА ===== -->
    <section class="pb-10">
        <div class="container-wide">
            <div class="rounded-2xl overflow-hidden border border-gray-200/80 shadow-xl shadow-gray-200/50">
                <div class="image-placeholder" style="min-height: 380px; border-radius: 0; border: none;">
                    <div class="placeholder-content">
                        <i data-lucide="image"></i>
                        <span>Обложка новости (16:9)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== КОНТЕНТ + САЙДБАР ===== -->
    <section class="py-10 bg-white">
        <div class="container-wide">
            <div class="grid lg:grid-cols-3 gap-12">

                <!-- Основной текст -->
                <article class="lg:col-span-2 prose prose-gray max-w-none">
                    <div class="text-gray-700 leading-relaxed space-y-5 text-[16px]">
                        <p>
                            Мы обновили раздел аналитики: теперь дашборд показывает не только
                            общие метрики, но и детальную статистику по каждой команде и проекту.
                            В статье — обзор ключевых изменений и советы, как ими пользоваться.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 pt-4">Что нового</h2>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <i data-lucide="check-circle" class="w-5 h-5 text-[#10b981] mt-0.5 flex-shrink-0"></i>
                                <span><strong class="text-gray-900">Гибкие фильтры</strong> — можно смотреть метрики по командам, проектам и отдельным сотрудникам.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i data-lucide="check-circle" class="w-5 h-5 text-[#10b981] mt-0.5 flex-shrink-0"></i>
                                <span><strong class="text-gray-900">Экспорт в Excel</strong> — выгружайте отчёты одним кликом и делитесь с коллегами.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i data-lucide="check-circle" class="w-5 h-5 text-[#10b981] mt-0.5 flex-shrink-0"></i>
                                <span><strong class="text-gray-900">Тёмная тема графиков</strong> — для тех, кто работает в тёмном режиме.</span>
                            </li>
                        </ul>

                        <div class="my-8 p-6 rounded-2xl bg-[#ecfdf5] border border-[#a7f3d0]/60">
                            <div class="flex items-start gap-3">
                                <i data-lucide="lightbulb" class="w-5 h-5 text-[#10b981] mt-0.5 flex-shrink-0"></i>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    <strong class="text-gray-900">Совет:</strong> используйте сравнительный режим,
                                    чтобы увидеть динамику за последние 30, 90 или 365 дней.
                                </p>
                            </div>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 pt-4">Как включить</h2>
                        <p>
                            Новый дашборд доступен всем пользователям на тарифах «Оптимальный» и выше.
                            Чтобы открыть его, перейдите в раздел «Аналитика» в левом меню.
                        </p>
                        <p>
                            Если у вас возникли вопросы — напишите нам в чат поддержки или на
                            <a href="mailto:info@менеджерплюс.рф" class="text-[#10b981] font-semibold hover:underline">info@менеджерплюс.рф</a>.
                        </p>
                    </div>

                    <!-- Теги -->
                    <div class="mt-10 pt-6 border-t border-gray-100 flex flex-wrap gap-2">
                        <span class="text-sm text-gray-400 mr-2">Теги:</span>
                        <span class="inline-flex items-center bg-gray-50 text-gray-600 px-3 py-1.5 rounded-full text-xs font-medium">аналитика</span>
                        <span class="inline-flex items-center bg-gray-50 text-gray-600 px-3 py-1.5 rounded-full text-xs font-medium">дашборд</span>
                        <span class="inline-flex items-center bg-gray-50 text-gray-600 px-3 py-1.5 rounded-full text-xs font-medium">обновление</span>
                    </div>

                    <!-- Шеринг -->
                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <span class="text-sm text-gray-400">Поделиться:</span>
                        <a href="#" class="w-9 h-9 rounded-lg flex items-center justify-center bg-gray-50 text-gray-500 hover:bg-[#ecfdf5] hover:text-[#10b981] transition">
                            <i data-lucide="send" class="w-4 h-4"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-lg flex items-center justify-center bg-gray-50 text-gray-500 hover:bg-[#ecfdf5] hover:text-[#10b981] transition">
                            <i data-lucide="link" class="w-4 h-4"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-lg flex items-center justify-center bg-gray-50 text-gray-500 hover:bg-[#ecfdf5] hover:text-[#10b981] transition">
                            <i data-lucide="share-2" class="w-4 h-4"></i>
                        </a>
                    </div>
                </article>

                <!-- Сайдбар -->
                <aside class="space-y-6">
                    <!-- Автор -->
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-4">Автор</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #10b981, #059669);">ИИ</div>
                            <div>
                                <p class="font-semibold text-gray-800">Ибрагим Ингтех</p>
                                <p class="text-sm text-gray-400">Продуктовый менеджер</p>
                            </div>
                        </div>
                    </div>

                    <!-- Читайте также -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm">
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-4">Читайте также</h3>
                        <ul class="space-y-4">
                            <li>
                                <a href="{{ route('news.show', ['slug' => 'ingteh-5-let']) }}" class="group block">
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-[#10b981] transition leading-snug">
                                        ООО «Ингтех» исполнилось 5 лет
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">28 октября 2026</p>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('news.show', ['slug' => 'keys-techstart']) }}" class="group block">
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-[#10b981] transition leading-snug">
                                        Как TechStart ускорил релизы на 40%
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">15 октября 2026</p>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Подписка -->
                    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <h3 class="font-bold text-lg">Подписка на новости</h3>
                        <p class="mt-2 text-sm text-white/80">Раз в месяц — только главное об обновлениях.</p>
                        <input type="email" placeholder="Ваш email" class="mt-4 w-full px-4 py-2.5 rounded-xl bg-white/15 border border-white/20 text-white placeholder:text-white/60 focus:bg-white/25 outline-none transition text-sm" />
                        <button class="mt-3 w-full bg-white text-[#10b981] font-semibold py-2.5 rounded-xl hover:shadow-lg transition text-sm">
                            Подписаться
                        </button>
                    </div>
                </aside>

            </div>

            <!-- Навигация назад -->
            <div class="mt-12 pt-8 border-t border-gray-100">
                <a href="{{ route('news') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#10b981] hover:gap-3 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Все новости
                </a>
            </div>
        </div>
    </section>

    <!-- ===== ПОХОЖИЕ НОВОСТИ ===== -->
    <section class="py-16 bg-gray-50/50">
        <div class="container-wide">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <span class="text-[#10b981] font-semibold text-sm tracking-widest uppercase">Ещё по теме</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">Похожие новости</h2>
                </div>
                <a href="{{ route('news') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-[#10b981] hover:gap-2.5 transition-all">
                    Все новости <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
            <div class="grid md:grid-cols-2 gap-8">

                <article class="card-hover rounded-2xl overflow-hidden flex flex-col sm:flex-row">
                    <div class="image-placeholder sm:w-40" style="min-height: 160px; border-radius: 0;">
                        <div class="placeholder-content">
                            <i data-lucide="image"></i>
                        </div>
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-center gap-3 text-xs font-semibold text-[#10b981] mb-2">
                            <span class="bg-[#ecfdf5] px-2.5 py-1 rounded-full">Компания</span>
                            <time class="text-gray-400 font-normal">28 октября 2026</time>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 leading-snug">
                            <a href="{{ route('news.show', ['slug' => 'ingteh-5-let']) }}" class="hover:text-[#10b981] transition">
                                ООО «Ингтех» исполнилось 5 лет
                            </a>
                        </h3>
                    </div>
                </article>

                <article class="card-hover rounded-2xl overflow-hidden flex flex-col sm:flex-row">
                    <div class="image-placeholder sm:w-40" style="min-height: 160px; border-radius: 0;">
                        <div class="placeholder-content">
                            <i data-lucide="image"></i>
                        </div>
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-center gap-3 text-xs font-semibold text-[#10b981] mb-2">
                            <span class="bg-[#ecfdf5] px-2.5 py-1 rounded-full">Кейсы</span>
                            <time class="text-gray-400 font-normal">15 октября 2026</time>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 leading-snug">
                            <a href="{{ route('news.show', ['slug' => 'keys-techstart']) }}" class="hover:text-[#10b981] transition">
                                Как TechStart ускорил релизы на 40% с МенеджерПлюс
                            </a>
                        </h3>
                    </div>
                </article>

            </div>
        </div>
    </section>

@endsection