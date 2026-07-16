{{-- resources/views/frontend/chat/index.blade.php --}}
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .chat-container {
            height: calc(100vh - 186px);
        }
        .message-list {
            scroll-behavior: smooth;
        }
        .message-item {
            transition: background-color 0.2s;
        }
        .message-item:hover {
            background-color: rgba(0,0,0,0.02);
        }
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 2px;
        }
        .typing-indicator span {
            width: 4px;
            height: 4px;
            background-color: #6B7280;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }
        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-4px); }
        }
        .online-indicator {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
        }
        .online { background-color: #10B981; }
        .away { background-color: #F59E0B; }
        .offline { background-color: #9CA3AF; }
        .file-attachment {
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 8px 12px;
            background: #F9FAFB;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .file-attachment:hover {
            background: #F3F4F6;
        }
        .message-input:focus {
            outline: none;
            box-shadow: none;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .chat-sidebar {
            transition: all 0.3s ease;
        }

        @media (max-width: 1280px) {
            .chat-sidebar-mobile {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 50;
                width: 100%;
                max-width: 380px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .chat-sidebar-mobile.open {
                transform: translateX(0);
            }

            .chat-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 40;
                display: none;
            }

            .chat-overlay.open {
                display: block;
            }
        }

        .message-image {
            max-height: 300px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 8px;
        }

        .reaction-emoji {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 12px;
            background: #f3f4f6;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .reaction-emoji:hover {
            background: #e5e7eb;
        }

        .reaction-emoji.active {
            background: #d1fae5;
            border: 1px solid #10b981;
        }

        .message-actions {
            opacity: 0;
            transition: opacity 0.2s;
        }

        .message-item:hover .message-actions {
            opacity: 1;
        }

        .dropdown-menu {
            transform-origin: top right;
            transition: all 0.2s;
        }

        .message-status {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            font-size: 12px;
        }

        .message-status .fa-check {
            transition: all 0.3s ease;
        }

        .message-status .fa-check.text-blue-400 {
            color: #60A5FA;
        }

        .message-status .fa-check.text-blue-600 {
            color: #2563EB;
        }

        .message-status .fa-check.text-gray-300 {
            color: #D1D5DB;
        }

        .status-tooltip {
            position: absolute;
            bottom: 100%;
            right: 0;
            background: #1F2937;
            color: white;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 4px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
        }

        .message-footer:hover .status-tooltip {
            opacity: 1;
        }
    </style>
@endpush

@section('content')
    <div class="chat-container overflow-hidden"
         x-data="chatApp()"
         x-init="init()"
         x-cloak>

        <div class="chat-overlay"
             :class="{'open': sidebarOpen}"
             @click="sidebarOpen = false"></div>

        <div class="flex h-full flex-col gap-6 xl:flex-row xl:gap-5">
            <div
                class="chat-sidebar chat-sidebar-mobile flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white xl:flex xl:w-1/4 dark:border-gray-800 dark:bg-white/[0.03]"
                :class="{'open': sidebarOpen, 'hidden xl:flex': activeChat, 'flex': !activeChat}">

                <div class="sticky px-4 pt-4 pb-4 sm:px-5 sm:pt-5 xl:pb-0">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-theme-xl font-semibold text-gray-800 sm:text-2xl dark:text-white/90">
                                Чаты
                                <span x-show="unreadTotal > 0"
                                      class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-green-500 rounded-full">
                                    <span x-text="unreadTotal"></span>
                                </span>
                            </h3>
                        </div>

                        <div x-data="{openMenu: false}" class="relative">
                            <button @click="openMenu = !openMenu"
                                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="openMenu"
                                 @click.outside="openMenu = false"
                                 x-cloak
                                 class="absolute right-0 top-full z-40 w-56 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-800">
                                <button @click="showNewChatModal = true; openMenu = false"
                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                                    <i class="fas fa-plus w-5"></i>
                                    Новый чат
                                </button>
                                <button @click="showNewGroupModal = true; openMenu = false"
                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                                    <i class="fas fa-users w-5"></i>
                                    Создать группу
                                </button>

                                @if(auth()->user()->isCompanyOwner())
                                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                    <button @click="createCompanyChat(); openMenu = false"
                                            class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                                            :disabled="hasCompanyChat"
                                            :class="{'opacity-50 cursor-not-allowed': hasCompanyChat}">
                                        <i class="fas fa-building w-5"></i>
                                        <span
                                            x-text="hasCompanyChat ? 'Общий чат уже создан' : 'Общий чат компании'"></span>
                                    </button>
                                @endif

                                @if(auth()->user()->isLeader() || auth()->user()->isManagerRole())
                                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                    <button @click="showDepartmentChats = !showDepartmentChats"
                                            class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                                        <i class="fas fa-sitemap w-5"></i>
                                        Чат отдела
                                        <i class="fas fa-chevron-down ml-auto text-xs transition-transform"
                                           :class="{'rotate-180': showDepartmentChats}"></i>
                                    </button>
                                    <div x-show="showDepartmentChats" x-cloak class="ml-4 space-y-1">
                                        <template x-for="dept in departments" :key="dept.id">
                                            <button @click="createDepartmentChat(dept.id)"
                                                    class="flex w-full items-center gap-2 rounded-lg px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                                                <i class="fas fa-users text-xs w-4"></i>
                                                <span x-text="dept.name"></span>
                                            </button>
                                        </template>
                                        <p x-show="departments.length === 0"
                                           class="text-xs text-gray-400 px-3 py-1">
                                            Нет доступных отделов
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text"
                               x-model="searchQuery"
                               @input="filterChats()"
                               placeholder="Поиск чатов..."
                               class="w-full rounded-lg border-2 border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 outline-none placeholder:text-gray-400 focus:border-green-500 focus:ring-4 focus:ring-green-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    </div>
                </div>

                <div class="flex-1 overflow-auto px-4 pb-4 sm:px-5 custom-scrollbar">
                    <template x-if="loading">
                        <div class="flex justify-center py-8">
                            <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                        </div>
                    </template>

                    <template x-if="!loading && filteredChats.length === 0">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-comments text-4xl mb-2 opacity-50"></i>
                            <p>Нет чатов</p>
                            <p class="text-sm mt-1">Начните новый чат или создайте группу</p>
                        </div>
                    </template>

                    <template x-for="chat in filteredChats" :key="chat.id">
                        <div @click="selectChat(chat)"
                             class="flex cursor-pointer items-center gap-3 rounded-lg p-3 mb-1 hover:bg-gray-100 dark:hover:bg-white/[0.03] transition-colors"
                             :class="{'bg-gray-100 dark:bg-white/[0.03]': activeChat?.id === chat.id}">

                            <div class="relative flex-shrink-0">
                                <div class="h-12 w-12 rounded-full overflow-hidden bg-gray-200">
                                    <template x-if="chat.type === 'private'">
                                        <div class="h-full w-full">
                                            <template x-if="chat.users && chat.users[0] && chat.users[0].avatar">
                                                <img :src="chat.users[0].avatar" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="!chat.users || !chat.users[0] || !chat.users[0].avatar">
                                                <div class="h-full w-full flex items-center justify-center text-lg font-medium text-white"
                                                     :class="chat.users && chat.users[0] ? chat.users[0].avatar_color || 'bg-gray-500' : 'bg-gray-500'">
                                                    <span x-text="chat.users && chat.users[0] ? chat.users[0].initials || '?' : '?'"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="chat.type !== 'private'">
                                        <div class="h-full w-full flex items-center justify-center text-white text-2xl"
                                             :style="'background: ' + (chat.type === 'company' ? 'linear-gradient(135deg, #3B82F6, #1D4ED8)' : 'linear-gradient(135deg, #10B981, #047857)')">
                                            <i class="fas"
                                               :class="chat.type === 'company' ? 'fa-building' : (chat.type === 'department' ? 'fa-sitemap' : 'fa-users')">
                                            </i>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <h5 class="text-sm font-medium text-gray-800 truncate dark:text-white/90"
                                        x-text="chat.display_name || chat.name || 'Чат'"></h5>
                                    <span class="text-xs text-gray-400 flex-shrink-0 ml-2" x-text="formatTime(chat.updated_at)"></span>
                                </div>

                                <p class="text-xs text-gray-500 truncate dark:text-gray-400 mt-0.5">
                                    <template x-if="chat.last_message">
                                        <span>
                                            <span x-text="chat.last_message.user?.name + ': '"
                                                  x-show="chat.type !== 'private'"></span>
                                            <span x-text="chat.last_message.content || 'Файл'"></span>
                                        </span>
                                    </template>
                                    <template x-if="!chat.last_message">
                                        <span>Нет сообщений</span>
                                    </template>
                                </p>

                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-xs text-gray-400 truncate max-w-[150px]"
                                          x-text="chat.users?.map(u => u.name).join(', ')"></span>

                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <span x-show="chat.unread_count > 0"
                                              class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-green-500 rounded-full"
                                              x-text="chat.unread_count"></span>

                                        <span x-show="chat.pivot?.is_muted" class="text-gray-400">
                                            <i class="fas fa-volume-mute text-xs"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div
                class="flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] xl:w-3/4 relative"
                :class="{'hidden xl:flex': !activeChat, 'flex': activeChat}">

                <template x-if="!activeChat">
                    <div class="flex flex-col items-center justify-center h-full text-gray-400">
                        <i class="fas fa-comments text-6xl mb-4 text-green-200"></i>
                        <p class="text-lg font-medium text-gray-600">Выберите чат для начала общения</p>
                        <p class="text-sm mt-1">Или создайте новый чат в меню слева</p>
                    </div>
                </template>

                <template x-if="activeChat">
                    <div class="flex flex-col h-full">
                        <div
                            class="sticky flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800 bg-white dark:bg-gray-900 z-10">
                            <div class="flex items-center gap-3">
                                <button @click="closeChat()"
                                        class="xl:hidden mr-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors">
                                    <i class="fas fa-arrow-left text-gray-600"></i>
                                </button>

                                <div class="relative flex-shrink-0">
                                    <template x-if="activeChat.type === 'private'">
                                        <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200">
                                            <template x-if="activeChat.users && activeChat.users[0] && activeChat.users[0].avatar">
                                                <img :src="activeChat.users[0].avatar" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="!colleague.avatar">
                                                <div class="h-full w-full flex items-center justify-center text-sm font-medium text-white"
                                                     :class="colleague.avatar_color || 'bg-gray-500'">
                                                    <span x-text="colleague.initials"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="activeChat.type !== 'private'">
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center text-white"
                                             :style="'background: ' + (activeChat.type === 'company' ? 'linear-gradient(135deg, #3B82F6, #1D4ED8)' : 'linear-gradient(135deg, #10B981, #047857)')">
                                            <i class="fas text-lg"
                                               :class="activeChat.type === 'company' ? 'fa-building' : (activeChat.type === 'department' ? 'fa-sitemap' : 'fa-users')">
                                            </i>
                                        </div>
                                    </template>

                                    <template x-if="activeChat.type === 'private' && activeChat.users && activeChat.users[0]">
                                        <span class="online-indicator w-3 h-3"
                                              :class="activeChat.users[0].is_online ? 'online' : 'offline'"></span>
                                    </template>
                                </div>

                                <div>
                                    <h5 class="text-sm font-medium text-gray-800 dark:text-white/90"
                                        x-text="activeChat.display_name || activeChat.name || 'Чат'"></h5>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        <span x-show="activeChat.type === 'private' && activeChat.users && activeChat.users[0]"
                                              x-text="activeChat.users[0].is_online ? 'В сети' : (activeChat.users[0].last_activity ? 'Был(а) ' + formatTime(activeChat.users[0].last_activity) : 'Не в сети')"></span>
                                        <span x-show="activeChat.type !== 'private'">
                                        <span x-text="activeChat.users?.length + ' участников'"></span>
                                        <span x-show="activeChat.type === 'department'" class="ml-1 text-green-500">
                                            <i class="fas fa-check-circle"></i> Отдел
                                        </span>
                                            <span x-show="activeChat.type === 'company'" class="ml-1 text-blue-500">
                                                <i class="fas fa-check-circle"></i> Компания
                                            </span>
                                        </span>
                                        <span x-show="activeChat.type === 'company'" class="ml-2 text-xs text-gray-400">
                                        <span x-text="activeChat.users?.length || 0"></span> участников</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1">
                                <button @click="toggleMute(activeChat)"
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white/90 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                        :class="{'text-green-500': activeChat.pivot?.is_muted}">
                                    <i class="fas" :class="activeChat.pivot?.is_muted ? 'fa-volume-off' : 'fa-volume-up'"></i>
                                </button>

                                <button @click="toggleChatInfo()"
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white/90 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                        :class="{'bg-gray-100 dark:bg-gray-800': showChatInfo}">
                                    <i class="fas fa-info-circle"></i>
                                </button>

                                <div x-data="{openMenu: false}" class="relative">
                                    <button @click="openMenu = !openMenu"
                                            class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white/90 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div x-show="openMenu"
                                         @click.outside="openMenu = false"
                                         x-cloak
                                         class="absolute right-0 top-full z-40 w-48 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-800">

                                        <template
                                            x-if="activeChat.type !== 'private' && (activeChat.pivot?.role === 'admin' || isLeader || isCompanyOwner)">
                                            <button @click="showAddUsersModal = true; openMenu = false"
                                                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                                                <i class="fas fa-user-plus w-5"></i>
                                                Добавить участников
                                            </button>
                                        </template>

                                        <button @click="leaveChat(activeChat); openMenu = false"
                                                class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                                            <i class="fas fa-sign-out-alt w-5"></i>
                                            Покинуть чат
                                        </button>

                                        <template
                                            x-if="(activeChat.pivot?.role === 'admin' || isLeader || isCompanyOwner)">
                                            <button @click="deleteChat(activeChat); openMenu = false"
                                                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                                                <i class="fas fa-trash w-5"></i>
                                                Удалить чат
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 overflow-auto p-5 space-y-3 custom-scrollbar"
                             x-ref="messagesContainer"
                             @scroll="checkScroll()">

                            <div class="text-xs text-gray-400 text-center py-1"
                                 x-show="!loadingMessages">
                                Сообщений: <span x-text="messages ? messages.length : 0"></span>
                            </div>

                            <template x-if="loadingMessages">
                                <div class="flex justify-center py-4">
                                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                                </div>
                            </template>

                            <template x-if="!loadingMessages && (!messages || messages.length === 0)">
                                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                                    <i class="fas fa-comment text-4xl mb-3 opacity-30"></i>
                                    <p class="text-sm">Нет сообщений</p>
                                    <p class="text-xs mt-1">Начните общение!</p>
                                </div>
                            </template>

                            <template x-for="(message, index) in messages" :key="message.id">
                                <div>
                                    <template x-if="message.parent_id">
                                        <div class="flex" :class="message.user_id === userId ? 'justify-end' : 'justify-start'">
                                            <div class="max-w-[75%] mb-1">
                                                <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1"
                                                     :class="message.user_id === userId ? 'justify-end' : 'justify-start'">
                                                    <i class="fas fa-reply text-gray-400 text-xs"></i>
                                                    <span>Ответ <span x-text="message.user_id === userId ? 'на своё' : 'на сообщение от'"></span>
                            <span class="font-medium" x-text="message.user_id !== userId ? getMessageAuthor(message.parent_id) : ''"></span>
                        </span>
                                                </div>
                                                <div class="rounded-lg px-3 py-1.5 text-sm bg-gray-50 dark:bg-gray-700/50 border-l-2 border-gray-300 dark:border-gray-600"
                                                     :class="message.user_id === userId ? 'text-right' : 'text-left'">
                                                    <p class="truncate text-gray-600 dark:text-gray-400 text-xs" x-text="getReplyContent(message.parent_id)"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="flex message-item"
                                         :class="message.user_id === userId ? 'justify-end' : 'justify-start'">
                                        <div class="max-w-[75%]"
                                             :class="message.user_id === userId ? 'text-right' : 'text-left'">

                                            <template x-if="message.type === 'system'">
                                                <div class="text-center text-xs text-gray-500 my-2">
                        <span class="bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full"
                              x-text="message.content"></span>
                                                </div>
                                            </template>

                                            <template x-if="message.type !== 'system'">
                                                <div>
                                                    <p x-show="activeChat.type !== 'private' && message.user_id !== userId"
                                                       class="text-xs text-gray-500 mb-1 ml-2"
                                                       x-text="message.user?.name"></p>

                                                    <div class="rounded-2xl px-4 py-2.5 break-words relative group shadow-sm"
                                                         :class="message.user_id === userId ?
                                'bg-green-500 text-white rounded-br-none' :
                                'bg-gray-100 dark:bg-white/5 rounded-bl-none'">

                                                        <template x-if="message.type === 'file' || message.type === 'image'">
                                                            <div class="space-y-2">
                                                                <template x-if="message.type === 'image'">
                                                                    <img :src="message.file_url"
                                                                         :alt="message.file_name"
                                                                         class="message-image w-full max-w-sm cursor-pointer rounded-lg"
                                                                         @click="openImageViewer(message)">
                                                                </template>

                                                                <div class="file-attachment flex items-center gap-3"
                                                                     @click="downloadFile(message)">
                                                                    <i :class="'fas ' + (message.file_icon || 'fa-file') + ' text-2xl'"></i>
                                                                    <div class="flex-1 min-w-0">
                                                                        <p class="text-sm font-medium truncate"
                                                                           :class="message.user_id === userId ? 'text-white' : 'text-gray-700'"
                                                                           x-text="message.file_name || 'Файл'"></p>
                                                                        <p class="text-xs opacity-75"
                                                                           x-text="message.formatted_file_size || ''"></p>
                                                                    </div>
                                                                    <i class="fas fa-download"></i>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <template x-if="message.type === 'text' || !message.type">
                                                            <p class="text-sm whitespace-pre-wrap break-words"
                                                               x-text="message.content"></p>
                                                        </template>

                                                        <span x-show="message.is_edited"
                                                              class="text-xs opacity-60 mt-1 block">
                                (ред.)
                            </span>
                                                    </div>

                                                    <div class="flex items-center gap-2 mt-1 text-xs text-gray-400 px-1">
                                                        <span x-text="formatTime(message.created_at)"></span>

                                                        <button @click="replyToMessage(message)"
                                                                class="text-gray-400 hover:text-green-500 transition-colors p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700"
                                                                title="Ответить">
                                                            <i class="fas fa-reply text-xs"></i>
                                                        </button>

                                                        <template x-if="message.user_id === userId">
                                                            <div class="flex items-center gap-0.5" title="Статус сообщения">
                                                                <i class="fas fa-check text-xs"
                                                                   :class="message.status === 'delivered' || message.status === 'read' ? 'text-blue-400' : 'text-gray-300'"></i>
                                                                <i class="fas fa-check text-xs"
                                                                   :class="message.status === 'read' ? 'text-blue-600' : 'text-gray-300'"></i>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="typingUsers.length > 0"
                                 class="flex items-center gap-2 text-gray-500 px-2">
                                <div class="typing-indicator">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="text-xs" x-text="getTypingText()"></span>
                            </div>
                        </div>

                        <button x-show="showScrollButton"
                                @click="scrollToBottom()"
                                class="absolute bottom-24 right-8 bg-green-500 text-white rounded-full p-3 shadow-lg hover:bg-green-600 transition z-10 animate-bounce">
                            <i class="fas fa-arrow-down"></i>
                        </button>

                        <div
                            class="sticky bottom-0 border-t border-gray-200 p-4 dark:border-gray-800 bg-white dark:bg-gray-900">
                            <div x-show="replyTo"
                                 class="mb-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border-l-4 border-green-500 flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Ответ на сообщение от <span x-text="replyTo.user?.name"></span>
                                    </p>
                                    <p class="text-sm truncate" x-text="replyTo.content"></p>
                                </div>
                                <button @click="cancelReply()"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-1">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <form @submit.prevent="sendMessage" class="flex items-end gap-2">
                                <button type="button"
                                        @click="triggerFileUpload"
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white/90 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                        :disabled="sending">
                                    <i class="fas fa-paperclip"></i>
                                </button>

                                <input type="file"
                                       x-ref="fileInput"
                                       @change="uploadFile"
                                       class="hidden"
                                       accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">

                                <div class="relative flex-1">
                                    <textarea x-model="newMessage"
                                              @keydown.enter.prevent="handleEnterKey"
                                              @input="handleTyping"
                                              rows="1"
                                              placeholder="Написать сообщение..."
                                              class="message-input w-full border-0 bg-transparent px-4 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-0 dark:text-white/90 resize-none max-h-32"
                                              style="max-height: 120px; min-height: 42px;"
                                              :disabled="sending"></textarea>

                                    <div x-show="selectedFile"
                                         class="absolute left-4 bottom-full mb-2 bg-gray-100 dark:bg-gray-800 rounded-lg p-2 flex items-center gap-2 text-sm">
                                        <i class="fas fa-file text-gray-500"></i>
                                        <span x-text="selectedFile ? selectedFile.name : ''"></span>
                                        <button @click="selectedFile = null" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit"
                                        :disabled="(!newMessage.trim() && !selectedFile) || sending"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-500 text-white hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>

                        <div x-show="showChatInfo"
                             x-cloak
                             class="absolute right-0 top-0 bottom-0 w-80 bg-white border-l border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-4 overflow-auto shadow-xl z-20"
                             x-transition:enter="transition transform duration-300"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transition transform duration-300"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full">

                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold">Информация о чате</h4>
                                <button @click="showChatInfo = false"
                                        class="text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-full transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div class="text-center">
                                    <div class="w-20 h-20 mx-auto rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                                        <template x-if="activeChat.type === 'private' && activeChat.users && activeChat.users[0]">
                                            <template x-if="activeChat.users[0].avatar">
                                                <img :src="activeChat.users[0].avatar" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="!activeChat.users[0].avatar">
                                                <div class="h-full w-full flex items-center justify-center text-2xl font-medium text-white bg-blue-500">
                                                    <span x-text="activeChat.users[0].initials"></span>
                                                </div>
                                            </template>
                                        </template>
                                        <template x-if="activeChat.type !== 'private'">
                                            <div
                                                class="h-full w-full flex items-center justify-center bg-gradient-to-br from-green-400 to-green-600 text-white">
                                                <i class="fas text-3xl"
                                                   :class="activeChat.type === 'company' ? 'fa-building' : 'fa-users'"></i>
                                            </div>
                                        </template>
                                    </div>
                                    <h5 class="mt-2 font-medium" x-text="activeChat.display_name || activeChat.name"></h5>
                                    <p class="text-sm text-gray-500" x-show="activeChat.description" x-text="activeChat.description"></p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        <span x-show="activeChat.type === 'private'">Личный чат</span>
                                        <span x-show="activeChat.type === 'group'">Групповой чат</span>
                                        <span x-show="activeChat.type === 'department'">Чат отдела</span>
                                        <span x-show="activeChat.type === 'company'">Общий чат компании</span>
                                    </p>
                                </div>

                                <div class="grid grid-cols-3 gap-2 text-center">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                                        <p class="text-lg font-semibold" x-text="activeChat.users?.length || 0"></p>
                                        <p class="text-xs text-gray-500">Участников</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                                        <p class="text-lg font-semibold"
                                           x-text="activeChat.users?.filter(u => u.is_online).length || 0"></p>
                                        <p class="text-xs text-gray-500">В сети</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                                        <p class="text-lg font-semibold" x-text="messages ? messages.length : 0"></p>
                                        <p class="text-xs text-gray-500">Сообщений</p>
                                    </div>
                                </div>

                                <div>
                                    <h5 class="font-medium mb-2">Участники</h5>
                                    <div class="space-y-2 max-h-96 overflow-auto custom-scrollbar">
                                        <template x-for="user in activeChat.users" :key="user.id">
                                            <div
                                                class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                                <div class="flex items-center gap-2">
                                                    <div class="relative flex-shrink-0">
                                                        <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200">
                                                            <template x-if="user.avatar">
                                                                <img :src="user.avatar" class="h-full w-full object-cover">
                                                            </template>
                                                            <template x-if="!user.avatar">
                                                                <div class="h-full w-full flex items-center justify-center text-xs font-medium text-white"
                                                                     :class="user.avatar_color || 'bg-gray-500'">
                                                                    <span x-text="user.initials"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                        <span class="online-indicator w-2.5 h-2.5"
                                                              :class="user.is_online ? 'online' : 'offline'"></span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium" x-text="user.name"></p>
                                                        <p class="text-xs text-gray-500" x-text="user.role || 'Сотрудник'"></p>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-1">
                                                    <span x-show="user.id === activeChat.created_by"
                                                          class="text-xs text-yellow-500" title="Создатель">
                                                        <i class="fas fa-crown"></i>
                                                    </span>
                                                    <span x-show="user.pivot?.role === 'admin'"
                                                          class="text-xs text-blue-500" title="Админ">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </span>
                                                </div>

                                                <template
                                                    x-if="activeChat.type !== 'private' && (activeChat.pivot?.role === 'admin' || isLeader || isCompanyOwner) && user.id !== userId">
                                                    <button @click="removeUserFromChat(user.id)"
                                                            class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded-full transition-colors">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        @include('partials.chat.modals')
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('chatApp', () => ({
                userId: {{ auth()->id() ?? 0 }},
                isLeader: {{ auth()->user()?->isLeader() ? 'true' : 'false' }},
                isManager: {{ auth()->user()?->isManagerRole() ? 'true' : 'false' }},
                isCompanyOwner: {{ auth()->user()?->isCompanyOwner() ? 'true' : 'false' }},

                loading: true,
                loadingMessages: false,
                sending: false,
                activeChat: null,
                showChatInfo: false,
                showNewChatModal: false,
                showNewGroupModal: false,
                showAddUsersModal: false,
                showScrollButton: false,
                sidebarOpen: false,
                showDepartmentChats: false,
                isCreatingChat: false,
                hasMoreMessages: true,
                oldestMessageId: null,
                loadingMoreMessages: false,
                replyTo: null,

                chats: [],
                messages: [],
                colleagues: [],
                departments: [],

                searchQuery: '',
                colleagueSearch: '',
                groupSearch: '',
                addUsersSearch: '',

                newMessage: '',
                selectedFile: null,
                typingUsers: [],
                typingTimeout: null,

                newGroup: {
                    name: '',
                    description: '',
                    selectedUsers: []
                },

                selectedUsersToAdd: [],

                pollInterval: null,
                searchTimeout: null,

                get unreadTotal() {
                    return (this.chats || []).reduce((sum, chat) => sum + (chat.unread_count || 0), 0);
                },

                get hasCompanyChat() {
                    return this.chats.some(chat => chat.type === 'company');
                },

                get filteredChats() {
                    if (!this.searchQuery) return this.chats || [];
                    const query = this.searchQuery.toLowerCase();
                    return (this.chats || []).filter(chat =>
                        (chat.display_name || chat.name || '').toLowerCase().includes(query) ||
                        (chat.users || []).some(u => (u.name || '').toLowerCase().includes(query))
                    );
                },

                get filteredColleagues() {
                    if (!this.colleagueSearch) return this.colleagues || [];
                    const query = this.colleagueSearch.toLowerCase();
                    return (this.colleagues || []).filter(c =>
                        (c.name || '').toLowerCase().includes(query) ||
                        (c.department || '').toLowerCase().includes(query)
                    );
                },

                get filteredGroupColleagues() {
                    let filtered = this.colleagues || [];
                    if (this.groupSearch) {
                        const query = this.groupSearch.toLowerCase();
                        filtered = filtered.filter(c =>
                            (c.name || '').toLowerCase().includes(query) ||
                            (c.department || '').toLowerCase().includes(query)
                        );
                    }
                    return filtered.filter(c => !(this.newGroup.selectedUsers || []).includes(c.id));
                },

                get filteredAddUsers() {
                    let filtered = this.colleagues || [];
                    if (this.addUsersSearch) {
                        const query = this.addUsersSearch.toLowerCase();
                        filtered = filtered.filter(c =>
                            (c.name || '').toLowerCase().includes(query) ||
                            (c.department || '').toLowerCase().includes(query)
                        );
                    }
                    return filtered;
                },

                init() {
                    console.log('Chat app initialized');
                    this.loadChats();
                    this.loadColleagues();
                    this.loadDepartments();
                    this.startPolling();

                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            this.sidebarOpen = false;
                            this.showChatInfo = false;
                        }
                    });
                },

                startPolling() {
                    this.pollInterval = setInterval(() => {
                        if (this.activeChat) {
                            this.pollNewMessages();
                        }
                        // this.updateUnreadCounts();
                    }, 5000);
                },

                stopPolling() {
                    if (this.pollInterval) {
                        clearInterval(this.pollInterval);
                    }
                },

                updateUnreadCounts() {
                    fetch('/chat/chats/unread-counts', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.chats = this.chats.map(chat => {
                                    const update = data.counts.find(c => c.id === chat.id);
                                    if (update) {
                                        chat.unread_count = update.unread_count;
                                    }
                                    return chat;
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error updating unread counts:', error);
                        });
                },

                loadChats() {
                    this.loading = true;
                    fetch('/chat/chats', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.chats = data.chats || [];
                                console.log('Chats loaded:', this.chats);
                            }
                        })
                        .catch(error => {
                            console.error('Error loading chats:', error);
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },

                loadColleagues() {
                    fetch('/chat/colleagues', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.colleagues = data.colleagues || [];
                            }
                        })
                        .catch(error => {
                            console.error('Error loading colleagues:', error);
                            this.colleagues = [];
                        });
                },

                loadDepartments() {
                    fetch('/team/departments/list', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.departments = data.departments || [];
                            } else {
                                this.departments = [];
                            }
                        })
                        .catch(error => {
                            console.error('Error loading departments:', error);
                            this.departments = [];
                        });
                },

                selectChat(chat) {
                    this.activeChat = chat;
                    this.showChatInfo = false;
                    this.sidebarOpen = false;
                    this.loadMessages(chat);
                },

                toggleChatInfo() {
                    this.showChatInfo = !this.showChatInfo;
                },

                closeChat() {
                    this.activeChat = null;
                    this.sidebarOpen = true;
                },

                replyToMessage(message) {
                    this.replyTo = message;
                    this.newMessage = '';
                    this.$nextTick(() => {
                        const input = document.querySelector('.message-input');
                        if (input) {
                            input.focus();
                        }
                    });
                },

                cancelReply() {
                    this.replyTo = null;
                },

                getMessageAuthor(messageId) {
                    const message = this.messages.find(m => m.id === messageId);
                    return message?.user?.name || 'Неизвестный';
                },

                getReplyContent(messageId) {
                    const message = this.messages.find(m => m.id === messageId);
                    if (!message) return '';
                    return message.content ? message.content.substring(0, 60) + (message.content.length > 60 ? '...' : '') : 'Сообщение';
                },

                loadMessages(chat) {
                    this.loadingMessages = true;
                    this.messages = [];

                    fetch(`/chat/chats/${chat.id}/messages?limit=50`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            console.log('=== ДАННЫЕ СООБЩЕНИЙ ===', data);
                            if (data && data.success) {
                                let messagesData = [];
                                if (data.messages && data.messages.data && Array.isArray(data.messages.data)) {
                                    messagesData = data.messages.data;
                                }
                                this.messages = [];
                                messagesData.forEach(msg => {
                                    this.messages.push(msg);
                                });
                                if (this.messages.length > 0) {
                                    this.oldestMessageId = this.messages[0]?.id;
                                    this.hasMoreMessages = this.messages.length >= 50;
                                }
                            } else {
                                this.messages = [];
                            }
                            setTimeout(() => {
                                this.$nextTick(() => {
                                    this.scrollToBottom();
                                    this.markAsRead();
                                });
                            }, 100);
                        })
                        .catch(error => {
                            console.error('Error loading messages:', error);
                            this.messages = [];
                        })
                        .finally(() => {
                            this.loadingMessages = false;
                        });
                },

                loadMoreMessages() {
                    if (!this.activeChat || !this.oldestMessageId || this.loadingMessages) return;

                    this.loadingMessages = true;
                    const oldestId = this.oldestMessageId;

                    fetch(`/chat/chats/${this.activeChat.id}/messages?before=${oldestId}&limit=30`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.messages?.data?.length > 0) {
                                const olderMessages = data.messages.data;
                                this.messages = [...olderMessages, ...this.messages];
                                this.oldestMessageId = olderMessages[0]?.id;
                                this.hasMoreMessages = olderMessages.length >= 30;
                                this.$nextTick(() => {
                                    const container = this.$refs.messagesContainer;
                                    if (container) {
                                        const firstMessage = container.querySelector('.message-item:first-child');
                                        if (firstMessage) {
                                            const scrollOffset = firstMessage.offsetTop;
                                            container.scrollTop = scrollOffset;
                                        }
                                    }
                                });
                            } else {
                                this.hasMoreMessages = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error loading more messages:', error);
                        })
                        .finally(() => {
                            this.loadingMessages = false;
                        });
                },

                pollNewMessages() {
                    if (!this.activeChat || !this.messages) return;

                    const lastMessageId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : 0;

                    fetch(`/chat/chats/${this.activeChat.id}/messages?after=${lastMessageId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.messages?.data?.length > 0) {
                                const newMessages = data.messages.data;
                                this.messages = [...this.messages, ...newMessages];
                                const container = this.$refs.messagesContainer;
                                if (container) {
                                    const isNearBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 200;
                                    if (isNearBottom) {
                                        this.$nextTick(() => {
                                            this.scrollToBottom();
                                        });
                                    }
                                }
                                this.markAsRead();
                            }
                        })
                        .catch(error => {
                            console.error('Error polling messages:', error);
                        });
                },

                sendMessage() {
                    if ((!this.newMessage.trim() && !this.selectedFile) || this.sending) return;

                    this.sending = true;

                    const formData = new FormData();
                    if (this.selectedFile) {
                        formData.append('file', this.selectedFile);
                        var url = `/chat/chats/${this.activeChat.id}/upload`;
                    } else {
                        formData.append('content', this.newMessage);
                        formData.append('type', 'text');
                        if (this.replyTo) {
                            formData.append('parent_id', this.replyTo.id);
                        }
                        var url = `/chat/chats/${this.activeChat.id}/send`;
                    }

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.messages.push(data.message);
                                this.newMessage = '';
                                this.selectedFile = null;
                                this.replyTo = null;
                                if (this.$refs.fileInput) {
                                    this.$refs.fileInput.value = '';
                                }
                                setTimeout(() => {
                                    this.loadChats();
                                }, 500);
                                this.$nextTick(() => {
                                    this.scrollToBottom();
                                });
                            } else {
                                alert(data.message || 'Ошибка при отправке сообщения');
                            }
                        })
                        .catch(error => {
                            console.error('Error sending message:', error);
                            alert('Ошибка при отправке сообщения');
                        })
                        .finally(() => {
                            this.sending = false;
                        });
                },

                uploadFile(event) {
                    this.selectedFile = event.target.files[0];
                    this.sendMessage();
                },

                triggerFileUpload() {
                    if (this.$refs.fileInput) {
                        this.$refs.fileInput.click();
                    }
                },

                handleEnterKey(e) {
                    if (e.shiftKey) {
                        return;
                    }
                    e.preventDefault();
                    this.sendMessage();
                },

                handleTyping() {
                    clearTimeout(this.typingTimeout);
                    this.typingTimeout = setTimeout(() => {}, 1000);
                },

                startPrivateChat(colleague) {
                    this.showNewChatModal = false;
                    this.colleagueSearch = '';
                    this.loading = true;

                    fetch('/chat/private-chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({user_id: colleague.id})
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.loadChats();
                                setTimeout(() => {
                                    this.selectChat(data.chat);
                                }, 300);
                            } else {
                                alert(data.message || 'Ошибка при создании чата');
                            }
                        })
                        .catch(error => {
                            console.error('Error creating private chat:', error);
                            alert('Ошибка при создании чата: ' + error.message);
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },

                createGroupChat() {
                    if (!this.newGroup.name || (this.newGroup.selectedUsers || []).length < 2) {
                        alert('Название группы и минимум 2 участника обязательны');
                        return;
                    }

                    this.isCreatingChat = true;
                    this.showNewGroupModal = false;

                    fetch('/chat/group-chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            name: this.newGroup.name,
                            description: this.newGroup.description,
                            user_ids: this.newGroup.selectedUsers
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.newGroup = {name: '', description: '', selectedUsers: []};
                                this.groupSearch = '';
                                this.loadChats();
                                setTimeout(() => {
                                    this.selectChat(data.chat);
                                }, 300);
                            } else {
                                alert(data.message || 'Ошибка при создании группы');
                                this.showNewGroupModal = true;
                            }
                        })
                        .catch(error => {
                            console.error('Error creating group chat:', error);
                            alert('Ошибка при создании группы');
                            this.showNewGroupModal = true;
                        })
                        .finally(() => {
                            this.isCreatingChat = false;
                        });
                },

                createDepartmentChat(departmentId) {
                    fetch('/chat/department-chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({department_id: departmentId})
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.loadChats();
                                this.selectChat(data.chat);
                                this.showDepartmentChats = false;
                            } else {
                                alert(data.message || 'Ошибка при создании чата отдела');
                            }
                        })
                        .catch(error => {
                            console.error('Error creating department chat:', error);
                            alert('Ошибка при создании чата отдела');
                        });
                },

                createCompanyChat() {
                    console.log('Клик по кнопке!'); // Если в консоли 2 клика — проблема во фронте
                    if (this.isCreatingChat) return;
                    if (this.hasCompanyChat) {
                        alert('Общий чат компании уже существует');
                        return;
                    }

                    if (this.isCreatingChat) return;

                    if (!confirm('Создать общий чат для всей компании?')) return;

                    this.isCreatingChat = true;

                    fetch('/chat/company-chat', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.loadChats();
                                setTimeout(() => {
                                    this.selectChat(data.chat);
                                    this.loadMessages(data.chat);
                                }, 300);
                            } else {
                                alert(data.message || 'Ошибка при создании общего чата');
                            }
                        })
                        .catch(error => {
                            console.error('Error creating company chat:', error);
                            alert('Ошибка при создании общего чата');
                        })
                        .finally(() => {
                            this.isCreatingChat = false;
                        });
                },

                addUsersToChat() {
                    if (this.selectedUsersToAdd.length === 0) return;

                    fetch(`/chat/chats/${this.activeChat.id}/add-users`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({user_ids: this.selectedUsersToAdd})
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.showAddUsersModal = false;
                                this.selectedUsersToAdd = [];
                                this.addUsersSearch = '';
                                this.loadMessages(this.activeChat);
                                alert('Участники добавлены');
                            } else {
                                alert(data.message || 'Ошибка при добавлении участников');
                            }
                        })
                        .catch(error => {
                            console.error('Error adding users:', error);
                            alert('Ошибка при добавлении участников');
                        });
                },

                removeUserFromChat(userId) {
                    if (!confirm('Удалить пользователя из чата?')) return;

                    fetch(`/chat/chats/${this.activeChat.id}/remove-user`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({user_id: userId})
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.loadMessages(this.activeChat);
                                this.loadChats();
                                alert('Пользователь удален из чата');
                            } else {
                                alert(data.message || 'Ошибка при удалении пользователя');
                            }
                        })
                        .catch(error => {
                            console.error('Error removing user:', error);
                            alert('Ошибка при удалении пользователя');
                        });
                },

                leaveChat(chat) {
                    if (!confirm('Вы уверены, что хотите покинуть чат?')) return;

                    fetch(`/chat/chats/${chat.id}/remove-user`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({user_id: this.userId})
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.loadChats();
                                this.activeChat = null;
                                alert('Вы покинули чат');
                            } else {
                                alert(data.message || 'Ошибка при выходе из чата');
                            }
                        })
                        .catch(error => {
                            console.error('Error leaving chat:', error);
                            alert('Ошибка при выходе из чата');
                        });
                },

                deleteChat(chat) {
                    if (!confirm('Удалить чат? Это действие нельзя отменить.')) return;

                    fetch(`/chat/chats/${chat.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.loadChats();
                                this.activeChat = null;
                                alert('Чат удален');
                            } else {
                                alert(data.message || 'Ошибка при удалении чата');
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting chat:', error);
                            alert('Ошибка при удалении чата');
                        });
                },

                markAsRead() {
                    if (!this.activeChat || !this.messages || this.messages.length === 0) return;

                    const unreadMessages = this.messages
                        .filter(m => m && m.user_id !== this.userId && (!m.status || m.status !== 'read'))
                        .map(m => m.id)
                        .filter(id => id);

                    if (unreadMessages.length === 0) return;

                    fetch(`/chat/chats/${this.activeChat.id}/mark-read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({message_ids: unreadMessages})
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.messages.forEach(m => {
                                    if (unreadMessages.includes(m.id)) {
                                        if (!m.statuses) m.statuses = {};
                                        m.statuses.status = 'read';
                                        m.statuses.read_at = new Date().toISOString();
                                        m.status = 'read';
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error marking messages as read:', error);
                        });
                },

                toggleMute(chat) {
                    if (!chat.pivot) chat.pivot = {};
                    chat.pivot.is_muted = !chat.pivot.is_muted;
                },

                downloadFile(message) {
                    if (message.file_url) {
                        window.open(message.file_url, '_blank');
                    }
                },

                openImageViewer(message) {
                    if (message.file_url) {
                        window.open(message.file_url, '_blank');
                    }
                },

                scrollToBottom() {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                        this.showScrollButton = false;
                    }
                },

                checkScroll() {
                    const container = this.$refs.messagesContainer;
                    if (!container) return;

                    const isNearTop = container.scrollTop < 100;

                    if (isNearTop && this.hasMoreMessages && !this.loadingMoreMessages && !this.loadingMessages) {
                        this.loadMoreMessages();
                    }

                    const isNearBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 100;
                    this.showScrollButton = !isNearBottom;
                },

                formatTime(timestamp) {
                    if (!timestamp) return '';
                    try {
                        const date = new Date(timestamp);
                        const now = new Date();
                        const diff = now - date;

                        if (diff < 24 * 60 * 60 * 1000) {
                            return date.toLocaleTimeString('ru-RU', {hour: '2-digit', minute: '2-digit'});
                        }

                        if (diff < 7 * 24 * 60 * 60 * 1000) {
                            return date.toLocaleDateString('ru-RU', {weekday: 'short'});
                        }

                        return date.toLocaleDateString('ru-RU', {day: '2-digit', month: '2-digit'});
                    } catch (e) {
                        return '';
                    }
                },

                getAvatarUrl(user) {
                    if (user && user.avatar) {
                        return user.avatar;
                    }
                    return null;
                },

                getTypingText() {
                    if (this.typingUsers.length === 1) {
                        return `${this.typingUsers[0]} печатает...`;
                    }
                    if (this.typingUsers.length === 2) {
                        return `${this.typingUsers[0]} и ${this.typingUsers[1]} печатают...`;
                    }
                    if (this.typingUsers.length > 2) {
                        return `${this.typingUsers[0]} и еще ${this.typingUsers.length - 1} печатают...`;
                    }
                    return '';
                },

                destroy() {
                    this.stopPolling();
                }
            }));

        });
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
@endpush
