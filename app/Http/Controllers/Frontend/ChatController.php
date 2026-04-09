<?php
// app/Http/Controllers/Chat/ChatController.php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{

    /**
     * Показать страницу чата
     */
    public function index()
    {
        return view('frontend.chat.index');
    }

    /**
     * Получить список чатов пользователя
     */
    public function getChats(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            // УПРОЩАЕМ ДО МАКСИМУМА
            $chatIds = DB::table('chat_user')
                ->where('user_id', $user->id)
                ->whereNull('left_at')
                ->pluck('chat_id');

            $chats = DB::table('chats')
                ->whereIn('id', $chatIds)
                ->whereNull('deleted_at')
                ->orderBy('updated_at', 'desc')
                ->get();

            $result = [];

            foreach ($chats as $chat) {
                // Получаем участников
                $users = DB::table('chat_user')
                    ->join('users', 'users.id', '=', 'chat_user.user_id')
                    ->where('chat_user.chat_id', $chat->id)
                    ->whereNull('chat_user.left_at')
                    ->select('users.id', 'users.name', 'users.avatar', 'users.last_activity_at', 'chat_user.role', 'chat_user.last_read_at')
                    ->get();

                // Получаем последнее сообщение
                $lastMessage = DB::table('messages')
                    ->where('chat_id', $chat->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Считаем непрочитанные
                $lastRead = null;
                foreach ($users as $u) {
                    if ($u->id == $user->id) {
                        $lastRead = $u->last_read_at;
                        break;
                    }
                }

                $unreadCount = DB::table('messages')
                    ->where('chat_id', $chat->id)
                    ->where('user_id', '!=', $user->id)
                    ->when($lastRead, function($q) use ($lastRead) {
                        $q->where('created_at', '>', $lastRead);
                    })
                    ->count();

                $result[] = [
                    'id' => $chat->id,
                    'name' => $chat->name,
                    'type' => $chat->type,
                    'updated_at' => $chat->updated_at,
                    'unread_count' => $unreadCount,
                    'users' => $users,
                    'last_message' => $lastMessage ? [
                        'content' => $lastMessage->content,
                        'created_at' => $lastMessage->created_at
                    ] : null
                ];
            }

            return response()->json([
                'success' => true,
                'chats' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('getChats error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить сообщения чата
     */
    public function getMessages(Request $request, Chat $chat)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            // Проверяем доступ
            $hasAccess = DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->whereNull('left_at')
                ->exists();

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нет доступа'
                ], 403);
            }

            // Получаем сообщения
            $messages = DB::table('messages')
                ->where('chat_id', $chat->id)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            // Получаем пользователей сообщений
            $userIds = $messages->pluck('user_id')->unique()->toArray();
            $users = DB::table('users')->whereIn('id', $userIds)->get()->keyBy('id');

            $formattedMessages = [];
            foreach ($messages as $message) {
                $formattedMessages[] = [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'content' => $message->content,
                    'type' => $message->type,
                    'created_at' => $message->created_at,
                    'user' => isset($users[$message->user_id]) ? [
                        'id' => $users[$message->user_id]->id,
                        'name' => $users[$message->user_id]->name
                    ] : null
                ];
            }

            // Обновляем last_read
            DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->update(['last_read_at' => now()]);

            return response()->json([
                'success' => true,
                'messages' => ['data' => array_reverse($formattedMessages)],
                'chat' => $chat
            ]);

        } catch (\Exception $e) {
            Log::error('getMessages error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Отправить сообщение
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $hasAccess = DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->whereNull('left_at')
                ->exists();

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому чату'
                ], 403);
            }

            $request->validate([
                'content' => 'required|string|max:5000'
            ]);

            DB::beginTransaction();

            // Создаем сообщение
            $messageId = DB::table('messages')->insertGetId([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'content' => $request->content,
                'type' => 'text',
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Получаем всех участников чата
            $participants = DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->whereNull('left_at')
                ->get();

            // Создаем статусы для всех участников
            foreach ($participants as $participant) {
                DB::table('message_statuses')->insert([
                    'message_id' => $messageId,
                    'user_id' => $participant->user_id,
                    'status' => $participant->user_id === $user->id ? 'read' : 'sent',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Обновляем время чата
            DB::table('chats')
                ->where('id', $chat->id)
                ->update(['updated_at' => now()]);

            DB::commit();

            // Получаем созданное сообщение
            $message = DB::table('messages')->where('id', $messageId)->first();
            $messageUser = DB::table('users')->where('id', $user->id)->first();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'chat_id' => $message->chat_id,
                    'user_id' => $message->user_id,
                    'content' => $message->content,
                    'type' => $message->type,
                    'created_at' => $message->created_at,
                    'user' => [
                        'id' => $messageUser->id,
                        'name' => $messageUser->name,
                        'avatar' => $messageUser->avatar ? Storage::url($messageUser->avatar) : null
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in sendMessage: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отправке сообщения: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Загрузить файл
     */
    public function uploadFile(Request $request, Chat $chat)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $hasAccess = DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->whereNull('left_at')
                ->exists();

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому чату'
                ], 403);
            }

            $request->validate([
                'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar,txt'
            ]);

            $file = $request->file('file');
            $path = $file->store('chat-files/' . $chat->id, 'public');

            DB::beginTransaction();

            // Создаем сообщение
            $messageId = DB::table('messages')->insertGetId([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'type' => 'file',
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'content' => $file->getClientOriginalName(),
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Получаем всех участников чата
            $participants = DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->whereNull('left_at')
                ->get();

            // Создаем статусы для всех участников
            foreach ($participants as $participant) {
                DB::table('message_statuses')->insert([
                    'message_id' => $messageId,
                    'user_id' => $participant->user_id,
                    'status' => $participant->user_id === $user->id ? 'read' : 'sent',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Обновляем время чата
            DB::table('chats')
                ->where('id', $chat->id)
                ->update(['updated_at' => now()]);

            DB::commit();

            // Получаем созданное сообщение
            $message = DB::table('messages')->where('id', $messageId)->first();
            $messageUser = DB::table('users')->where('id', $user->id)->first();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'chat_id' => $message->chat_id,
                    'user_id' => $message->user_id,
                    'content' => $message->content,
                    'type' => $message->type,
                    'file_name' => $message->file_name,
                    'file_url' => Storage::url($message->file_path),
                    'file_size' => $message->file_size,
                    'file_icon' => $this->getFileIcon($message->mime_type),
                    'created_at' => $message->created_at,
                    'user' => [
                        'id' => $messageUser->id,
                        'name' => $messageUser->name,
                        'avatar' => $messageUser->avatar ? Storage::url($messageUser->avatar) : null
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            Log::error('Error in uploadFile: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке файла: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Начать приватный чат
     */
    public function startPrivateChat(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            $recipientId = $request->user_id;
            $recipient = User::find($recipientId);

            if ($user->company_id !== $recipient->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователи должны быть из одной компании'
                ], 403);
            }

            // Ищем существующий чат
            $chatsWithUser1 = DB::table('chat_user')
                ->where('user_id', $user->id)
                ->whereNull('left_at')
                ->pluck('chat_id');

            $chatsWithUser2 = DB::table('chat_user')
                ->where('user_id', $recipientId)
                ->whereNull('left_at')
                ->pluck('chat_id');

            $commonChatIds = array_intersect($chatsWithUser1->toArray(), $chatsWithUser2->toArray());

            $existingChatId = null;
            foreach ($commonChatIds as $chatId) {
                $userCount = DB::table('chat_user')
                    ->where('chat_id', $chatId)
                    ->whereNull('left_at')
                    ->count();

                $chatType = DB::table('chats')->where('id', $chatId)->value('type');

                if ($userCount == 2 && $chatType == 'private') {
                    $existingChatId = $chatId;
                    break;
                }
            }

            if ($existingChatId) {
                $chat = Chat::find($existingChatId);

                // Получаем участников
                $chatUsers = DB::table('chat_user')
                    ->join('users', 'users.id', '=', 'chat_user.user_id')
                    ->where('chat_user.chat_id', $chat->id)
                    ->whereNull('chat_user.left_at')
                    ->select('users.*', 'chat_user.role')
                    ->get();

                $formattedUsers = [];
                foreach ($chatUsers as $cu) {
                    $lastActivity = $cu->last_activity_at ? new \Carbon\Carbon($cu->last_activity_at) : null;
                    $isOnline = $lastActivity && $lastActivity->diffInMinutes(now()) < 2;

                    $formattedUsers[] = [
                        'id' => $cu->id,
                        'name' => $cu->name,
                        'email' => $cu->email,
                        'avatar' => $cu->avatar ? Storage::url($cu->avatar) : null,
                        'initials' => $this->getInitials($cu->name),
                        'avatar_color' => $this->getAvatarColor($cu->name),
                        'is_online' => $isOnline,
                        'last_activity' => $cu->last_activity_at,
                        'role' => $cu->role
                    ];
                }

                return response()->json([
                    'success' => true,
                    'chat' => [
                        'id' => $chat->id,
                        'name' => $chat->name,
                        'type' => $chat->type,
                        'description' => $chat->description,
                        'created_at' => $chat->created_at,
                        'updated_at' => $chat->updated_at,
                        'users' => $formattedUsers
                    ]
                ]);
            }

            // Создаем новый чат
            DB::beginTransaction();

            $chatId = DB::table('chats')->insertGetId([
                'company_id' => $user->company_id,
                'created_by' => $user->id,
                'type' => 'private',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $now = now();
            DB::table('chat_user')->insert([
                [
                    'chat_id' => $chatId,
                    'user_id' => $user->id,
                    'role' => 'admin',
                    'joined_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'chat_id' => $chatId,
                    'user_id' => $recipientId,
                    'role' => 'member',
                    'joined_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ]);

            DB::commit();

            $chat = Chat::find($chatId);

            // Получаем участников
            $chatUsers = DB::table('chat_user')
                ->join('users', 'users.id', '=', 'chat_user.user_id')
                ->where('chat_user.chat_id', $chat->id)
                ->whereNull('chat_user.left_at')
                ->select('users.*', 'chat_user.role')
                ->get();

            $formattedUsers = [];
            foreach ($chatUsers as $cu) {
                $lastActivity = $cu->last_activity_at ? new \Carbon\Carbon($cu->last_activity_at) : null;
                $isOnline = $lastActivity && $lastActivity->diffInMinutes(now()) < 2;

                $formattedUsers[] = [
                    'id' => $cu->id,
                    'name' => $cu->name,
                    'email' => $cu->email,
                    'avatar' => $cu->avatar ? Storage::url($cu->avatar) : null,
                    'initials' => $this->getInitials($cu->name),
                    'avatar_color' => $this->getAvatarColor($cu->name),
                    'is_online' => $isOnline,
                    'last_activity' => $cu->last_activity_at,
                    'role' => $cu->role
                ];
            }

            return response()->json([
                'success' => true,
                'chat' => [
                    'id' => $chat->id,
                    'name' => $chat->name,
                    'type' => $chat->type,
                    'description' => $chat->description,
                    'created_at' => $chat->created_at,
                    'updated_at' => $chat->updated_at,
                    'users' => $formattedUsers
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ERROR in startPrivateChat: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании чата: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Создать групповой чат
     */
    public function createGroupChat(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            // Проверяем права (только руководитель или менеджер)
            $isLeader = $this->isLeader($user);
            $isManager = $this->isManager($user);

            if (!$isLeader && !$isManager) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав для создания группового чата'
                ], 403);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'user_ids' => 'required|array|min:2',
                'user_ids.*' => 'exists:users,id'
            ]);

            // Проверяем, что все пользователи из одной компании
            $usersInCompany = DB::table('users')
                ->whereIn('id', $request->user_ids)
                ->where('company_id', $user->company_id)
                ->count();

            if ($usersInCompany !== count($request->user_ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Все пользователи должны быть из вашей компании'
                ], 422);
            }

            DB::beginTransaction();

            // Создаем чат
            $chatId = DB::table('chats')->insertGetId([
                'name' => $request->name,
                'description' => $request->description,
                'company_id' => $user->company_id,
                'created_by' => $user->id,
                'type' => 'group',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $now = now();

            // Добавляем создателя как админа
            DB::table('chat_user')->insert([
                'chat_id' => $chatId,
                'user_id' => $user->id,
                'role' => 'admin',
                'joined_at' => $now,
                'created_at' => $now,
                'updated_at' => $now
            ]);

            // Добавляем остальных участников
            foreach ($request->user_ids as $userId) {
                if ($userId != $user->id) {
                    DB::table('chat_user')->insert([
                        'chat_id' => $chatId,
                        'user_id' => $userId,
                        'role' => 'member',
                        'joined_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                }
            }

            // Создаем системное сообщение
            DB::table('messages')->insert([
                'chat_id' => $chatId,
                'type' => 'system',
                'content' => "Чат создан пользователем {$user->name}",
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            $chat = Chat::find($chatId);

            // Получаем участников
            $chatUsers = DB::table('chat_user')
                ->join('users', 'users.id', '=', 'chat_user.user_id')
                ->where('chat_user.chat_id', $chat->id)
                ->whereNull('chat_user.left_at')
                ->select('users.*', 'chat_user.role')
                ->get();

            $formattedUsers = [];
            foreach ($chatUsers as $cu) {
                $lastActivity = $cu->last_activity_at ? new \Carbon\Carbon($cu->last_activity_at) : null;
                $isOnline = $lastActivity && $lastActivity->diffInMinutes(now()) < 2;

                $formattedUsers[] = [
                    'id' => $cu->id,
                    'name' => $cu->name,
                    'email' => $cu->email,
                    'avatar' => $cu->avatar ? Storage::url($cu->avatar) : null,
                    'initials' => $this->getInitials($cu->name),
                    'avatar_color' => $this->getAvatarColor($cu->name),
                    'is_online' => $isOnline,
                    'last_activity' => $cu->last_activity_at,
                    'role' => $cu->role
                ];
            }

            return response()->json([
                'success' => true,
                'chat' => [
                    'id' => $chat->id,
                    'name' => $chat->name,
                    'type' => $chat->type,
                    'description' => $chat->description,
                    'created_at' => $chat->created_at,
                    'updated_at' => $chat->updated_at,
                    'users' => $formattedUsers
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in createGroupChat: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании группового чата: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Добавить участников в групповой чат
     */
    public function addUsers(Request $request, Chat $chat)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            // Проверяем права (админ чата или руководитель)
            $isAdmin = DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->where('role', 'admin')
                ->whereNull('left_at')
                ->exists();

            $isLeader = $this->isLeader($user);

            if (!$isAdmin && !$isLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав для добавления участников'
                ], 403);
            }

            // Только для групповых чатов
            if ($chat->type !== 'group') {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя добавлять участников в приватный чат'
                ], 422);
            }

            $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            DB::beginTransaction();

            $now = now();
            $addedUsers = [];

            foreach ($request->user_ids as $userId) {
                // Проверяем, что пользователь еще не в чате
                $existing = DB::table('chat_user')
                    ->where('chat_id', $chat->id)
                    ->where('user_id', $userId)
                    ->whereNull('left_at')
                    ->exists();

                if (!$existing) {
                    // Проверяем, был ли пользователь раньше в чате
                    $previous = DB::table('chat_user')
                        ->where('chat_id', $chat->id)
                        ->where('user_id', $userId)
                        ->first();

                    if ($previous) {
                        // Обновляем существующую запись
                        DB::table('chat_user')
                            ->where('id', $previous->id)
                            ->update([
                                'left_at' => null,
                                'joined_at' => $now,
                                'updated_at' => $now
                            ]);
                    } else {
                        // Создаем новую запись
                        DB::table('chat_user')->insert([
                            'chat_id' => $chat->id,
                            'user_id' => $userId,
                            'role' => 'member',
                            'joined_at' => $now,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                    }

                    $addedUser = DB::table('users')->where('id', $userId)->first();
                    $addedUsers[] = $addedUser->name;
                }
            }

            // Создаем системное сообщение
            if (!empty($addedUsers)) {
                $addedNames = implode(', ', $addedUsers);
                DB::table('messages')->insert([
                    'chat_id' => $chat->id,
                    'type' => 'system',
                    'content' => "Пользователи добавлены в чат: {$addedNames}",
                    'sent_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            // Получаем обновленный список участников
            $chatUsers = DB::table('chat_user')
                ->join('users', 'users.id', '=', 'chat_user.user_id')
                ->where('chat_user.chat_id', $chat->id)
                ->whereNull('chat_user.left_at')
                ->select('users.*', 'chat_user.role')
                ->get();

            $formattedUsers = [];
            foreach ($chatUsers as $cu) {
                $lastActivity = $cu->last_activity_at ? new \Carbon\Carbon($cu->last_activity_at) : null;
                $isOnline = $lastActivity && $lastActivity->diffInMinutes(now()) < 2;

                $formattedUsers[] = [
                    'id' => $cu->id,
                    'name' => $cu->name,
                    'email' => $cu->email,
                    'avatar' => $cu->avatar ? Storage::url($cu->avatar) : null,
                    'initials' => $this->getInitials($cu->name),
                    'avatar_color' => $this->getAvatarColor($cu->name),
                    'is_online' => $isOnline,
                    'last_activity' => $cu->last_activity_at,
                    'role' => $cu->role
                ];
            }

            return response()->json([
                'success' => true,
                'chat' => [
                    'id' => $chat->id,
                    'name' => $chat->name,
                    'type' => $chat->type,
                    'users' => $formattedUsers
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in addUsers: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении участников: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить участника из чата
     */
    public function removeUser(Request $request, Chat $chat)
    {
        try {
            $user = auth()->user();
            $targetUserId = $request->user_id;

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            // Проверяем права
            $isAdmin = DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->where('role', 'admin')
                ->whereNull('left_at')
                ->exists();

            $isLeader = $this->isLeader($user);
            $isSelf = $user->id == $targetUserId;

            if (!$isAdmin && !$isLeader && !$isSelf) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав для удаления участников'
                ], 403);
            }

            // Нельзя удалить последнего админа
            if ($targetUserId != $user->id) {
                $adminCount = DB::table('chat_user')
                    ->where('chat_id', $chat->id)
                    ->where('role', 'admin')
                    ->whereNull('left_at')
                    ->count();

                $targetIsAdmin = DB::table('chat_user')
                    ->where('chat_id', $chat->id)
                    ->where('user_id', $targetUserId)
                    ->where('role', 'admin')
                    ->whereNull('left_at')
                    ->exists();

                if ($adminCount === 1 && $targetIsAdmin) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Нельзя удалить последнего администратора'
                    ], 422);
                }
            }

            DB::beginTransaction();

            // Помечаем пользователя как покинувшего чат
            DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->where('user_id', $targetUserId)
                ->whereNull('left_at')
                ->update(['left_at' => now()]);

            // Создаем системное сообщение
            $targetUser = DB::table('users')->where('id', $targetUserId)->first();

            if ($user->id == $targetUserId) {
                $message = "Пользователь {$targetUser->name} покинул чат";
            } else {
                $message = "Пользователь {$targetUser->name} удален из чата";
            }

            DB::table('messages')->insert([
                'chat_id' => $chat->id,
                'type' => 'system',
                'content' => $message,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Участник удален из чата'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in removeUser: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении участника: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить чат
     */
    public function deleteChat(Chat $chat)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            // Проверяем права (создатель, админ или руководитель)
            $isCreator = $chat->created_by == $user->id;
            $isAdmin = DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->where('role', 'admin')
                ->whereNull('left_at')
                ->exists();
            $isLeader = $this->isLeader($user);

            if (!$isCreator && !$isAdmin && !$isLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав для удаления чата'
                ], 403);
            }

            // Мягкое удаление чата
            DB::table('chats')
                ->where('id', $chat->id)
                ->update(['deleted_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Чат удален'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in deleteChat: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении чата: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Отметить сообщения как прочитанные
     */
    public function markAsRead(Request $request, Chat $chat)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $hasAccess = DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->whereNull('left_at')
                ->exists();

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому чату'
                ], 403);
            }

            $messageIds = $request->message_ids ?? [];

            DB::transaction(function () use ($chat, $user, $messageIds) {
                if (!empty($messageIds)) {
                    DB::table('message_statuses')
                        ->whereIn('message_id', $messageIds)
                        ->where('user_id', $user->id)
                        ->update([
                            'status' => 'read',
                            'read_at' => now(),
                            'updated_at' => now()
                        ]);
                }

                DB::table('chat_user')
                    ->where('chat_id', $chat->id)
                    ->where('user_id', $user->id)
                    ->update(['last_read_at' => now()]);
            });

            return response()->json([
                'success' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error in markAsRead: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отметке сообщений: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить список коллег для нового чата
     */
    public function getColleagues(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $colleagues = DB::table('users')
                ->where('company_id', $user->company_id)
                ->where('id', '!=', $user->id)
                ->where('is_active', true)
                ->get();

            $formattedColleagues = [];
            foreach ($colleagues as $colleague) {
                $lastActivity = $colleague->last_activity_at ? new \Carbon\Carbon($colleague->last_activity_at) : null;
                $isOnline = $lastActivity && $lastActivity->diffInMinutes(now()) < 2;

                $department = DB::table('departments')->where('id', $colleague->department_id)->first();
                $role = DB::table('roles')->where('id', $colleague->role_id)->first();

                $formattedColleagues[] = [
                    'id' => $colleague->id,
                    'name' => $colleague->name,
                    'email' => $colleague->email,
                    'avatar' => $colleague->avatar ? Storage::url($colleague->avatar) : null,
                    'initials' => $this->getInitials($colleague->name),
                    'avatar_color' => $this->getAvatarColor($colleague->name),
                    'department' => $department->name ?? 'Без отдела',
                    'role' => $role->name ?? 'Сотрудник',
                    'is_online' => $isOnline,
                    'last_activity' => $colleague->last_activity_at
                ];
            }

            return response()->json([
                'success' => true,
                'colleagues' => $formattedColleagues
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getColleagues: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке списка сотрудников: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Вспомогательные методы
     */
    private function getInitials($name)
    {
        if (!$name) return '?';
        $words = explode(' ', trim($name));
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            }
        }
        return substr($initials, 0, 2);
    }

    private function getAvatarColor($name)
    {
        $colors = ['blue-500', 'green-500', 'yellow-500', 'red-500', 'purple-500', 'pink-500', 'indigo-500'];
        $hash = crc32($name);
        return $colors[abs($hash) % count($colors)];
    }

    private function getFileIcon($mimeType)
    {
        if (!$mimeType) return 'fa-file';

        if (str_contains($mimeType, 'image')) return 'fa-file-image';
        if (str_contains($mimeType, 'pdf')) return 'fa-file-pdf';
        if (str_contains($mimeType, 'word')) return 'fa-file-word';
        if (str_contains($mimeType, 'excel')) return 'fa-file-excel';
        if (str_contains($mimeType, 'zip') || str_contains($mimeType, 'rar')) return 'fa-file-archive';

        return 'fa-file';
    }

    private function isLeader($user)
    {
        // Проверяем, является ли пользователь руководителем
        $role = DB::table('roles')->where('id', $user->role_id)->first();
        return $role && $role->name === 'Руководитель';
    }

    private function isManager($user)
    {
        // Проверяем, является ли пользователь менеджером
        $role = DB::table('roles')->where('id', $user->role_id)->first();
        return $role && $role->name === 'Менеджер';
    }
}
