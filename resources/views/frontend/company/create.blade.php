@extends('layouts.app')

@section('content')
<div class="bg-white rounded-[24px] w-full max-w-3xl shadow-2xl shadow-slate-900/10 mb-8">

    <!-- Заголовок -->
    <div class="border-b border-slate-100">
        <div class="flex justify-between items-center px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
            <div>
                <h3 class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Новая компания</h3>
                <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Заполните информацию о компании</p>
            </div>
        </div>
    </div>

    <!-- Форма -->
    <form id="companyForm" action="{{ route('companies.store') }}" method="post"
          class="px-8 py-6 space-y-7 max-[500px]:px-5 max-[500px]:py-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
            <x-form.input
                name="name"
                label="Название"
                placeholder="Введите название"
                :required="true"
            />

            <x-form.input
                id="phone-input"
                name="phone"
                type="tel"
                label="Номер телефона"
                placeholder="7(***)***-**-**"
                :required="true"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
            <x-form.input-select
                name="user_id"
                label="Руководитель"
                :required="true"
                :options="[auth()->id() => auth()->user()->name]"
                :selected="auth()->id()"
            />
        </div>

        <!-- Кнопки действий -->
        <div class="flex justify-end items-center gap-3 pt-6 border-t border-slate-100 max-[500px]:justify-center max-[500px]:flex-col-reverse max-[500px]:gap-3">
            <x-ui.button
                type="button"
                variant="secondary"
                onclick="window.location.href='{{ route('companies.index') }}'"
            >
                Отмена
            </x-ui.button>

            <x-ui.button
                type="submit"
                variant="primary"
                icon="fas fa-plus"
            >
                Создать компанию
            </x-ui.button>
        </div>
    </form>
</div>
@endsection
