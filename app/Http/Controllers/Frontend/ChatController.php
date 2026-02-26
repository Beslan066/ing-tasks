<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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

            // Получаем все чаты пользователя
            $chats = $user->chats()
                ->with(['lastMessage' => function($q) {
                    $q->with('user');
                }])
                ->with(['users' => function($q) use ($user) {
                    $q->where('user_id', '!=', $user->id);
                }])
                ->orderBy('updated_at', 'desc')
                ->get();

            // ВРУЧНУЮ считаем непрочитанные сообщения для каждого чата
            foreach ($chats as $chat) {
                $lastRead = $chat->pivot->last_read_at;

                $unreadCount = Message::where('chat_id', $chat->id)
                    ->where('user_id', '!=', $user->id)
                    ->when($lastRead, function ($query) use ($lastRead) {
                        $query->where('created_at', '>', $lastRead);
                    })
                    ->count();

                $chat->unread_count = $unreadCount;
            }

            return response()->json([
                'success' => true,
                'chats' => $chats
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getChats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке чатов'
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
            if (!$chat->hasUser($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому чату'
                ], 403);
            }

            $messages = $chat->messages()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(50);

            // Отмечаем сообщения как доставленные
            foreach ($messages as $message) {
                if ($message->user_id !== $user->id) {
                    try {
                        // Проверяем есть ли статус
                        $status = DB::table('message_statuses')
                            ->where('message_id', $message->id)
                            ->where('user_id', $user->id)
                            ->first();

                        if (!$status) {
                            DB::table('message_statuses')->insert([
                                'message_id' => $message->id,
                                'user_id' => $user->id,
                                'status' => 'delivered',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        } elseif ($status->status === 'sent') {
                            DB::table('message_statuses')
                                ->where('id', $status->id)
                                ->update([
                                    'status' => 'delivered',
                                    'updated_at' => now()
                                ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error marking message as delivered: ' . $e->getMessage());
                    }
                }
            }

            // Обновляем время последнего прочтения
            try {
                DB::table('chat_user')
                    ->where('chat_id', $chat->id)
                    ->where('user_id', $user->id)
                    ->update(['last_read_at' => now()]);
            } catch (\Exception $e) {
                Log::warning('Error updating last read: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'chat' => $chat->load('users')
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMessages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке сообщений'
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

            if (!$chat->hasUser($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому чату'
                ], 403);
            }

            $request->validate([
                'content' => 'required|string|max:5000'
            ]);

            DB::beginTransaction();

            $message = new Message();
            $message->chat_id = $chat->id;
            $message->user_id = $user->id;
            $message->content = $request->content;
            $message->type = 'text';
            $message->save();

            // Создаем статусы для всех участников
            $users = DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->whereNull('left_at')
                ->get();

            foreach ($users as $participant) {
                DB::table('message_statuses')->insert([
                    'message_id' => $message->id,
                    'user_id' => $participant->user_id,
                    'status' => $participant->user_id === $user->id ? 'read' : 'sent',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Обновляем время чата
            $chat->touch();

            DB::commit();

            $message->load('user');

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in sendMessage: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отправке сообщения'
            ], 500);
        }
    }

    /**
     * Загрузить файл
     */
    public function uploadFile(Request $request, Chat $chat)
    {
        $user = auth()->user();

        if (!$chat->hasUser($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет доступа к этому чату'
            ], 403);
        }

        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar,txt' // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('chat-files/' . $chat->id, 'public');

            DB::beginTransaction();

            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'type' => 'file',
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'content' => $file->getClientOriginalName()
            ]);

            // Создаем статусы
            foreach ($chat->users as $participant) {
                $message->statuses()->create([
                    'user_id' => $participant->id,
                    'status' => $participant->id === $user->id ? 'read' : 'sent'
                ]);
            }

            $chat->touch();

            DB::commit();

            $message->load('user');

            $this->broadcastMessage($message, $chat);

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке файла'
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

            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            $recipientId = $request->user_id;

            // Ищем существующий чат
            $existingChat = null;
            $allChats = Chat::where('company_id', $user->company_id)
                ->where('type', 'private')
                ->whereHas('users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereHas('users', function($q) use ($recipientId) {
                    $q->where('user_id', $recipientId);
                })
                ->get();

            foreach ($allChats as $chat) {
                $userIds = $chat->users->pluck('id')->toArray();
                if (count($userIds) == 2 && in_array($user->id, $userIds) && in_array($recipientId, $userIds)) {
                    $existingChat = $chat;
                    break;
                }
            }

            if ($existingChat) {
                return response()->json([
                    'success' => true,
                    'chat' => $existingChat->load('users')
                ]);
            }

            // Создаем новый чат
            DB::beginTransaction();
            try {
                $chat = new Chat();
                $chat->company_id = $user->company_id;
                $chat->created_by = $user->id;
                $chat->type = 'private';
                $chat->save();

                $now = now();
                DB::table('chat_user')->insert([
                    [
                        'chat_id' => $chat->id,
                        'user_id' => $user->id,
                        'role' => 'admin',
                        'joined_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now
                    ],
                    [
                        'chat_id' => $chat->id,
                        'user_id' => $recipientId,
                        'role' => 'member',
                        'joined_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'chat' => $chat->load('users')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('ERROR in startPrivateChat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Создать групповой чат
     */
    public function createGroupChat(Request $request)
    {
        $user = auth()->user();

        // Проверяем права (только руководитель или менеджер)
        if (!$user->isLeader() && !$user->isManagerRole()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для создания группового чата'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'required|array|min:2',
            'user_ids.*' => 'exists:users,id'
        ]);

        // Проверяем, что все пользователи из одной компании
        $usersInCompany = User::whereIn('id', $request->user_ids)
            ->where('company_id', $user->company_id)
            ->count();

        if ($usersInCompany !== count($request->user_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Все пользователи должны быть из вашей компании'
            ], 422);
        }

        $chat = Chat::createGroupChat(
            $request->name,
            $user->id,
            $user->company_id,
            $request->user_ids
        );

        // Создаем системное сообщение
        Message::createSystemMessage(
            $chat->id,
            "Чат создан пользователем {$user->name}"
        );

        return response()->json([
            'success' => true,
            'chat' => $chat->load('users')
        ]);
    }

    /**
     * Добавить участников в групповой чат
     */
    public function addUsers(Request $request, Chat $chat)
    {
        $user = auth()->user();

        // Проверяем права (админ чата или руководитель)
        if (!$chat->isAdmin($user->id) && !$user->isLeader()) {
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

        DB::transaction(function () use ($request, $chat, $user) {
            foreach ($request->user_ids as $userId) {
                // Проверяем, что пользователь еще не в чате
                if (!$chat->hasUser($userId)) {
                    $chat->addUser($userId);

                    // Системное сообщение
                    $addedUser = User::find($userId);
                    Message::createSystemMessage(
                        $chat->id,
                        "Пользователь {$addedUser->name} добавлен {$user->name}"
                    );
                }
            }
        });

        return response()->json([
            'success' => true,
            'chat' => $chat->load('users')
        ]);
    }

    /**
     * Удалить участника из чата
     */
    public function removeUser(Request $request, Chat $chat)
    {
        $user = auth()->user();
        $targetUserId = $request->user_id;

        // Проверяем права
        if (!$chat->isAdmin($user->id) && !$user->isLeader() && $user->id != $targetUserId) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для удаления участников'
            ], 403);
        }

        // Нельзя удалить последнего админа
        if ($targetUserId != $user->id) {
            $adminCount = $chat->users()->wherePivot('role', 'admin')->count();
            $targetIsAdmin = $chat->isAdmin($targetUserId);

            if ($adminCount === 1 && $targetIsAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя удалить последнего администратора'
                ], 422);
            }
        }

        DB::transaction(function () use ($chat, $targetUserId, $user) {
            $removedUser = User::find($targetUserId);

            $chat->removeUser($targetUserId);

            // Системное сообщение
            if ($user->id == $targetUserId) {
                Message::createSystemMessage(
                    $chat->id,
                    "Пользователь {$removedUser->name} покинул чат"
                );
            } else {
                Message::createSystemMessage(
                    $chat->id,
                    "Пользователь {$removedUser->name} удален {$user->name}"
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Участник удален из чата'
        ]);
    }

    /**
     * Удалить чат
     */
    public function deleteChat(Chat $chat)
    {
        $user = auth()->user();

        // Проверяем права (создатель, админ или руководитель)
        if ($chat->created_by != $user->id && !$chat->isAdmin($user->id) && !$user->isLeader()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для удаления чата'
            ], 403);
        }

        $chat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Чат удален'
        ]);
    }

    /**
     * Отметить сообщения как прочитанные
     */
    public function markAsRead(Request $request, Chat $chat)
    {
        try {
            $user = auth()->user();

            if (!$chat->hasUser($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому чату'
                ], 403);
            }

            $messageIds = $request->message_ids ?? [];

            DB::transaction(function () use ($chat, $user, $messageIds) {
                if (!empty($messageIds)) {
                    foreach ($messageIds as $messageId) {
                        DB::table('message_statuses')
                            ->where('message_id', $messageId)
                            ->where('user_id', $user->id)
                            ->update([
                                'status' => 'read',
                                'read_at' => now(),
                                'updated_at' => now()
                            ]);
                    }
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
            Log::error('Error in markAsRead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отметке сообщений'
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

            $colleagues = User::where('company_id', $user->company_id)
                ->where('id', '!=', $user->id)
                ->where('is_active', true)
                ->with('role', 'department')
                ->get()
                ->map(function ($colleague) {
                    return [
                        'id' => $colleague->id,
                        'name' => $colleague->name,
                        'email' => $colleague->email,
                        'avatar' => $colleague->avatar_url,
                        'initials' => $colleague->getInitials(),
                        'avatar_color' => $colleague->getAvatarColor(),
                        'department' => $colleague->department?->name,
                        'role' => $colleague->role?->name,
                        'is_online' => $colleague->isOnline(),
                        'last_activity' => $colleague->last_activity_at
                    ];
                });

            return response()->json([
                'success' => true,
                'colleagues' => $colleagues
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getColleagues: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке списка сотрудников'
            ], 500);
        }
    }

    /**
     * Отправить уведомление через WebSocket
     */
    private function broadcastMessage($message, $chat)
    {
        // Здесь будет интеграция с Pusher или Laravel WebSockets
        // broadcast(new NewMessageEvent($message, $chat))->toOthers();
    }
}
