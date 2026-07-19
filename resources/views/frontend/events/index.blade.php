@extends('layouts.app')

@section('content')
    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
        $canManage = auth()->user()->isManager() || auth()->user()->isSupervisor();
    @endphp

    <div x-data="calendarApp()" x-init="initCalendar()">
        <!-- Хлебные крошки -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
            <nav class="hidden max-[500px]:block">
                <ol class="flex items-center gap-1.5">
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-500' }}"
                           href="{{ route('welcome') }}">
                            Главная
                            <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16">
                                <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke="" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </a>
                    </li>
                    <li class="text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-800 dark:text-white/90' }}">Календарь</li>
                </ol>
            </nav>
            <div class="max-[500px]:hidden">
                @if($backgroundEnabled && $backgroundImage)
                    <h2 class="text-3xl font-bold text-white max-[500px]:text-[26px]">Календарь событий</h2>
                @else
                    <h2 class="text-3xl font-bold text-[#16a34a] max-[500px]:text-[26px]">Календарь событий</h2>
                @endif
            </div>
            @if($canManage)
                <button @click="openCreateModal()"
                        class="flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600 transition-colors">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.2502 4.99951C9.2502 4.5853 9.58599 4.24951 10.0002 4.24951C10.4144 4.24951 10.7502 4.5853 10.7502 4.99951V9.24971H15.0006C15.4148 9.24971 15.7506 9.5855 15.7506 9.99971C15.7506 10.4139 15.4148 10.7497 15.0006 10.7497H10.7502V15.0001C10.7502 15.4143 10.4144 15.7501 10.0002 15.7501C9.58599 15.7501 9.2502 15.4143 9.2502 15.0001V10.7497H5C4.58579 10.7497 4.25 10.4139 4.25 9.99971C4.25 9.5855 4.58579 9.24971 5 9.24971H9.2502V4.99951Z"/>
                    </svg>
                    Создать событие
                </button>
            @endif
        </div>

        <!-- КАЛЕНДАРЬ -->
        <div class="w-full">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Панель управления -->
                <div class="flex flex-wrap items-center justify-between gap-4 p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <button @click="prevMonth()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button @click="nextMonth()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <button @click="goToday()"
                                class="px-4 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors border border-gray-300 dark:border-gray-600">
                            Сегодня
                        </button>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white ml-2" x-text="currentMonthName"></h2>
                    </div>
                    <div class="flex gap-1">
                        <button @click="viewMode = 'month'"
                                class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors"
                                :class="viewMode === 'month' ? 'bg-green-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'">
                            Месяц
                        </button>
                        <button @click="viewMode = 'week'"
                                class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors"
                                :class="viewMode === 'week' ? 'bg-green-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'">
                            Неделя
                        </button>
                        <button @click="viewMode = 'day'"
                                class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors"
                                :class="viewMode === 'day' ? 'bg-green-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'">
                            День
                        </button>
                    </div>
                </div>

                <!-- Сетка календаря - альтернативный вариант с flex -->
                <div class="p-4">
                    <!-- Дни недели -->
                    <div class="grid grid-cols-7 gap-1 mb-2">
                        <template x-for="day in ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']" :key="day">
                            <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">
                                <span x-text="day"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Ячейки дней - каждая неделя отдельный flex -->
                    <div class="flex flex-col gap-1">
                        <template x-for="(week, weekIndex) in calendarDays" :key="weekIndex">
                            <div class="grid grid-cols-7 gap-1">
                                <template x-for="(day, dayIndex) in week" :key="dayIndex">
                                    <div @click="day.date && openDayModal(day)"
                                         class="min-h-[100px] p-1 rounded-lg transition-all cursor-pointer"
                                         :class="{
                             'bg-green-50 dark:bg-green-900/20 border-2 border-green-500': day.isToday,
                             'bg-gray-50 dark:bg-gray-700/30': !day.isToday && day.date,
                             'opacity-40': !day.date,
                             'hover:bg-gray-100 dark:hover:bg-gray-700/50': day.date
                         }">
                                        <div class="flex justify-between items-start">
                            <span class="text-sm font-medium p-1 rounded-full"
                                  :class="{
                                      'bg-green-500 text-white w-8 h-8 flex items-center justify-center': day.isToday,
                                      'text-gray-700 dark:text-gray-300': !day.isToday && day.date,
                                      'text-gray-400 dark:text-gray-600': !day.date
                                  }"
                                  x-text="day.day"></span>
                                            <span x-show="day.eventCount > 0"
                                                  class="text-xs bg-red-500 text-white rounded-full px-1.5 py-0.5"
                                                  x-text="day.eventCount"></span>
                                        </div>
                                        <div class="mt-1 space-y-0.5">
                                            <template x-for="event in day.events" :key="event.id">
                                                <div @click.stop="openEventModal(event)"
                                                     class="text-xs px-1.5 py-0.5 rounded truncate cursor-pointer hover:opacity-80 transition-opacity"
                                                     :style="{
                                         backgroundColor: event.color || '#3B82F6',
                                         color: 'white'
                                     }"
                                                     x-text="event.title">
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- МОДАЛЬНОЕ ОКНО ПРОСМОТРА ДНЯ -->
        <div x-show="showDayModal"
             x-cloak
             class="fixed inset-0 overflow-y-auto backdrop-blur-md bg-black/50 z-50 flex items-center justify-center px-4 py-8"
             @click.away="closeDayModal()">
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-4 pt-5 pb-4 sm:p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                    <div class="flex items-start justify-between mb-4 sticky top-0 bg-white dark:bg-gray-800 pt-2 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white" x-text="selectedDay.dateFormatted"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5" x-text="selectedDay.events.length + ' событий'"></p>
                        </div>
                        <button @click="closeDayModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 p-1">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3 mt-4">
                        <template x-if="selectedDay.events.length === 0">
                            <div class="text-center py-12">
                                <svg class="w-20 h-20 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Нет событий на этот день</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Нажмите кнопку ниже, чтобы создать событие</p>
                            </div>
                        </template>

                        <template x-for="event in selectedDay.events" :key="event.id">
                            <div @click="closeDayModal(); setTimeout(() => openEventModal(event), 300)"
                                 class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer transition-colors">
                                <div class="w-1 h-full min-h-[40px] rounded-full" :style="{ backgroundColor: event.color || '#3B82F6' }"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 dark:text-white truncate" x-text="event.title"></div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <span x-text="formatEventTime(event)"></span>
                                        <span x-show="event.extendedProps?.location" class="ml-2">
                                            • <span x-text="event.extendedProps.location"></span>
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-show="event.extendedProps?.creator_name">
                                        Создал: <span x-text="event.extendedProps.creator_name"></span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400"
                                          x-text="event.extendedProps?.type_label || 'Другое'"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/30 sm:px-6 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                    @if($canManage)
                        <button @click="closeDayModal(); setTimeout(() => openCreateModal(selectedDay.date), 300)"
                                class="px-6 py-2.5 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Создать событие
                        </button>
                    @endif
                    <button @click="closeDayModal()"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Закрыть
                    </button>
                </div>
            </div>
        </div>

        <!-- МОДАЛЬНОЕ ОКНО СОЗДАНИЯ -->
        <div x-show="showCreateModal"
             x-cloak
             class="fixed inset-0 overflow-y-auto backdrop-blur-md bg-black/50 z-50 flex items-center justify-center px-4 py-8">
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-4 pt-5 pb-4 sm:p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                    <div class="flex items-start justify-between mb-4 sticky top-0 bg-white dark:bg-gray-800 pt-2 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white" x-text="modalTitle">Создание события</h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 p-1">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="saveEvent()">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Название *</label>
                                <input type="text" x-model="form.title" required
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:text-white focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none"
                                       placeholder="Введите название события">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Начало *</label>
                                    <input type="datetime-local" x-model="form.start_date" required
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:text-white focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Окончание *</label>
                                    <input type="datetime-local" x-model="form.end_date" required
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:text-white focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none">
                                </div>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" x-model="form.all_day" id="all_day"
                                       class="rounded border-gray-300 dark:border-gray-600 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                <label for="all_day" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Весь день</label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Описание</label>
                                <textarea x-model="form.description" rows="2"
                                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:text-white focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none"
                                          placeholder="Описание события"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Местоположение</label>
                                <input type="text" x-model="form.location"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:text-white focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none"
                                       placeholder="Офис, Zoom, адрес...">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Тип</label>
                                    <select x-model="form.type"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:text-white focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none">
                                        <option value="meeting">Встреча</option>
                                        <option value="deadline">Дедлайн</option>
                                        <option value="reminder">Напоминание</option>
                                        <option value="other">Другое</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Отдел</label>
                                    <select x-model="form.department_id"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:text-white focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none">
                                        <option value="">Все отделы</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Участники - красивый select -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Участники</label>

                                <div class="relative">
                                    <select x-model="form.participants" multiple
                                            class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white focus:border-green-400 focus:ring-4 focus:ring-green-100 dark:focus:ring-green-900/30 outline-none h-32 appearance-none">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}"
                                                    class="py-1.5 px-3 hover:bg-green-50 dark:hover:bg-green-900/20 cursor-pointer">
                                                {{ $user->name }}
                                                @if($user->email)
                                                    <span class="text-gray-400 dark:text-gray-500">({{ $user->email }})</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>

                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 dark:text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mt-1 flex items-center justify-between text-xs">
        <span class="text-gray-500 dark:text-gray-400">
            Выбрано: <span class="font-medium text-green-600 dark:text-green-400" x-text="form.participants.length"></span> участников
        </span>
                                    <span x-show="form.participants.length > 0"
                                          class="text-red-500 dark:text-red-400 cursor-pointer hover:underline"
                                          @click="form.participants = []">
            Очистить все
        </span>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    💡 Удерживайте <kbd class="px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs">Ctrl</kbd> для выбора нескольких участников
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Цвет</label>
                                <input type="color" x-model="form.color"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent px-3 py-1 text-sm focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none h-10">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" @click="closeModal()"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Отмена
                            </button>
                            <button type="submit"
                                    class="px-6 py-2.5 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="loading">
                                <span x-show="!loading" x-text="modalButtonText">Создать</span>
                                <span x-show="loading" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Сохранение...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- МОДАЛЬНОЕ ОКНО ПРОСМОТРА СОБЫТИЯ -->
        <div x-show="showViewModal"
             x-cloak
             class="fixed inset-0 overflow-y-auto backdrop-blur-md bg-black/50 z-50 flex items-center justify-center px-4 py-8">
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-hidden">
                <div class="px-4 pt-5 pb-4 sm:p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                    <div class="flex items-start justify-between mb-4 sticky top-0 bg-white dark:bg-gray-800 pt-2 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <span class="inline-block w-4 h-4 rounded-full" :style="{ backgroundColor: selectedEvent.color || '#6B7280' }"></span>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" x-text="selectedEvent.title">Событие</h3>
                                <!-- Статус пользователя -->
                                <span x-show="userStatus === 'creator'"
                                      class="text-xs px-2 py-0.5 rounded-full mt-1 inline-block bg-purple-100 text-purple-700">
    👑 Создатель
</span>
                                <span x-show="userStatus && userStatus !== 'invited' && userStatus !== 'creator'"
                                      class="text-xs px-2 py-0.5 rounded-full mt-1 inline-block"
                                      :class="{
                                          'bg-green-100 text-green-700': userStatus === 'confirmed',
                                          'bg-red-100 text-red-700': userStatus === 'declined',
                                          'bg-yellow-100 text-yellow-700': userStatus === 'maybe'
                                      }"
                                                                      x-text="userStatus === 'confirmed' ? '✅ Вы приняли' :
                                              userStatus === 'declined' ? '❌ Вы отклонили' :
                                              userStatus === 'maybe' ? '🤔 Вы отметили Возможно' : ''">
                                </span>
                            </div>
                        </div>
                        <button @click="closeViewModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 p-1">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3 text-sm mt-4">
                        <div class="flex items-start gap-3">
                            <span class="text-gray-500 dark:text-gray-400 w-20">Время:</span>
                            <span class="text-gray-800 dark:text-gray-200" x-text="formatEventTime(selectedEvent)"></span>
                        </div>
                        <div class="flex items-start gap-3" x-show="selectedEvent.extendedProps?.description">
                            <span class="text-gray-500 dark:text-gray-400 w-20">Описание:</span>
                            <span class="text-gray-800 dark:text-gray-200" x-text="selectedEvent.extendedProps?.description"></span>
                        </div>
                        <div class="flex items-start gap-3" x-show="selectedEvent.extendedProps?.location">
                            <span class="text-gray-500 dark:text-gray-400 w-20">Место:</span>
                            <span class="text-gray-800 dark:text-gray-200" x-text="selectedEvent.extendedProps?.location"></span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-gray-500 dark:text-gray-400 w-20">Создатель:</span>
                            <span class="text-gray-800 dark:text-gray-200" x-text="selectedEvent.extendedProps?.creator_name || 'Неизвестно'"></span>
                        </div>
                        <div class="flex items-start gap-3" x-show="selectedEvent.extendedProps?.department_name">
                            <span class="text-gray-500 dark:text-gray-400 w-20">Отдел:</span>
                            <span class="text-gray-800 dark:text-gray-200" x-text="selectedEvent.extendedProps?.department_name"></span>
                        </div>

                        <!-- УЧАСТНИКИ С ИХ СТАТУСАМИ -->
                        <div class="flex items-start gap-3">
                            <span class="text-gray-500 dark:text-gray-400 w-20">Участники:</span>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="p in selectedEvent.extendedProps?.participants || []" :key="p.id">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs"
                  :class="{
                      'bg-purple-100 text-purple-700': p.id === selectedEvent.extendedProps?.creator_id,
                      'bg-green-100 text-green-700': p.status === 'confirmed' && p.id !== selectedEvent.extendedProps?.creator_id,
                      'bg-red-100 text-red-700': p.status === 'declined',
                      'bg-yellow-100 text-yellow-700': p.status === 'maybe',
                      'bg-gray-100 text-gray-700': p.status === 'invited'
                  }"
                  x-text="p.id === selectedEvent.extendedProps?.creator_id ? p.name + ' 👑 (создатель)' :
                          p.status === 'confirmed' ? p.name + ' ✅' :
                          p.status === 'declined' ? p.name + ' ❌' :
                          p.status === 'maybe' ? p.name + ' 🤔' :
                          p.name">
            </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- КНОПКИ ОТВЕТА - показываем если пользователь участник -->
                    <div class="mt-6 flex justify-end gap-3" x-show="isParticipant && userStatus === 'invited'">
                        <button @click="respondToEvent('confirmed')"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors flex items-center gap-2">
                            ✅ Принять
                        </button>
                        <button @click="respondToEvent('maybe')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center gap-2">
                            🤔 Возможно
                        </button>
                        <button @click="respondToEvent('declined')"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors flex items-center gap-2">
                            ❌ Отказаться
                        </button>
                    </div>

                    <!-- Показываем статус если уже ответили -->
                    <div x-show="isParticipant && userStatus !== 'invited'"
                         class="mt-6 p-3 rounded-lg text-center"
                         :class="{
                     'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300': userStatus === 'confirmed',
                     'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300': userStatus === 'declined',
                     'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300': userStatus === 'maybe'
                 }">
                <span x-text="userStatus === 'confirmed' ? '✅ Вы приняли приглашение' :
                              userStatus === 'declined' ? '❌ Вы отклонили приглашение' :
                              userStatus === 'maybe' ? '🤔 Вы отметили Возможно' : ''">
                        </span>
                    </div>

                    <!-- Кнопки управления (для менеджеров) -->
                    <div class="mt-4 flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700" x-show="canManage">
                        <button @click="editEvent(selectedEvent)"
                                class="px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Редактировать
                        </button>
                        <button @click="deleteEvent(selectedEvent.id)"
                                class="px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Удалить
                        </button>
                    </div>

                    <!-- Кнопка закрытия -->
                    <div class="mt-4 flex justify-end">
                        <button @click="closeViewModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .max-h-\[90vh\] {
            max-height: 90vh;
        }

        .max-h-\[calc\(90vh-80px\)\] {
            max-height: calc(90vh - 80px);
        }

        .fixed.inset-0 {
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .max-h-60::-webkit-scrollbar {
            width: 4px;
        }
        .max-h-60::-webkit-scrollbar-track {
            background: transparent;
        }
        .max-h-60::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 2px;
        }
        .dark .max-h-60::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        /* Анимация появления */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        /* Стили для компонента с тегами */
        .absolute.z-50 {
            animation: slideDown 0.15s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .max-h-48::-webkit-scrollbar {
            width: 4px;
        }
        .max-h-48::-webkit-scrollbar-track {
            background: transparent;
        }
        .max-h-48::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 2px;
        }
        .dark .max-h-48::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            // ===== КОМПОНЕНТ ДЛЯ ВЫБОРА УЧАСТНИКОВ =====
            Alpine.data('tagSelect', () => ({
                allUsers: [],
                selectedIds: [],
                search: '',
                filteredUsers: [],
                isOpen: false,
                selectedIndex: -1,

                init(users, initialSelected) {
                    this.allUsers = users || [];
                    this.selectedIds = initialSelected || [];
                    this.filteredUsers = this.allUsers;

                    // Обновляем форму
                    this.updateForm();
                },

                filterUsers() {
                    const query = this.search.toLowerCase().trim();
                    if (!query) {
                        this.filteredUsers = this.allUsers;
                    } else {
                        this.filteredUsers = this.allUsers.filter(user =>
                            user.name.toLowerCase().includes(query) ||
                            (user.email && user.email.toLowerCase().includes(query))
                        );
                    }
                    this.selectedIndex = -1;
                },

                openDropdown() {
                    this.isOpen = true;
                    this.filterUsers();
                },

                closeDropdown() {
                    this.isOpen = false;
                    this.search = '';
                    this.selectedIndex = -1;
                },

                focusInput() {
                    this.$refs.searchInput.focus();
                    this.openDropdown();
                },

                toggleUser(id) {
                    const index = this.selectedIds.indexOf(id);
                    if (index > -1) {
                        this.selectedIds.splice(index, 1);
                    } else {
                        this.selectedIds.push(id);
                    }
                    this.updateForm();
                    this.search = '';
                    this.filterUsers();
                    this.$refs.searchInput.focus();
                },

                removeUser(id) {
                    const index = this.selectedIds.indexOf(id);
                    if (index > -1) {
                        this.selectedIds.splice(index, 1);
                        this.updateForm();
                    }
                },

                isSelected(id) {
                    return this.selectedIds.includes(id);
                },

                getUserName(id) {
                    const user = this.allUsers.find(u => u.id === id);
                    return user ? user.name : 'Неизвестный';
                },

                getInitials(name) {
                    if (!name) return '?';
                    const parts = name.trim().split(' ');
                    if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
                    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
                },

                getColor(id) {
                    const colors = [
                        '#3B82F6', '#EF4444', '#F59E0B', '#10B981', '#8B5CF6',
                        '#EC4899', '#14B8A6', '#F97316', '#6366F1', '#84CC16',
                        '#06B6D4', '#D946EF', '#22C55E', '#EAB308', '#3B82F6'
                    ];
                    return colors[id % colors.length];
                },

                selectNext() {
                    if (!this.filteredUsers.length) return;
                    this.selectedIndex = (this.selectedIndex + 1) % this.filteredUsers.length;
                    this.scrollToSelected();
                },

                selectPrevious() {
                    if (!this.filteredUsers.length) return;
                    this.selectedIndex = this.selectedIndex <= 0 ? this.filteredUsers.length - 1 : this.selectedIndex - 1;
                    this.scrollToSelected();
                },

                selectCurrent() {
                    if (this.selectedIndex >= 0 && this.selectedIndex < this.filteredUsers.length) {
                        this.toggleUser(this.filteredUsers[this.selectedIndex].id);
                        this.selectedIndex = -1;
                    }
                },

                scrollToSelected() {
                    const container = this.$el.querySelector('.max-h-48');
                    if (container) {
                        const items = container.querySelectorAll('.flex.items-center.gap-2');
                        if (items[this.selectedIndex]) {
                            items[this.selectedIndex].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                        }
                    }
                },

                updateForm() {
                    const parent = this.$el.closest('[x-data="calendarApp()"]');
                    if (parent && parent.__x) {
                        // ОБНОВЛЯЕМ ТОЛЬКО ТО, ЧТО ВЫБРАНО (без создателя)
                        parent.__x.$data.form.participants = [...this.selectedIds];
                        console.log('Updated form participants:', this.selectedIds);
                    }
                },
            }));

            // ===== ОСНОВНОЙ КОМПОНЕНТ КАЛЕНДАРЯ =====
            Alpine.data('calendarApp', () => ({
                viewMode: 'month',
                currentDate: new Date(),
                events: @json($formattedEvents),
                selectedEvent: {
                    id: null,
                    title: '',
                    color: '#3B82F6',
                    start: null,
                    end: null,
                    allDay: false,
                    extendedProps: {}
                },
                selectedDay: {
                    date: null,
                    dateFormatted: '',
                    events: [],
                    eventCount: 0
                },
                isParticipant: false,
                showDayModal: false,
                showCreateModal: false,
                showViewModal: false,
                modalTitle: 'Создание события',
                modalButtonText: 'Создать',
                isEditMode: false,
                editId: null,
                loading: false,
                userStatus: 'invited',
                form: {
                    title: '',
                    description: '',
                    type: 'meeting',
                    start_date: '',
                    end_date: '',
                    all_day: false,
                    location: '',
                    color: '#3B82F6',
                    department_id: '',
                    participants: [],
                },
                canManage: @json($canManage),
                isRefreshing: false,

                get currentMonthName() {
                    return this.currentDate.toLocaleString('ru-RU', { month: 'long', year: 'numeric' });
                },

                get calendarDays() {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth();
                    const today = new Date();

                    // Первый день месяца
                    const firstDay = new Date(year, month, 1);
                    // Сколько дней в месяце
                    const daysInMonth = new Date(year, month + 1, 0).getDate();

                    // День недели первого дня (0 - воскресенье, 1 - понедельник ... 6 - суббота)
                    let startDay = firstDay.getDay();
                    // Если воскресенье (0), то это 7
                    startDay = startDay === 0 ? 7 : startDay;
                    // Сдвигаем чтобы неделя начиналась с понедельника (понедельник = 0)
                    startDay = startDay - 1;

                    const weeks = [];
                    let currentWeek = [];
                    let dayCount = 1;

                    // Пустые ячейки до первого дня месяца
                    for (let i = 0; i < startDay; i++) {
                        currentWeek.push({
                            day: '',
                            date: null,
                            events: [],
                            eventCount: 0,
                            isToday: false
                        });
                    }

                    // Заполняем дни месяца (1, 2, 3, ...)
                    while (dayCount <= daysInMonth) {
                        const dateObj = new Date(year, month, dayCount);
                        const isToday = dateObj.toDateString() === today.toDateString();
                        const dateStr = dateObj.toISOString().split('T')[0];

                        const dayEvents = this.events.filter(e => {
                            if (!e.start) return false;
                            const eventDate = new Date(e.start);
                            return eventDate.toDateString() === dateObj.toDateString();
                        });

                        currentWeek.push({
                            day: dayCount,
                            date: dateStr,
                            events: dayEvents,
                            eventCount: dayEvents.length,
                            isToday: isToday
                        });

                        dayCount++;

                        if (currentWeek.length === 7) {
                            weeks.push(currentWeek);
                            currentWeek = [];
                        }
                    }

                    // Пустые ячейки в конце последней недели
                    while (currentWeek.length < 7) {
                        currentWeek.push({
                            day: '',
                            date: null,
                            events: [],
                            eventCount: 0,
                            isToday: false
                        });
                    }
                    if (currentWeek.length > 0) {
                        weeks.push(currentWeek);
                    }

                    // ✅ НЕ ПЕРЕВОРАЧИВАЕМ - недели уже в правильном порядке (1-я неделя сверху)
                    return weeks;
                },

                initCalendar() {},

                prevMonth() {
                    this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                    this.currentDate = new Date(this.currentDate);
                },

                nextMonth() {
                    this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                    this.currentDate = new Date(this.currentDate);
                },

                goToday() {
                    this.currentDate = new Date();
                },

                openDayModal(day) {
                    const dateObj = new Date(day.date + 'T00:00:00');
                    const dateFormatted = dateObj.toLocaleDateString('ru-RU', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });

                    this.selectedDay = {
                        date: day.date,
                        dateFormatted: dateFormatted,
                        events: day.events || [],
                        eventCount: day.events?.length || 0
                    };

                    this.showDayModal = true;
                },

                closeDayModal() {
                    this.showDayModal = false;
                },

                openCreateModal(date) {
                    this.isEditMode = false;
                    this.editId = null;
                    this.modalTitle = 'Создание события';
                    this.modalButtonText = 'Создать';

                    const now = new Date();
                    let startDate, endDate;

                    if (date) {
                        startDate = new Date(date + 'T09:00:00');
                        endDate = new Date(date + 'T10:00:00');
                    } else {
                        startDate = new Date(now);
                        startDate.setMinutes(0, 0, 0);
                        startDate.setHours(now.getHours() + 1);
                        endDate = new Date(startDate);
                        endDate.setHours(startDate.getHours() + 1);
                    }

                    this.form = {
                        title: '',
                        description: '',
                        type: 'meeting',
                        start_date: startDate.toISOString().slice(0, 16),
                        end_date: endDate.toISOString().slice(0, 16),
                        all_day: false,
                        location: '',
                        color: '#3B82F6',
                        department_id: '',
                        participants: [],
                    };

                    this.showCreateModal = true;
                },

                // ИСПРАВЛЕННЫЙ МЕТОД РЕДАКТИРОВАНИЯ
                editEvent(event) {
                    console.log('editEvent called with:', event);
                    this.showViewModal = false;

                    setTimeout(() => {
                        this.isEditMode = true;
                        this.editId = event.id;
                        this.modalTitle = 'Редактирование события';
                        this.modalButtonText = 'Сохранить';

                        let startDate = '';
                        let endDate = '';

                        if (event.start) {
                            const d = new Date(event.start);
                            if (!isNaN(d.getTime())) {
                                startDate = d.toISOString().slice(0, 16);
                            }
                        }

                        if (event.end) {
                            const d = new Date(event.end);
                            if (!isNaN(d.getTime())) {
                                endDate = d.toISOString().slice(0, 16);
                            }
                        }

                        // ✅ ПРАВИЛЬНО: Исключаем создателя из списка участников
                        const allParticipants = event.extendedProps?.participants || [];
                        const creatorId = event.extendedProps?.creator_id || {{ auth()->id() }};

                        // Фильтруем участников - убираем создателя
                        const participantIds = allParticipants
                            .filter(p => p.id !== creatorId) // Убираем создателя
                            .map(p => p.id);

                        console.log('Creator ID:', creatorId);
                        console.log('All participants:', allParticipants);
                        console.log('Filtered participants IDs:', participantIds);

                        this.form = {
                            title: event.title || '',
                            description: event.extendedProps?.description || '',
                            type: event.extendedProps?.type || 'meeting',
                            start_date: startDate,
                            end_date: endDate,
                            all_day: event.allDay || false,
                            location: event.extendedProps?.location || '',
                            color: event.color || '#3B82F6',
                            department_id: event.extendedProps?.department_id || '',
                            participants: participantIds,
                        };

                        // ✅ Обновляем компонент tagSelect
                        const tagSelectEl = document.querySelector('[x-data="tagSelect()"]');
                        if (tagSelectEl && tagSelectEl.__x) {
                            tagSelectEl.__x.$data.selectedIds = [...participantIds];
                            tagSelectEl.__x.$data.filteredUsers = tagSelectEl.__x.$data.allUsers;
                            console.log('Updated tagSelect with:', participantIds);
                        }

                        this.showCreateModal = true;
                    }, 400);
                },

                closeModal() {
                    this.showCreateModal = false;
                    this.isEditMode = false;
                    this.editId = null;
                },

                openEventModal(event) {
                    this.selectedEvent = {
                        id: event.id,
                        title: event.title,
                        color: event.color,
                        start: event.start,
                        end: event.end,
                        allDay: event.allDay,
                        extendedProps: event.extendedProps || {}
                    };

                    const currentUser = {{ auth()->id() }};
                    const creatorId = this.selectedEvent.extendedProps?.creator_id;

                    // ✅ Проверяем, является ли текущий пользователь создателем
                    if (currentUser === creatorId) {
                        this.isParticipant = true;
                        this.userStatus = 'creator';
                    } else {
                        // Ищем пользователя в списке участников
                        const participant = this.selectedEvent.extendedProps?.participants?.find(p => p.id === currentUser);
                        this.isParticipant = !!participant;
                        this.userStatus = participant?.status || 'invited';
                    }

                    console.log('Current user:', currentUser);
                    console.log('Creator ID:', creatorId);
                    console.log('User status:', this.userStatus);
                    console.log('All participants:', this.selectedEvent.extendedProps?.participants);

                    this.showViewModal = true;
                },

                closeViewModal() {
                    this.showViewModal = false;
                },

                async refreshEvents() {
                    if (this.isRefreshing) return;
                    this.isRefreshing = true;

                    try {
                        const response = await fetch('/events/list', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        if (data.success) {
                            this.events = data.events;

                            if (this.showDayModal && this.selectedDay.date) {
                                const targetDate = new Date(this.selectedDay.date + 'T00:00:00');
                                const dayEvents = this.events.filter(e => {
                                    if (!e.start) return false;
                                    const eventDate = new Date(e.start);
                                    return eventDate.toDateString() === targetDate.toDateString();
                                });

                                this.selectedDay.events = dayEvents;
                                this.selectedDay.eventCount = dayEvents.length;
                            }

                            if (this.showViewModal && this.selectedEvent.id) {
                                const updatedEvent = this.events.find(e => e.id === this.selectedEvent.id);
                                if (updatedEvent) {
                                    this.selectedEvent = {
                                        id: updatedEvent.id,
                                        title: updatedEvent.title,
                                        color: updatedEvent.color,
                                        start: updatedEvent.start,
                                        end: updatedEvent.end,
                                        allDay: updatedEvent.allDay,
                                        extendedProps: updatedEvent.extendedProps || {}
                                    };

                                    const currentUser = {{ auth()->id() }};
                                    const participant = this.selectedEvent.extendedProps?.participants?.find(p => p.id === currentUser);
                                    this.userStatus = participant?.status || 'invited';
                                    this.isParticipant = !!participant;
                                }
                            }
                        }
                    } catch (err) {
                        console.error('Error refreshing events:', err);
                    } finally {
                        this.isRefreshing = false;
                    }
                },

                saveEvent() {
                    this.loading = true;

                    // ✅ ПРЯМОЕ ЧТЕНИЕ ИЗ SELECT
                    const selectEl = document.querySelector('select[x-model="form.participants"]');
                    let participants = [];

                    if (selectEl) {
                        for (let option of selectEl.options) {
                            if (option.selected) {
                                participants.push(parseInt(option.value));
                            }
                        }
                        console.log('✅ Participants from select:', participants);
                    } else {
                        console.log('❌ Select not found');
                        participants = this.form.participants || [];
                    }

                    const url = this.isEditMode ? `/events/${this.editId}` : '/events';

                    const data = {
                        title: this.form.title,
                        description: this.form.description,
                        type: this.form.type,
                        start_date: this.form.start_date,
                        end_date: this.form.end_date,
                        all_day: this.form.all_day,
                        location: this.form.location,
                        color: this.form.color,
                        department_id: this.form.department_id,
                        participants: participants,
                    };

                    if (this.isEditMode) {
                        data._method = 'PUT';
                    }

                    console.log('=== FINAL DATA ===');
                    console.log('Participants to save:', data.participants);

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(data)
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw new Error(err.message || 'Ошибка сервера');
                                }).catch(() => {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                this.showCreateModal = false;
                                this.isEditMode = false;
                                this.editId = null;

                                this.refreshEvents().then(() => {
                                    this.showToast('success', data.message);
                                });
                            } else {
                                this.showToast('error', data.message || 'Ошибка при сохранении');
                            }
                        })
                        .catch((error) => {
                            console.error('Save error:', error);
                            this.showToast('error', error.message || 'Ошибка при сохранении');
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },

                deleteEvent(eventId) {
                    if (!confirm('Удалить событие?')) return;

                    fetch(`/events/${eventId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                this.closeViewModal();
                                this.refreshEvents().then(() => {
                                    this.showToast('success', data.message);
                                });
                            } else {
                                this.showToast('error', data.message || 'Ошибка удаления');
                            }
                        })
                        .catch((error) => {
                            console.error('Delete error:', error);
                            this.showToast('error', 'Ошибка при удалении');
                        });
                },

                respondToEvent(status) {
                    if (!this.selectedEvent.id) {
                        this.showToast('error', 'Ошибка: событие не найдено');
                        return;
                    }

                    this.loading = true;

                    fetch(`/events/${this.selectedEvent.id}/respond`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ status })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw new Error(err.message || 'Ошибка сервера');
                                }).catch(() => {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                this.refreshEvents().then(() => {
                                    const statusMessages = {
                                        'confirmed': 'Вы приняли приглашение ✅',
                                        'declined': 'Вы отклонили приглашение ❌',
                                        'maybe': 'Вы отметили "Возможно" 🤔'
                                    };
                                    this.showToast('success', statusMessages[status] || 'Ответ сохранен');
                                });
                            } else {
                                this.showToast('error', data.message || 'Ошибка при сохранении ответа');
                            }
                        })
                        .catch((error) => {
                            console.error('Respond error:', error);
                            this.showToast('error', error.message || 'Ошибка при отправке ответа');
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },

                formatEventTime(event) {
                    if (!event.start || !event.end) return 'Не указано';

                    const startDate = new Date(event.start);
                    const endDate = new Date(event.end);

                    if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                        return 'Не указано';
                    }

                    if (event.allDay) {
                        return 'Весь день';
                    }

                    const startTime = startDate.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
                    const endTime = endDate.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
                    return `${startTime} - ${endTime}`;
                },

                showToast(type, message) {
                    const toast = document.createElement('div');
                    const colors = {
                        success: 'bg-green-500',
                        error: 'bg-red-500',
                        warning: 'bg-yellow-500',
                        info: 'bg-blue-500'
                    };
                    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${colors[type] || 'bg-gray-500'} shadow-lg z-50 transition-opacity duration-300`;
                    toast.textContent = message;
                    document.body.appendChild(toast);
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        setTimeout(() => document.body.removeChild(toast), 300);
                    }, 3000);
                }
            }));
        });
    </script>
@endpush
