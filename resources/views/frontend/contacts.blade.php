@extends('layouts.main')

@section('title', 'О нас и контакты — МенеджерПлюс | ООО «Ингтех»')

@section('content')

    <!-- ===== КОНТАКТЫ + ФОРМА ===== -->
    <section class="py-20 bg-gray-50/50">
        <div class="container-wide">
            <div class="grid lg:grid-cols-2 gap-16">
                <div>
                    <span class="text-[#10b981] font-semibold text-sm tracking-widest uppercase">Контакты</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">
                        Оставьте <span class="gradient-text-primary">заявку</span>
                    </h2>
                    <p class="mt-4 text-gray-500 text-lg leading-relaxed">
                        Ответим в течение рабочего дня. Поможем с выбором тарифа,
                        демонстрацией и документами для оплаты.
                    </p>
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
                        <p class="text-gray-500 mt-2">386101, Республика Ингушетия, Назрановский район, с. Экажево, ул. Центральная, д. 1.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-8 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">Email</h3>
                        <a href="mailto:info@менеджерплюс.рф" class="text-[#10b981] font-semibold hover:underline mt-2 block">info@менеджерплюс.рф</a>
                    </div>
                    <div class="bg-white rounded-2xl p-8 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">Телефон</h3>
                        <a href="tel:+79067913131" class="text-[#10b981] font-semibold hover:underline mt-2 block">+7 906 791 31 31</a>
                    </div>
                    <div class="bg-white rounded-2xl p-8 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">Реквизиты</h3>
                        <p class="text-gray-500 mt-2">ООО «Ингтех», ИНН 0600000000, ОГРН 1200000000000</p>
                        <a href="{{ route('requisites') }}" class="inline-flex items-center gap-2 mt-3 text-[#10b981] font-semibold hover:underline">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            Полные реквизиты
                        </a>
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