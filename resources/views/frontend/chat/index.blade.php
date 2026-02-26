{{-- resources/views/frontend/chat/index.blade.php --}}
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .chat-container {
            height: calc(100vh - 186px);
        }
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
        .bg-green-500 {
            background-color: #10B981;
        }
        .bg-green-600:hover {
            background-color: #059669;
        }
        .text-green-500 {
            color: #10B981;
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
    </style>
@endpush

@section('content')
    <div class="chat-container overflow-hidden" x-data="chatApp()" x-init="init()" x-cloak>

    <div class="flex h-full flex-col gap-6 xl:flex-row xl:gap-5">
            <!-- Chat Sidebar -->
            <div class="flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white xl:flex xl:w-1/4 dark:border-gray-800 dark:bg-white/[0.03]"
                 :class="{'hidden xl:flex': activeChat, 'flex': !activeChat}">

                <div class="sticky px-4 pt-4 pb-4 sm:px-5 sm:pt-5 xl:pb-0">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-theme-xl font-semibold text-gray-800 sm:text-2xl dark:text-white/90">
                                Чаты
                                <span x-show="unreadTotal > 0"
                                      class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                <span x-text="unreadTotal"></span>
                            </span>
                            </h3>
                        </div>

                        <div x-data="{openMenu: false}" class="relative">
                            <button @click="openMenu = !openMenu"
                                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white p-2">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="openMenu" @click.outside="openMenu = false" x-cloak
                                 class="absolute right-0 top-full z-40 w-48 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-800">
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
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text"
                               x-model="searchQuery"
                               @input="filterChats()"
                               placeholder="Поиск..."
                               class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    </div>
                </div>

                <!-- Chat List -->
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
                        </div>
                    </template>

                    <template x-for="chat in filteredChats" :key="chat.id">
                        <div @click="selectChat(chat)"
                             class="flex cursor-pointer items-center gap-3 rounded-lg p-3 mb-1 hover:bg-gray-100 dark:hover:bg-white/[0.03]"
                             :class="{'bg-gray-100 dark:bg-white/[0.03]': activeChat?.id === chat.id}">

                            <div class="relative flex-shrink-0">
                                <template x-if="chat.type === 'private' && chat.users && chat.users[0]">
                                    <div class="h-12 w-12 rounded-full overflow-hidden bg-gray-200">
                                        <template x-if="chat.users[0].avatar">
                                            <img :src="chat.users[0].avatar" class="h-full w-full object-cover">
                                        </template>
                                        <template x-if="!chat.users[0].avatar">
                                            <div class="h-full w-full flex items-center justify-center text-lg font-medium text-white"
                                                 :class="'bg-' + (chat.users[0].avatar_color || 'blue-500')">
                                                <span x-text="chat.users[0].initials || '?'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="chat.type === 'group'">
                                    <div class="h-12 w-12 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-users text-xl text-gray-500"></i>
                                    </div>
                                </template>

                                <template x-if="chat.type === 'private' && chat.users && chat.users[0]">
                                <span class="online-indicator"
                                      :class="chat.users[0].is_online ? 'online' : 'offline'"></span>
                                </template>
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
                                              x-show="chat.type === 'group'"></span>
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

                                        <span x-show="chat.pivot?.is_muted"
                                              class="text-gray-400">
                                        <i class="fas fa-volume-mute text-xs"></i>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Chat Box -->
            <div class="flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] xl:w-3/4"
                 :class="{'hidden xl:flex': !activeChat, 'flex': activeChat}">

                <template x-if="!activeChat">
                    <div class="flex flex-col items-center justify-center h-full text-gray-400">
                        <i class="fas fa-comments text-6xl mb-4"></i>
                        <p class="text-lg">Выберите чат для начала общения</p>
                    </div>
                </template>

                <template x-if="activeChat">
                    <div class="flex flex-col h-full">
                        <!-- Chat Header -->
                        <div class="sticky flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <button @click="activeChat = null" class="xl:hidden mr-2 p-2 hover:bg-gray-100 rounded-full">
                                    <i class="fas fa-arrow-left"></i>
                                </button>

                                <div class="relative flex-shrink-0">
                                    <template x-if="activeChat.type === 'private' && activeChat.users && activeChat.users[0]">
                                        <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200">
                                            <template x-if="activeChat.users[0].avatar">
                                                <img :src="activeChat.users[0].avatar" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="!activeChat.users[0].avatar">
                                                <div class="h-full w-full flex items-center justify-center text-sm font-medium text-white"
                                                     :class="'bg-' + (activeChat.users[0].avatar_color || 'blue-500')">
                                                    <span x-text="activeChat.users[0].initials || '?'"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="activeChat.type === 'group'">
                                        <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-users text-lg text-gray-500"></i>
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
                                          x-text="activeChat.users[0].is_online ? 'В сети' : (activeChat.users[0].last_activity ? 'Был(а) ' + formatTime(activeChat.users[0].last_activity) : '')"></span>
                                        <span x-show="activeChat.type === 'group'"
                                              x-text="activeChat.users?.length + ' участников'"></span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button @click="toggleMute(activeChat)"
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white/90 rounded-full hover:bg-gray-100"
                                        :class="{'text-green-500': activeChat.pivot?.is_muted}">
                                    <i class="fas" :class="activeChat.pivot?.is_muted ? 'fa-volume-off' : 'fa-volume-up'"></i>
                                </button>

                                <button @click="showChatInfo = !showChatInfo"
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white/90 rounded-full hover:bg-gray-100">
                                    <i class="fas fa-info-circle"></i>
                                </button>

                                <div x-data="{openMenu: false}" class="relative">
                                    <button @click="openMenu = !openMenu"
                                            class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white/90 rounded-full hover:bg-gray-100">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div x-show="openMenu" @click.outside="openMenu = false" x-cloak
                                         class="absolute right-0 top-full z-40 w-48 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-800">

                                        <template x-if="activeChat.type === 'group' && (activeChat.pivot?.role === 'admin' || isLeader)">
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

                                        <template x-if="activeChat.type === 'group' && (activeChat.pivot?.role === 'admin' || isLeader)">
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

                        <!-- Messages Area -->
                        <div class="flex-1 overflow-auto p-5 space-y-4 custom-scrollbar"
                             x-ref="messagesContainer"
                             @scroll="checkScroll()">

                            <template x-if="loadingMessages">
                                <div class="flex justify-center py-4">
                                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                                </div>
                            </template>

                            <template x-for="message in messages" :key="message.id">
                                <div class="flex"
                                     :class="message.user_id === userId ? 'justify-end' : 'justify-start'">

                                    <div class="max-w-[70%]"
                                         :class="message.user_id === userId ? 'text-right' : ''">

                                        <!-- System Message -->
                                        <template x-if="message.type === 'system'">
                                            <div class="text-center text-xs text-gray-500 my-2">
                                            <span class="bg-gray-100 px-3 py-1 rounded-full dark:bg-gray-800"
                                                  x-text="message.content"></span>
                                            </div>
                                        </template>

                                        <!-- Regular Message -->
                                        <template x-if="message.type !== 'system'">
                                            <div>
                                                <!-- Sender name for group chats -->
                                                <p x-show="activeChat.type === 'group' && message.user_id !== userId"
                                                   class="text-xs text-gray-500 mb-1 ml-2"
                                                   x-text="message.user?.name"></p>

                                                <!-- Message content -->
                                                <div class="rounded-2xl px-4 py-2 break-words"
                                                     :class="message.user_id === userId ?
                                                        'bg-green-500 text-white rounded-br-none' :
                                                        'bg-gray-100 dark:bg-white/5 rounded-bl-none'">

                                                    <!-- File message -->
                                                    <template x-if="message.type === 'file'">
                                                        <div class="file-attachment" @click="downloadFile(message)">
                                                            <div class="flex items-center gap-3">
                                                                <i :class="'fas ' + (message.file_icon || 'fa-file') + ' text-2xl'"></i>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-sm font-medium truncate"
                                                                       x-text="message.file_name || 'Файл'"></p>
                                                                    <p class="text-xs opacity-75"
                                                                       x-text="message.formatted_file_size || ''"></p>
                                                                </div>
                                                                <i class="fas fa-download"></i>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Image message -->
                                                    <template x-if="message.type === 'image'">
                                                        <div class="max-w-sm">
                                                            <img :src="message.file_url"
                                                                 :alt="message.file_name"
                                                                 class="rounded-lg cursor-pointer max-h-48 object-cover"
                                                                 @click="openImageViewer(message)">
                                                        </div>
                                                    </template>

                                                    <!-- Text message -->
                                                    <template x-if="message.type === 'text' || !message.type">
                                                        <p class="text-sm whitespace-pre-wrap break-words"
                                                           x-text="message.content"></p>
                                                    </template>

                                                    <!-- Edited indicator -->
                                                    <span x-show="message.is_edited"
                                                          class="text-xs opacity-70 mt-1 block">
                                                    (ред.)
                                                </span>
                                                </div>

                                                <!-- Message footer -->
                                                <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                                                    <span x-text="formatTime(message.created_at)"></span>

                                                    <template x-if="message.user_id === userId">
                                                        <div class="flex items-center gap-1">
                                                            <i class="fas fa-check text-xs"
                                                               :class="{'text-blue-400': message.delivered_at, 'text-gray-400': !message.delivered_at}"></i>
                                                            <i class="fas fa-check text-xs"
                                                               :class="{'text-blue-600': message.statuses?.[0]?.status === 'read', 'text-gray-400': message.statuses?.[0]?.status !== 'read'}"></i>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Typing indicator -->
                            <div x-show="typingUsers.length > 0"
                                 class="flex items-center gap-2 text-gray-500">
                                <div class="typing-indicator">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="text-xs" x-text="getTypingText()"></span>
                            </div>
                        </div>

                        <!-- Scroll to bottom button -->
                        <button x-show="showScrollButton"
                                @click="scrollToBottom()"
                                class="fixed bottom-24 right-8 bg-green-500 text-white rounded-full p-3 shadow-lg hover:bg-green-600 transition z-10">
                            <i class="fas fa-arrow-down"></i>
                        </button>

                        <!-- Message Input -->
                        <div class="sticky bottom-0 border-t border-gray-200 p-3 dark:border-gray-800 bg-white dark:bg-gray-900">
                            <form @submit.prevent="sendMessage" class="flex items-center gap-2">
                                <button type="button"
                                        @click="triggerFileUpload"
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white/90 rounded-full hover:bg-gray-100"
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
                                          style="max-height: 120px;"
                                          :disabled="sending"></textarea>
                                </div>

                                <button type="submit"
                                        :disabled="(!newMessage.trim() && !selectedFile) || sending"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-500 text-white hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Chat Info Sidebar -->
                        <div x-show="showChatInfo" x-cloak
                             class="absolute right-0 top-0 bottom-0 w-80 bg-white border-l border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-4 overflow-auto shadow-xl z-20"
                             x-transition:enter="transition transform duration-300"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transition transform duration-300"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full">

                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold">Информация о чате</h4>
                                <button @click="showChatInfo = false" class="text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-full">
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
                                        <template x-if="activeChat.type === 'group'">
                                            <i class="fas fa-users text-4xl text-gray-400"></i>
                                        </template>
                                    </div>
                                    <h5 class="mt-2 font-medium" x-text="activeChat.display_name || activeChat.name"></h5>
                                    <p class="text-sm text-gray-500" x-show="activeChat.description" x-text="activeChat.description"></p>
                                </div>

                                <div>
                                    <h5 class="font-medium mb-2">Участники · <span x-text="activeChat.users?.length"></span></h5>
                                    <div class="space-y-2 max-h-96 overflow-auto custom-scrollbar">
                                        <template x-for="user in activeChat.users" :key="user.id">
                                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                                                <div class="flex items-center gap-2">
                                                    <div class="relative flex-shrink-0">
                                                        <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200">
                                                            <template x-if="user.avatar">
                                                                <img :src="user.avatar" class="h-full w-full object-cover">
                                                            </template>
                                                            <template x-if="!user.avatar">
                                                                <div class="h-full w-full flex items-center justify-center text-xs font-medium text-white"
                                                                     :class="'bg-' + (user.avatar_color || 'blue-500')">
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

                                                <template x-if="activeChat.type === 'group' && (activeChat.pivot?.role === 'admin' || isLeader) && user.id !== userId">
                                                    <button @click="removeUserFromChat(user.id)"
                                                            class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded-full">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </template>

                                                <span x-show="user.id === activeChat.created_by"
                                                      class="text-xs text-gray-400">
                                                <i class="fas fa-crown text-yellow-500"></i>
                                            </span>
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

        <!-- New Chat Modal -->
        <div x-show="showNewChatModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
             @click.self="showNewChatModal = false">
            <div class="bg-white rounded-2xl w-full max-w-md p-6 dark:bg-gray-800">
                <h3 class="text-lg font-semibold mb-4">Новый чат</h3>

                <div class="mb-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text"
                               x-model="colleagueSearch"
                               placeholder="Поиск сотрудников..."
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 pl-10 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                </div>

                <div class="max-h-96 overflow-auto space-y-2 custom-scrollbar">
                    <template x-if="filteredColleagues.length === 0">
                        <div class="text-center py-4 text-gray-500">
                            <p>Нет доступных сотрудников</p>
                        </div>
                    </template>

                    <template x-for="colleague in filteredColleagues" :key="colleague.id">
                        <div @click="startPrivateChat(colleague)"
                             class="flex items-center gap-3 p-3 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="relative flex-shrink-0">
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                                    <template x-if="colleague.avatar">
                                        <img :src="colleague.avatar" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!colleague.avatar">
                                        <div class="h-full w-full flex items-center justify-center text-sm font-medium text-white"
                                             :class="'bg-' + (colleague.avatar_color || 'blue-500')">
                                            <span x-text="colleague.initials"></span>
                                        </div>
                                    </template>
                                </div>
                                <span class="online-indicator w-2.5 h-2.5"
                                      :class="colleague.is_online ? 'online' : 'offline'"></span>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium" x-text="colleague.name"></p>
                                <p class="text-sm text-gray-500" x-text="colleague.department || 'Без отдела'"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end mt-4">
                    <button @click="showNewChatModal = false"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-300">
                        Отмена
                    </button>
                </div>
            </div>
        </div>

        <!-- New Group Modal -->
        <div x-show="showNewGroupModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
             @click.self="showNewGroupModal = false">
            <div class="bg-white rounded-2xl w-full max-w-md p-6 dark:bg-gray-800">
                <h3 class="text-lg font-semibold mb-4">Создать группу</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Название группы</label>
                        <input type="text"
                               x-model="newGroup.name"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 dark:bg-gray-700 dark:border-gray-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Описание (необязательно)</label>
                        <textarea x-model="newGroup.description"
                                  rows="2"
                                  class="w-full rounded-lg border border-gray-300 px-4 py-2 dark:bg-gray-700 dark:border-gray-600"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Участники (минимум 2)</label>
                        <div class="relative mb-2">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text"
                                   x-model="groupSearch"
                                   placeholder="Поиск..."
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2 pl-10 dark:bg-gray-700 dark:border-gray-600">
                        </div>

                        <div class="max-h-48 overflow-auto space-y-2 custom-scrollbar border rounded-lg p-2">
                            <template x-if="filteredGroupColleagues.length === 0">
                                <div class="text-center py-2 text-gray-500 text-sm">
                                    Нет доступных сотрудников
                                </div>
                            </template>

                            <template x-for="colleague in filteredGroupColleagues" :key="colleague.id">
                                <label class="flex items-center gap-3 p-2 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <input type="checkbox"
                                           :value="colleague.id"
                                           x-model="newGroup.selectedUsers"
                                           class="rounded border-gray-300 text-green-500 focus:ring-green-500">
                                    <div class="flex-1">
                                        <p class="font-medium text-sm" x-text="colleague.name"></p>
                                        <p class="text-xs text-gray-500" x-text="colleague.department || 'Без отдела'"></p>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <div class="mt-2 text-sm text-gray-500" x-show="newGroup.selectedUsers.length > 0">
                            Выбрано: <span x-text="newGroup.selectedUsers.length"></span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button @click="showNewGroupModal = false"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-300">
                        Отмена
                    </button>
                    <button @click="createGroupChat"
                            :disabled="!newGroup.name || newGroup.selectedUsers.length < 2"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        Создать
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Users Modal -->
        <div x-show="showAddUsersModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
             @click.self="showAddUsersModal = false">
            <div class="bg-white rounded-2xl w-full max-w-md p-6 dark:bg-gray-800">
                <h3 class="text-lg font-semibold mb-4">Добавить участников</h3>

                <div class="mb-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text"
                               x-model="addUsersSearch"
                               placeholder="Поиск..."
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 pl-10 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                </div>

                <div class="max-h-96 overflow-auto space-y-2 custom-scrollbar">
                    <template x-for="colleague in filteredAddUsers" :key="colleague.id">
                        <template x-if="!activeChat.users?.some(u => u.id === colleague.id)">
                            <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                                <input type="checkbox"
                                       :value="colleague.id"
                                       x-model="selectedUsersToAdd"
                                       class="rounded border-gray-300 text-green-500 focus:ring-green-500">
                                <div class="flex-1">
                                    <p class="font-medium" x-text="colleague.name"></p>
                                    <p class="text-sm text-gray-500" x-text="colleague.department || 'Без отдела'"></p>
                                </div>
                            </label>
                        </template>
                    </template>

                    <template x-if="filteredAddUsers.filter(u => !activeChat.users?.some(au => au.id === u.id)).length === 0">
                        <div class="text-center py-4 text-gray-500">
                            <p>Нет доступных сотрудников для добавления</p>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button @click="showAddUsersModal = false"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-300">
                        Отмена
                    </button>
                    <button @click="addUsersToChat"
                            :disabled="selectedUsersToAdd.length === 0"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:opacity-50">
                        Добавить (<span x-text="selectedUsersToAdd.length"></span>)
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Используем IIFE для изоляции переменных
        (function() {
            function chatApp() {
                return {
                    // Data
                    userId: {{ auth()->id() ?? 0 }},
                    isLeader: {{ auth()->user()?->isLeader() ? 'true' : 'false' }},
                    isManager: {{ auth()->user()?->isManagerRole() ? 'true' : 'false' }},

                    // UI State
                    loading: true,
                    loadingMessages: false,
                    sending: false,
                    activeChat: null,
                    showChatInfo: false,
                    showNewChatModal: false,
                    showNewGroupModal: false,
                    showAddUsersModal: false,
                    showScrollButton: false,

                    // Chat Data
                    chats: [],
                    messages: [],
                    colleagues: [],

                    // Search
                    searchQuery: '',
                    colleagueSearch: '',
                    groupSearch: '',
                    addUsersSearch: '',

                    // Message
                    newMessage: '',
                    selectedFile: null,
                    typingUsers: [],
                    typingTimeout: null,

                    // Group Creation
                    newGroup: {
                        name: '',
                        description: '',
                        selectedUsers: []
                    },

                    // Add Users
                    selectedUsersToAdd: [],

                    // Polling interval
                    pollInterval: null,

                    // Computed
                    get unreadTotal() {
                        return (this.chats || []).reduce((sum, chat) => sum + (chat.unread_count || 0), 0);
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

                    // Methods
                    init() {
                        this.loadChats();
                        this.loadColleagues();
                        this.startPolling();
                    },

                    startPolling() {
                        this.pollInterval = setInterval(() => {
                            if (this.activeChat) {
                                this.pollNewMessages();
                            }
                            this.loadChats();
                        }, 5000);
                    },

                    stopPolling() {
                        if (this.pollInterval) {
                            clearInterval(this.pollInterval);
                        }
                    },

                    loadChats() {
                        this.loading = true;
                        fetch('/chat/api/chats', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.chats = data.chats || [];
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
                        fetch('/chat/api/colleagues', {
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
                            });
                    },

                    selectChat(chat) {
                        this.activeChat = chat;
                        this.showChatInfo = false;
                        this.loadMessages(chat);
                    },

                    loadMessages(chat) {
                        this.loadingMessages = true;
                        this.messages = [];

                        fetch(`/chat/api/chats/${chat.id}/messages`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.messages = (data.messages?.data || []).reverse();
                                    this.$nextTick(() => {
                                        this.scrollToBottom();
                                        this.markAsRead();
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error loading messages:', error);
                            })
                            .finally(() => {
                                this.loadingMessages = false;
                            });
                    },

                    pollNewMessages() {
                        if (!this.activeChat) return;

                        const lastMessageId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : 0;

                        fetch(`/chat/api/chats/${this.activeChat.id}/messages?after=${lastMessageId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && data.messages?.data?.length > 0) {
                                    const newMessages = data.messages.data.reverse();
                                    this.messages = [...this.messages, ...newMessages];
                                    this.$nextTick(() => {
                                        this.scrollToBottom();
                                        this.markAsRead();
                                    });
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
                            var url = `/chat/api/chats/${this.activeChat.id}/upload`;
                        } else {
                            formData.append('content', this.newMessage);
                            formData.append('type', 'text');
                            var url = `/chat/api/chats/${this.activeChat.id}/send`;
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
                                    if (this.$refs.fileInput) {
                                        this.$refs.fileInput.value = '';
                                    }
                                    this.$nextTick(() => {
                                        this.scrollToBottom();
                                    });
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
                        this.typingTimeout = setTimeout(() => {
                            // Implement typing stopped
                        }, 1000);
                    },

                    startPrivateChat(colleague) {
                        fetch('/chat/api/private-chat', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ user_id: colleague.id })
                        })
                            .then(res => {
                                if (!res.ok) {
                                    throw new Error(`HTTP error! status: ${res.status}`);
                                }
                                return res.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    this.showNewChatModal = false;
                                    this.colleagueSearch = '';
                                    this.loadChats();
                                    this.selectChat(data.chat);
                                } else {
                                    alert(data.message || 'Ошибка при создании чата');
                                }
                            })
                            .catch(error => {
                                console.error('Error creating private chat:', error);
                                alert('Ошибка при создании чата: ' + error.message);
                            });
                    },

                    createGroupChat() {
                        if (!this.newGroup.name || (this.newGroup.selectedUsers || []).length < 2) return;

                        fetch('/chat/api/group-chat', {
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
                                    this.showNewGroupModal = false;
                                    this.newGroup = { name: '', description: '', selectedUsers: [] };
                                    this.groupSearch = '';
                                    this.loadChats();
                                    this.selectChat(data.chat);
                                } else {
                                    alert(data.message || 'Ошибка при создании группы');
                                }
                            })
                            .catch(error => {
                                console.error('Error creating group chat:', error);
                                alert('Ошибка при создании группы');
                            });
                    },

                    addUsersToChat() {
                        if (this.selectedUsersToAdd.length === 0) return;

                        fetch(`/chat/api/chats/${this.activeChat.id}/add-users`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ user_ids: this.selectedUsersToAdd })
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

                    leaveChat(chat) {
                        if (!confirm('Вы уверены, что хотите покинуть чат?')) return;

                        fetch(`/chat/api/chats/${chat.id}/remove-user`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ user_id: this.userId })
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

                    removeUserFromChat(userId) {
                        if (!confirm('Удалить пользователя из чата?')) return;

                        fetch(`/chat/api/chats/${this.activeChat.id}/remove-user`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ user_id: userId })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.loadMessages(this.activeChat);
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

                    deleteChat(chat) {
                        if (!confirm('Удалить чат? Это действие нельзя отменить.')) return;

                        fetch(`/chat/api/chats/${chat.id}`, {
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

                    toggleMute(chat) {
                        if (!chat.pivot) chat.pivot = {};
                        chat.pivot.is_muted = !chat.pivot.is_muted;
                        alert(chat.pivot.is_muted ? 'Чат заглушен' : 'Звук включен');
                    },

                    markAsRead() {
                        if (!this.activeChat || !this.messages.length) return;

                        const unreadMessages = this.messages
                            .filter(m => m.user_id !== this.userId && (!m.statuses || !m.statuses[0]?.read_at))
                            .map(m => m.id);

                        if (unreadMessages.length) {
                            fetch(`/chat/api/chats/${this.activeChat.id}/mark-read`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({ message_ids: unreadMessages })
                            })
                                .catch(error => {
                                    console.error('Error marking messages as read:', error);
                                });
                        }
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

                        const isNearBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 100;
                        this.showScrollButton = !isNearBottom;
                    },

                    filterChats() {
                        // Computed property handles filtering
                    },

                    formatTime(timestamp) {
                        if (!timestamp) return '';
                        try {
                            const date = new Date(timestamp);
                            const now = new Date();
                            const diff = now - date;

                            if (diff < 24 * 60 * 60 * 1000) {
                                return date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
                            }

                            if (diff < 7 * 24 * 60 * 60 * 1000) {
                                return date.toLocaleDateString('ru-RU', { weekday: 'short' });
                            }

                            return date.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit' });
                        } catch (e) {
                            return '';
                        }
                    },

                    getInitials(name) {
                        if (!name) return '?';
                        return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
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

                    // Cleanup
                    destroy() {
                        this.stopPolling();
                    }
                }
            }

            // Register with Alpine
            if (window.Alpine) {
                Alpine.data('chatApp', chatApp);
            } else {
                document.addEventListener('alpine:init', () => {
                    Alpine.data('chatApp', chatApp);
                });
            }
        })();
    </script>
@endpush
