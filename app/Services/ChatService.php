<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Models\Department;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use App\Models\ChatUser;
use Illuminate\Support\Facades\Storage;

class ChatService
{
    public function getUserChats(User $user)
    {
        return Chat::forUser($user)
            ->with(['users' => function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id);
            }, 'lastMessage.user', 'lastMessage.statuses'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($chat) use ($user) {
                return $this->formatChat($chat, $user);
            });
    }

    public function getChatMessages(Chat $chat, User $user, int $limit = 50, int $offset = 0)
    {
        return $chat->messages()
            ->with(['user', 'statuses' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('id', 'asc') // Сортировка по возрастанию
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function createPrivateChat(User $user1, User $user2, Company $company)
    {
        // Проверяем, существует ли уже приватный чат
        $existingChat = Chat::where('company_id', $company->id)
            ->where('type', Chat::TYPE_PRIVATE)
            ->whereHas('users', function ($query) use ($user1) {
                $query->where('user_id', $user1->id)->whereNull('left_at');
            })
            ->whereHas('users', function ($query) use ($user2) {
                $query->where('user_id', $user2->id)->whereNull('left_at');
            })
            ->first();

        if ($existingChat) {
            return $existingChat;
        }

        DB::beginTransaction();
        try {
            $chat = Chat::create([
                'type' => Chat::TYPE_PRIVATE,
                'company_id' => $company->id,
                'created_by' => $user1->id,
                'is_active' => true,
            ]);

            $chat->users()->attach($user1->id, [
                'role' => ChatUser::ROLE_ADMIN,
                'joined_at' => now(),
            ]);

            $chat->users()->attach($user2->id, [
                'role' => ChatUser::ROLE_MEMBER,
                'joined_at' => now(),
            ]);

            DB::commit();
            return $chat;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createGroupChat(array $data, User $creator, Company $company)
    {
        DB::beginTransaction();
        try {
            $chat = Chat::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'type' => Chat::TYPE_GROUP,
                'company_id' => $company->id,
                'created_by' => $creator->id,
                'is_active' => true,
            ]);

            // Добавляем создателя как админа
            $chat->users()->attach($creator->id, [
                'role' => ChatUser::ROLE_ADMIN,
                'joined_at' => now(),
            ]);

            // Добавляем остальных пользователей
            foreach ($data['user_ids'] as $userId) {
                if ($userId != $creator->id) {
                    $chat->users()->attach($userId, [
                        'role' => ChatUser::ROLE_MEMBER,
                        'joined_at' => now(),
                    ]);
                }
            }

            // Отправляем системное сообщение о создании группы
            $this->sendSystemMessage($chat, "Группа '{$chat->name}' создана");

            DB::commit();
            return $chat;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createDepartmentChat(Department $department, User $creator)
    {
        DB::beginTransaction();
        try {
            $chat = Chat::create([
                'name' => "Чат отдела: {$department->name}",
                'type' => Chat::TYPE_DEPARTMENT,
                'company_id' => $department->company_id,
                'department_id' => $department->id,
                'created_by' => $creator->id,
                'is_active' => true,
            ]);

            // Добавляем всех пользователей отдела
            foreach ($department->users as $user) {
                $chat->users()->attach($user->id, [
                    'role' => $user->id === $department->supervisor_id ? ChatUser::ROLE_ADMIN : ChatUser::ROLE_MEMBER,
                    'joined_at' => now(),
                ]);
            }

            // Отправляем системное сообщение
            $this->sendSystemMessage($chat, "Чат отдела '{$department->name}' создан");

            DB::commit();
            return $chat;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createCompanyChat(Company $company, User $creator)
    {
        DB::beginTransaction();
        try {
            $chat = Chat::create([
                'name' => "Общий чат компании",
                'type' => Chat::TYPE_COMPANY,
                'company_id' => $company->id,
                'created_by' => $creator->id,
                'is_active' => true,
            ]);

            // Добавляем создателя как админа
            $chat->users()->attach($creator->id, [
                'role' => ChatUser::ROLE_ADMIN,
                'joined_at' => now(),
            ]);

            // Добавляем всех активных пользователей компании
            foreach ($company->users()->where('is_active', true)->get() as $user) {
                // Пропускаем создателя, он уже добавлен
                if ($user->id === $creator->id) {
                    continue;
                }
                $chat->users()->attach($user->id, [
                    'role' => $user->id === $company->user_id ? ChatUser::ROLE_ADMIN : ChatUser::ROLE_MEMBER,
                    'joined_at' => now(),
                ]);
            }

            $this->sendSystemMessage($chat, "Общий чат компании создан");

            DB::commit();
            return $chat;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function sendMessage(Chat $chat, User $user, string $content, string $type = 'text')
    {
        DB::beginTransaction();
        try {
            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'content' => $content,
                'type' => $type,
            ]);

            // Обновляем последнее сообщение в чате
            $chat->update([
                'last_message_id' => $message->id,
                'last_message_at' => now(),
            ]);

            // Создаем статусы для всех участников чата, кроме отправителя
            $chat->users()
                ->where('user_id', '!=', $user->id)
                ->get()
                ->each(function ($participant) use ($message) {
                    $message->statuses()->create([
                        'user_id' => $participant->id,
                        'status' => 'delivered',
                        'delivered_at' => now(),
                    ]);
                });

            DB::commit();
            return $message->load(['user', 'statuses']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function sendSystemMessage(Chat $chat, string $content)
    {
        return Message::create([
            'chat_id' => $chat->id,
            'user_id' => $chat->created_by,
            'content' => $content,
            'type' => 'system',
        ]);
    }

    public function markMessagesAsRead(Chat $chat, User $user, array $messageIds)
    {
        DB::beginTransaction();
        try {
            // Проверяем, что сообщения принадлежат этому чату
            $validMessageIds = Message::where('chat_id', $chat->id)
                ->whereIn('id', $messageIds)
                ->pluck('id')
                ->toArray();

            if (empty($validMessageIds)) {
                DB::rollBack();
                return false;
            }

            // Обновляем статусы сообщений
            \App\Models\MessageStatus::whereIn('message_id', $validMessageIds)
                ->where('user_id', $user->id)
                ->update([
                    'status' => 'read',
                    'read_at' => now(),
                ]);

            // Обновляем время последнего прочтения
            $chat->users()->updateExistingPivot($user->id, [
                'last_read_at' => now(),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function addUsersToChat(Chat $chat, array $userIds, User $admin)
    {
        // Проверяем права админа
        if (!$chat->users()->where('user_id', $admin->id)->wherePivot('role', 'admin')->exists()) {
            throw new \Exception('У вас нет прав для добавления пользователей');
        }

        DB::beginTransaction();
        try {
            foreach ($userIds as $userId) {
                if (!$chat->isUserInChat(User::find($userId))) {
                    $chat->users()->attach($userId, [
                        'role' => ChatUser::ROLE_MEMBER,
                        'joined_at' => now(),
                    ]);
                }
            }

            $userNames = User::whereIn('id', $userIds)->pluck('name')->implode(', ');
            $this->sendSystemMessage($chat, "Добавлены участники: {$userNames}");

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function removeUserFromChat(Chat $chat, User $user, User $admin)
    {
        // Проверяем права
        $isAdmin = $chat->users()->where('user_id', $admin->id)->wherePivot('role', 'admin')->exists();
        $isSelf = $user->id === $admin->id;

        if (!$isAdmin && !$isSelf) {
            throw new \Exception('У вас нет прав для удаления пользователя');
        }

        DB::beginTransaction();
        try {
            $chat->removeUser($user);
            $this->sendSystemMessage($chat, "Пользователь {$user->name} покинул чат");

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteChat(Chat $chat, User $user)
    {
        // Проверяем права (только админ может удалить чат)
        $isAdmin = $chat->users()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();

        if (!$isAdmin) {
            throw new \Exception('У вас нет прав для удаления чата');
        }

        $chat->delete();
        return true;
    }

    private function formatChat(Chat $chat, User $user)
    {
        $unreadCount = $chat->getUnreadCount($user);

        // Берем уже загруженных пользователей
        $users = $chat->users;

        return [
            'id' => $chat->id,
            'name' => $chat->name,
            'display_name' => $chat->getDisplayName(),
            'type' => $chat->type,
            'description' => $chat->description,
            'created_by' => $chat->created_by,
            'users' => $users->map(function ($u) {
                $avatarUrl = null;
                if ($u->avatar && !empty($u->avatar)) {
                    if (Storage::exists($u->avatar)) {
                        $avatarUrl = Storage::url($u->avatar);
                    }
                }
                if (!$avatarUrl && $u->provider_avatar && !empty($u->provider_avatar)) {
                    $avatarUrl = $u->provider_avatar;
                }

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'avatar' => $avatarUrl,
                    'initials' => $u->getInitials(),
                    'avatar_color' => $u->getAvatarColor(),
                    'is_online' => $u->isOnline(),
                    'last_activity' => $u->last_activity_at,
                    'role' => $u->pivot->role,
                ];
            }),
            'last_message' => $chat->lastMessage ? [
                'id' => $chat->lastMessage->id,
                'content' => $chat->lastMessage->content,
                'type' => $chat->lastMessage->type,
                'created_at' => $chat->lastMessage->created_at,
                'user' => [
                    'id' => $chat->lastMessage->user->id,
                    'name' => $chat->lastMessage->user->name,
                    'avatar' => $chat->lastMessage->user->avatar ? Storage::url($chat->lastMessage->user->avatar) : null,
                ],
            ] : null,
            'unread_count' => $unreadCount,
            'pivot' => [
                'role' => $chat->users()->where('user_id', $user->id)->first()?->pivot->role,
                'last_read_at' => $chat->users()->where('user_id', $user->id)->first()?->pivot->last_read_at,
                'is_muted' => $chat->users()->where('user_id', $user->id)->first()?->pivot->is_muted,
            ],
            'updated_at' => $chat->updated_at,
        ];
    }
}
