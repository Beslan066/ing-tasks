<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'company_id',
        'created_by',
        'type',
        'avatar',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'type' => 'string'
    ];

    /**
     * Получить компанию чата
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Получить создателя чата
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Получить участников чата
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_user')
            ->withPivot(['role', 'last_read_at', 'is_muted', 'joined_at', 'left_at'])
            ->withTimestamps()
            ->wherePivotNull('left_at'); // Только активные участники
    }

    /**
     * Получить всех участников включая покинувших
     */
    public function allUsers()
    {
        return $this->belongsToMany(User::class, 'chat_user')
            ->withPivot(['role', 'last_read_at', 'is_muted', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    /**
     * Получить сообщения чата
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Получить последнее сообщение
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Получить непрочитанные сообщения для пользователя
     */
    public function unreadMessagesForUser($userId)
    {
        $userPivot = $this->users()
            ->where('user_id', $userId)
            ->first();

        if (!$userPivot || !$userPivot->pivot->last_read_at) {
            return $this->messages();
        }

        return $this->messages()
            ->where('created_at', '>', $userPivot->pivot->last_read_at)
            ->where('user_id', '!=', $userId); // Не считаем свои сообщения
    }

    /**
     * Проверить, является ли пользователь участником
     */
    public function hasUser($userId)
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    /**
     * Проверить, является ли пользователь админом
     */
    public function isAdmin($userId)
    {
        $user = $this->users()
            ->where('user_id', $userId)
            ->first();

        return $user && $user->pivot->role === 'admin';
    }

    /**
     * Добавить участника
     */
    public function addUser($userId, $role = 'member')
    {
        return $this->users()->attach($userId, [
            'role' => $role,
            'joined_at' => now()
        ]);
    }

    /**
     * Удалить участника (выход)
     */
    public function removeUser($userId)
    {
        return $this->allUsers()
            ->where('user_id', $userId)
            ->update(['left_at' => now()]);
    }

    /**
     * Обновить время последнего прочтения
     */
    public function updateLastRead($userId)
    {
        return $this->users()
            ->where('user_id', $userId)
            ->updateExistingPivot($userId, [
                'last_read_at' => now()
            ]);
    }

    /**
     * Получить приватный чат между двумя пользователями
     */
    public static function getPrivateChat($user1Id, $user2Id, $companyId)
    {
        // Ищем существующий приватный чат
        $chat = self::where('company_id', $companyId)
            ->where('type', 'private')
            ->whereHas('users', function ($query) use ($user1Id) {
                $query->where('user_id', $user1Id);
            })
            ->whereHas('users', function ($query) use ($user2Id) {
                $query->where('user_id', $user2Id);
            })
            ->withCount('users')
            ->having('users_count', 2)
            ->first();

        if ($chat) {
            return $chat;
        }

        // Создаем новый чат
        $chat = self::create([
            'company_id' => $companyId,
            'created_by' => $user1Id,
            'type' => 'private'
        ]);

        // Добавляем участников
        $chat->addUser($user1Id, 'admin');
        $chat->addUser($user2Id, 'member');

        return $chat;
    }

    /**
     * Создать групповой чат
     */
    public static function createGroupChat($name, $createdBy, $companyId, array $userIds)
    {
        $chat = self::create([
            'name' => $name,
            'company_id' => $companyId,
            'created_by' => $createdBy,
            'type' => 'group'
        ]);

        // Добавляем создателя как админа
        $chat->addUser($createdBy, 'admin');

        // Добавляем остальных участников
        foreach ($userIds as $userId) {
            if ($userId != $createdBy) {
                $chat->addUser($userId, 'member');
            }
        }

        return $chat;
    }

    /**
     * Получить URL аватара
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }

        // Для групповых чатов показываем иконку группы
        if ($this->type === 'group') {
            return asset('images/group-avatar-default.png');
        }

        // Для приватных чатов показываем аватар собеседника
        return null;
    }

    /**
     * Получить название чата для отображения
     */
    public function getDisplayNameAttribute()
    {
        if ($this->type === 'group' && $this->name) {
            return $this->name;
        }

        // Для приватных чатов показываем имя собеседника
        if ($this->type === 'private' && auth()->check()) {
            $otherUser = $this->users()
                ->where('user_id', '!=', auth()->id())
                ->first();

            return $otherUser ? $otherUser->name : 'Чат';
        }

        return $this->name ?? 'Чат';
    }
}
