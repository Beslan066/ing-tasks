@extends('layouts.main')

@section('title', 'О нас и контакты — МенеджерПлюс | ООО «Ингтех»')

@section('content')

    <!-- ===== О НАС + СТАТИСТИКА ===== -->
    <section class="py-20 bg-white">
        <div class="container-wide">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-[#10b981] font-semibold text-sm tracking-widest uppercase">О нас</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">
                        Технологии, которые <span class="gradient-text-primary">работают</span> на результат
                    </h2>
                    <p class="mt-5 text-gray-500 text-lg leading-relaxed">
                        ООО «Ингтех» — разработчик платформы «МенеджерПлюс». Мы строим современный
                        коробочный сервис для управления командами, задачами и коммуникациями.
                        Особое внимание уделяем безопасности и конфиденциальности данных.
                    </p>
                    <ul class="mt-6 space-y-3">
                        <li class="flex items-start gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-[#10b981] mt-0.5"></i>
                            <span class="text-gray-700">Российская разработка и хранение данных</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-[#10b981] mt-0.5"></i>
                            <span class="text-gray-700">Коробочное решение и бесплатный тариф</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-[#10b981] mt-0.5"></i>
                            <span class="text-gray-700">Поддержка 24/7 на тарифе «Оптимальный»</span>
                        </li>
                    </ul>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div class="text-center p-8 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-4xl font-bold text-gray-900"><span class="stat-number">15</span></p>
                        <p class="text-gray-500 mt-2 font-medium">Пользователей</p>
                    </div>
                    <div class="text-center p-8 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-4xl font-bold text-gray-900"><span class="stat-number">5+</span></p>
                        <p class="text-gray-500 mt-2 font-medium">Компаний</p>
                    </div>
                    <div class="text-center p-8 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-4xl font-bold text-gray-900"><span class="stat-number">2</span></p>
                        <p class="text-gray-500 mt-2 font-medium">Тарифов</p>
                    </div>
                    <div class="text-center p-8 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-4xl font-bold text-gray-900"><span class="stat-number">2026</span></p>
                        <p class="text-gray-500 mt-2 font-medium">Год основания</p>
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
                    <p class="mt-4 text-white/80 max-w-lg mx-auto text-lg">Бесплатная версия до 3 пользователей. Полный набор инструментов — на тарифе «Оптимальный».</p>
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a href="{{ route('register') }}" class="bg-white text-[#10b981] px-8 py-3.5 rounded-xl font-semibold hover:shadow-xl transition shadow-lg flex items-center gap-2">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                            Зарегистрироваться
                        </a>
                        <a href="{{ route('requisites') }}" class="bg-white/20 text-white px-8 py-3.5 rounded-xl font-semibold hover:bg-white/30 transition border border-white/20 flex items-center gap-2">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                            Реквизиты
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection