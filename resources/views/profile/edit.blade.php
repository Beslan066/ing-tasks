@extends('layouts.app')

@section('content')
    <div>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">

            <div class="grid grid-cols-1 lg:grid-cols-5 xl:grid-cols-4 gap-6">
                <!-- Основные настройки -->
                <div class="lg:col-span-3 xl:col-span-3 space-y-2">
                    <!-- Информация профиля -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-800 max-[500px]:text-[16px]">Личная информация</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Смена пароля -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-800 max-[500px]:text-[16px]">Безопасность</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
                  <!-- Боковая панель с аватаром и удалением аккаунта -->
                <div class="lg:col-span-2 xl:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <!-- Блок с аватаром -->
                        <div class="p-6 text-center" style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100% 100%);">
                            <div class="relative inline-block">
                                <!-- Аватар -->
                                <div class="relative group">
                                    <div
                                        class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white mx-auto">
                                        @if(Auth::user()->avatar)
                                            <img src="{{ Storage::url(Auth::user()->avatar) }}"
                                                 alt="Avatar"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center">
                                            <span class="text-white text-4xl font-bold">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                            </span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Кнопка изменения аватара -->
                                    <button onclick="document.getElementById('avatar-upload').click()"
                                            class="absolute bottom-0 right-0 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition duration-200">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </button>
                                </div>

                                <form id="avatar-form" action="{{ route('profile.avatar.update') }}" method="POST"
                                      enctype="multipart/form-data" class="hidden">
                                    @csrf
                                    @method('PATCH')
                                    <input type="file" name="avatar" id="avatar-upload" accept="image/*"
                                           onchange="this.form.submit()">
                                </form>
                            </div>

                            <div class="mt-4">
                                <h3 class="text-white font-semibold text-lg">{{ Auth::user()->name }}</h3>
                                <p class="text-blue-100 text-sm">{{ Auth::user()->email }}</p>
                            </div>

{{--                            <!-- Кнопка удаления аватара -->--}}
{{--                            @if(Auth::user()->avatar)--}}
{{--                                <div class="mt-3">--}}
{{--                                    <button type="button"--}}
{{--                                            onclick="confirmAvatarDelete()"--}}
{{--                                            class="text-sm text-blue-100 hover:text-white transition duration-200 flex items-center justify-center gap-1">--}}
{{--                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"--}}
{{--                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>--}}
{{--                                        </svg>--}}
{{--                                        Удалить аватар--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            @endif--}}


                        </div>

                        <!-- Статистика -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Роль:</span>
                                    <span class="font-semibold text-gray-800">
                                    @if(isset(Auth::user()->role))
                                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">{{auth()->user()->role->name}}</span>
                                        @endif
                                </span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Дата регистрации:</span>
                                    <span
                                        class="font-semibold text-gray-800">{{ Auth::user()->created_at->format('d.m.Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Статус:</span>
                                    <span class="flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    <span class="text-gray-800">Активен</span>
                                </span>
                                </div>
                            </div>
                            <div class="flex justify-center mt-2">
                                <form action="{{route('logout')}}" method="post">
                                    @csrf
                                    @method('post')
                                    <button  class="btn btn-secondary outline-none"></button>
                                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md
                                         px-6 font-medium text-neutral-50 transition active:scale-110" style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100% 100%);">Выйти</button>
                                </form>
                            </div>
                        </div>


                    </div>
                    <!-- Блок удаления аккаунта (под аватаром) -->
                    <div class=" mt-2">
                        <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                            <div class="flex items-center mb-3">
                                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <h4 class="font-semibold text-red-800">Удаление аккаунта</h4>
                            </div>
                            <p class="text-sm text-red-600 mb-4">Удалите свою учетную запись и все данные</p>
                            <button type="button"
                                    onclick="confirmAccountDeletion()"
                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition duration-200 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Удалить аккаунт
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Форма удаления аватара -->
    <form id="delete-avatar-form" action="{{ route('profile.avatar.delete') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Форма удаления аккаунта -->
    <form id="delete-account-form" action="{{ route('profile.destroy') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
        <input type="password" name="password" id="delete-password">
    </form>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Показываем уведомление об успешном обновлении аватара
            @if(session('avatar-status'))
            Swal.fire({
                icon: 'success',
                title: 'Успешно!',
                text: '{{ session("avatar-status") }}',
                showConfirmButton: false,
                timer: 3000
            });
            @endif

            @if(session('avatar-deleted'))
            Swal.fire({
                icon: 'success',
                title: 'Успешно!',
                text: '{{ session("avatar-deleted") }}',
                showConfirmButton: false,
                timer: 3000
            });
            @endif

            function confirmAvatarDelete() {
                Swal.fire({
                    title: 'Удалить аватар?',
                    text: 'Вы уверены, что хотите удалить свой аватар?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Да, удалить',
                    cancelButtonText: 'Отмена'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-avatar-form').submit();
                    }
                });
            }

            function confirmAccountDeletion() {
                Swal.fire({
                    title: 'Удалить аккаунт?',
                    html: `
                <p class="mb-3">Вы уверены, что хотите удалить свой аккаунт?</p>
                <p class="text-sm text-red-600">Это действие необратимо! Все ваши данные будут удалены.</p>
                <div class="mt-4">
                    <input type="password" id="password-confirm" class="swal2-input w-full px-3 py-2 border rounded-lg" placeholder="Введите ваш пароль" required>
                </div>
            `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Да, удалить аккаунт',
                    cancelButtonText: 'Отмена',
                    preConfirm: () => {
                        const password = Swal.getPopup().querySelector('#password-confirm').value;
                        if (!password) {
                            Swal.showValidationMessage('Пожалуйста, введите пароль');
                        }
                        return {password: password};
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const passwordInput = document.getElementById('delete-password');
                        passwordInput.value = result.value.password;
                        document.getElementById('delete-account-form').submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
