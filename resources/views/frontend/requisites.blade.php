@extends('layouts.main')

@section('title', 'Реквизиты — ООО «Ингтех» | МенеджерПлюс')

@section('content')

    </section>

    <!-- ===== РЕКВИЗИТЫ ===== -->
    <section class="py-16 bg-white">
        <div class="container-wide">
             <div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">
                        Реквизиты <span class="gradient-text-primary">компании</span>
                    </h2>
                    
                    <div class="mt-8 space-y-4">
                    </form>
                </div>
            <div class="grid lg:grid-cols-2 gap-10">

                <!-- Карточка организации -->
                <div class="rounded-2xl p-8 bg-white border border-gray-200/80 shadow-xl shadow-gray-200/50">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-11 h-11 rounded-xl flex items-center justify-center text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <i data-lucide="building-2" class="w-5 h-5"></i>
                        </span>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">ООО «Ингтех»</h2>
                            <p class="text-sm text-gray-400">Владелец платформы МенеджерПлюс</p>
                        </div>
                    </div>
                    <dl class="space-y-4 text-sm">
                        <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                            <dt class="text-gray-500">Полное наименование</dt>
                            <dd class="font-semibold text-gray-800 text-right">Общество с ограниченной ответственностью «Ингтех»</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                            <dt class="text-gray-500">Сокращённое наименование</dt>
                            <dd class="font-semibold text-gray-800 text-right">ООО «Ингтех»</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                            <dt class="text-gray-500">ИНН</dt>
                            <dd class="font-semibold text-gray-800 text-right">0600025663</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                            <dt class="text-gray-500">КПП</dt>
                            <dd class="font-semibold text-gray-800 text-right">060001001</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                            <dt class="text-gray-500">ОГРН</dt>
                            <dd class="font-semibold text-gray-800 text-right">1260600002860</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                            <dt class="text-gray-500">ОКВЭД (основной)</dt>
                            <dd class="font-semibold text-gray-800 text-right">62.01 — Разработка ПО</dd>
                        </div> 
                    </dl>
                </div>

                <!-- Адрес и банк -->
                <div class="space-y-6">
                    <div class="rounded-2xl p-8 bg-gray-50 border border-gray-100">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-10 h-10 rounded-lg flex items-center justify-center bg-[#ecfdf5] text-[#10b981]">
                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                            </span>
                            <h3 class="text-lg font-bold text-gray-800">Юридический адрес</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            386101, Республика Ингушетия,<br>
                            Назрановский район, с. Экажево,<br>
                            ул. Центральная, д. 1
                        </p>
                    </div>

                    <div class="rounded-2xl p-8 bg-white border border-gray-200/80 shadow-xl shadow-gray-200/50">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-10 h-10 rounded-lg flex items-center justify-center bg-[#ecfdf5] text-[#10b981]">
                                <i data-lucide="landmark" class="w-5 h-5"></i>
                            </span>
                            <h3 class="text-lg font-bold text-gray-800">Банковские реквизиты</h3>
                        </div>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4 py-1">
                                <dt class="text-gray-500">Расчётный счёт</dt>
                                <dd class="font-semibold text-gray-800">40702810860510000992</dd>
                            </div>
                            <div class="flex justify-between gap-4 py-1">
                                <dt class="text-gray-500">Банк</dt>
                                <dd class="font-semibold text-gray-800 text-right">ПАО «Сбербанк»</dd>
                            </div>
                            <div class="flex justify-between gap-4 py-1">
                                <dt class="text-gray-500">БИК</dt>
                                <dd class="font-semibold text-gray-800">040702615</dd>
                            </div>
                            <div class="flex justify-between gap-4 py-1">
                                <dt class="text-gray-500">Корр. счёт</dt>
                                <dd class="font-semibold text-gray-800">30101810907020000615</dd>
                            </div>


                            <div class="flex justify-between gap-4 py-1">
                                <dt class="text-gray-500">ИНН</dt>
                                <dd class="font-semibold text-gray-800">7707083893</dd>
                            </div>

                             <div class="flex justify-between gap-4 py-1">
                                <dt class="text-gray-500">КПП</dt>
                                <dd class="font-semibold text-gray-800">060602001</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-2xl p-8 bg-[#ecfdf5] border border-[#a7f3d0]/60">
                        <div class="flex items-center gap-3 mb-3">
                            <i data-lucide="file-text" class="w-5 h-5 text-[#10b981]"></i>
                            <h3 class="text-lg font-bold text-gray-800">Договор и оплата по счёту</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed text-sm">
                            Готовы работать по договору с юридическими лицами и ИП.
                            Напишите нам — подготовим счёт и комплект документов.
                        </p>
                        <a href="mailto:info@менеджерплюс.рф" class="inline-flex items-center gap-2 mt-4 text-[#10b981] font-semibold hover:underline">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                            info@менеджерплюс.рф
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
                    <h2 class="text-3xl sm:text-4xl font-bold">Нужны документы для оплаты?</h2>
                    <p class="mt-4 text-white/80 max-w-lg mx-auto text-lg">Свяжитесь с нами — вышлем счёт, договор и акт в течение рабочего дня.</p>
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a href="{{ route('contacts') }}" class="bg-white text-[#10b981] px-8 py-3.5 rounded-xl font-semibold hover:shadow-xl transition shadow-lg flex items-center gap-2">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                            Связаться с нами
                        </a>
                        <a href="mailto:info@менеджерплюс.рф" class="bg-white/20 text-white px-8 py-3.5 rounded-xl font-semibold hover:bg-white/30 transition border border-white/20 flex items-center gap-2">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                            Написать на email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection