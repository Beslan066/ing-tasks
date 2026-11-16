@extends('layouts.main')

@section('title', 'Новости — МенеджерПлюс | ООО «Ингтех»')

@section('content')

    <!-- ===== HERO ===== -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10" style="background: radial-gradient(ellipse at 30% 20%, rgba(16, 185, 129, 0.06) 0%, transparent 60%), radial-gradient(ellipse at 70% 80%, rgba(16, 185, 129, 0.04) 0%, transparent 50%);"></div>
        <div class="container-wide pt-16 pb-10 lg:pt-24 lg:pb-12">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 bg-[#ecfdf5] text-[#10b981] text-xs font-semibold px-4 py-1.5 rounded-full border border-[#a7f3d0]/60 mb-6">
                    <span class="w-2 h-2 rounded-full" style="background: #10b981;"></span>
                    Блог и обновления продукта
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 leading-[1.1]">
                    Новости <span class="gradient-text-primary">компании</span>
                </h1>
                <p class="mt-6 text-lg text-gray-500 max-w-xl leading-relaxed">
                    Релизы, обновления платформы, кейсы клиентов и полезные материалы
                    о проектном управлении — всё в одном месте.
                </p>
            </div>
        </div>
    </section>

    <!-- ===== СПИСОК НОВОСТЕЙ ===== -->
    <section class="py-16 bg-white">
        <div class="container-wide">

            {{-- Фильтр по категориям (опционально) --}}
            <div class="flex flex-wrap gap-3 mb-10">
                <a href="{{ route('news') }}" class="inline-flex items-center gap-2 bg-[#ecfdf5] text-[#10b981] px-4 py-2 rounded-full text-sm font-medium border border-[#a7f3d0]/60">
                    Все
                </a>
                <a href="{{ route('news', ['category' => 'product']) }}" class="inline-flex items-center gap-2 bg-gray-50 text-gray-600 px-4 py-2 rounded-full text-sm font-medium hover:text-[#10b981] hover:bg-[#ecfdf5] transition">
                    Продукт
                </a>
                <a href="{{ route('news', ['category' => 'company']) }}" class="inline-flex items-center gap-2 bg-gray-50 text-gray-600 px-4 py-2 rounded-full text-sm font-medium hover:text-[#10b981] hover:bg-[#ecfdf5] transition">
                    Компания
                </a>
                <a href="{{ route('news', ['category' => 'cases']) }}" class="inline-flex items-center gap-2 bg-gray-50 text-gray-600 px-4 py-2 rounded-full text-sm font-medium hover:text-[#10b981] hover:bg-[#ecfdf5] transition">
                    Кейсы
                </a>
            </div>

            {{-- Сетка новостей --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                {{-- Карточка 1 --}}
                <article class="card-hover rounded-2xl overflow-hidden flex flex-col">
                    <div class="image-placeholder" style="min-height: 200px; border-radius: 0;">
                        <div class="placeholder-content">
                            <i data-lucide="newspaper"></i>
                            <span>Обложка новости</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex items-center gap-3 text-xs font-semibold text-[#10b981] mb-2">
                            <span class="bg-[#ecfdf5] px-2.5 py-1 rounded-full">Продукт</span>
                            <time class="text-gray-400 font-normal">12 ноября 2026</time>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 leading-snug">
                            <a href="{{ route('news.show', ['slug' => 'novyy-dashboard-analitiki']) }}" class="hover:text-[#10b981] transition">
                                Новый дашборд аналитики: больше метрик и гибкие отчёты
                            </a>
                        </h2>
                        <p class="mt-3 text-sm text-gray-500 leading-relaxed flex-1">
                            Рассказываем, что изменилось в разделе аналитики: новые графики,
                            фильтры по командам и экспорт отчётов в Excel.
                        </p>
                        <a href="{{ route('news.show', ['slug' => 'novyy-dashboard-analitiki']) }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-[#10b981] hover:gap-2.5 transition-all">
                            Читать далее <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </article>

                {{-- Карточка 2 --}}
                <article class="card-hover rounded-2xl overflow-hidden flex flex-col">
                    <div class="image-placeholder" style="min-height: 200px; border-radius: 0;">
                        <div class="placeholder-content">
                            <i data-lucide="newspaper"></i>
                            <span>Обложка новости</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex items-center gap-3 text-xs font-semibold text-[#10b981] mb-2">
                            <span class="bg-[#ecfdf5] px-2.5 py-1 rounded-full">Компания</span>
                            <time class="text-gray-400 font-normal">28 октября 2026</time>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 leading-snug">
                            <a href="{{ route('news.show', ['slug' => 'ingteh-5-let']) }}" class="hover:text-[#10b981] transition">
                                ООО «Ингтех» исполнилось 5 лет
                            </a>
                        </h2>
                        <p class="mt-3 text-sm text-gray-500 leading-relaxed flex-1">
                            Подводим итоги пяти лет работы: от небольшой команды из Экажево
                            до платформы с тысячами пользователей по всей России.
                        </p>
                        <a href="{{ route('news.show', ['slug' => 'ingteh-5-let']) }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-[#10b981] hover:gap-2.5 transition-all">
                            Читать далее <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </article>

                {{-- Карточка 3 --}}
                <article class="card-hover rounded-2xl overflow-hidden flex flex-col">
                    <div class="image-placeholder" style="min-height: 200px; border-radius: 0;">
                        <div class="placeholder-content">
                            <i data-lucide="newspaper"></i>
                            <span>Обложка новости</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex items-center gap-3 text-xs font-semibold text-[#10b981] mb-2">
                            <span class="bg-[#ecfdf5] px-2.5 py-1 rounded-full">Кейсы</span>
                            <time class="text-gray-400 font-normal">15 октября 2026</time>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 leading-snug">
                            <a href="{{ route('news.show', ['slug' => 'keys-techstart']) }}" class="hover:text-[#10b981] transition">
                                Как TechStart ускорил релизы на 40% с МенеджерПлюс
                            </a>
                        </h2>
                        <p class="mt-3 text-sm text-gray-500 leading-relaxed flex-1">
                            Разбираем реальный кейс: какие инструменты внедрили,
                            с какими трудностями столкнулись и какой результат получили.
                        </p>
                        <a href="{{ route('news.show', ['slug' => 'keys-techstart']) }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-[#10b981] hover:gap-2.5 transition-all">
                            Читать далее <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </article>

            </div>

            {{-- Пагинация (пример; при подключении БД замените на $posts->links()) --}}
            <div class="mt-12 flex justify-center">
                <nav class="flex items-center gap-2 text-sm">
                    <span class="px-4 py-2 rounded-xl bg-[#ecfdf5] text-[#10b981] font-semibold">1</span>
                    <a href="#" class="px-4 py-2 rounded-xl text-gray-500 hover:bg-gray-50 transition">2</a>
                    <a href="#" class="px-4 py-2 rounded-xl text-gray-500 hover:bg-gray-50 transition">3</a>
                    <a href="#" class="px-4 py-2 rounded-xl text-gray-500 hover:bg-gray-50 transition flex items-center gap-1">
                        Далее <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </nav>
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
                    <h2 class="text-3xl sm:text-4xl font-bold">Не пропускайте обновления</h2>
                    <p class="mt-4 text-white/80 max-w-lg mx-auto text-lg">Подпишитесь на рассылку — раз в месяц присылаем только главное.</p>
                    <div class="mt-8 flex justify-center">
                        <div class="flex w-full max-w-md">
                            <input type="email" placeholder="Ваш email" class="flex-1 px-5 py-3.5 rounded-l-xl border border-r-0 border-white/20 bg-white/15 text-white placeholder:text-white/60 focus:bg-white/25 outline-none transition" />
                            <button class="bg-white text-[#10b981] px-6 py-3.5 rounded-r-xl font-semibold hover:shadow-xl transition">
                                Подписаться
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection