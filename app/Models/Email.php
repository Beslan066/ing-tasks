<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Email extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'subject',
        'body',
        'from_email',
        'from_name',
        'to_emails',
        'cc_emails',
        'bcc_emails',
        'sender_id', // Кто отправил (user_id)
        'recipient_id', // Кому адресовано (user_id или department_id)
        'recipient_type', // 'user' или 'department'
        'parent_id',
        'is_read',
        'is_archived',
        'is_draft',
        'is_important',
        'has_attachments',
        'sent_at',
        'received_at',
        'deleted_by',
        'delete_reason',
    ];

    protected $casts = [
        'to_emails' => 'array',
        'cc_emails' => 'array',
        'bcc_emails' => 'array',
        'is_read' => 'boolean',
        'is_archived' => 'boolean',
        'is_draft' => 'boolean',
        'is_important' => 'boolean',
        'has_attachments' => 'boolean',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // === СКОПЫ ДЛЯ ФИЛЬТРАЦИИ ===

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('subject', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%")
                ->orWhere('from_email', 'like', "%{$search}%")
                ->orWhere('from_name', 'like', "%{$search}%")
                ->orWhereJsonContains('to_emails', $search);
        });
    }

    public function scopeFrom($query, $email)
    {
        return $query->where('from_email', 'like', "%{$email}%");
    }

    public function scopeTo($query, $email)
    {
        return $query->whereJsonContains('to_emails', $email);
    }

    public function scopeWithAttachments($query)
    {
        return $query->where('has_attachments', true);
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeDrafts($query)
    {
        return $query->where('is_draft', true);
    }

    public function scopeSent($query)
    {
        return $query->whereNotNull('sent_at')->where('is_draft', false);
    }

    public function scopeReceived($query)
    {
        return $query->whereNotNull('received_at');
    }

    public function scopeWithTags($query, array $tagIds)
    {
        return $query->whereHas('tags', function ($q) use ($tagIds) {
            $q->whereIn('tags.id', $tagIds);
        });
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sent_at', [$startDate, $endDate]);
    }

    // === СВЯЗИ ===

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // Полиморфная связь получателя
    public function recipient()
    {
        return $this->morphTo();
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function deletedBy(): BelongsTo // Добавить
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(EmailFile::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'email_tag');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Email::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Email::class, 'parent_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(EmailNotification::class);
    }

    // === МЕТОДЫ ===

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    public function markAsImportant(): void
    {
        $this->update(['is_important' => true]);
    }

    public function markAsUnimportant(): void
    {
        $this->update(['is_important' => false]);
    }

    public function toggleArchive(): void
    {
        $this->update(['is_archived' => !$this->is_archived]);
    }

    public function addTag(Tag $tag): void
    {
        $this->tags()->syncWithoutDetaching($tag->id);
    }

    public function removeTag(Tag $tag): void
    {
        $this->tags()->detach($tag->id);
    }

    public function getRecipientsCount(): int
    {
        return count($this->to_emails ?? [])
            + count($this->cc_emails ?? [])
            + count($this->bcc_emails ?? []);
    }

    public function getAttachmentsSize(): int
    {
        return $this->files->sum(function ($emailFile) {
            return $emailFile->file->size ?? 0;
        });
    }

    // === МЕТОДЫ ДЛЯ УДАЛЕНИЯ ===

    /**
     * Мягкое удаление письма с сохранением информации
     */
    public function softDelete(User $deletedBy, ?string $reason = null): bool
    {
        return $this->update([
            'deleted_by' => $deletedBy->id,
            'delete_reason' => $reason,
            'deleted_at' => now(),
        ]);
    }

    /**
     * Восстановление письма
     */
    public function restoreEmail(): bool
    {
        return $this->update([
            'deleted_by' => null,
            'delete_reason' => null,
            'deleted_at' => null,
        ]);
    }

    /**
     * Полное удаление письма (только для администраторов)
     */
    public function forceDeleteEmail(): bool
    {
        // Удаляем связанные файлы
        foreach ($this->files as $emailFile) {
            $file = $emailFile->file;
            if ($file) {
                \Illuminate\Support\Facades\Storage::disk($file->disk)->delete($file->path);
                $file->forceDelete();
            }
        }

        // Удаляем связанные уведомления
        $this->notifications()->forceDelete();

        // Удаляем связи с тегами
        $this->tags()->detach();

        // Полное удаление письма
        return $this->forceDelete();
    }

    /**
     * Проверяет, удалено ли письмо
     */
    public function isDeleted(): bool
    {
        return !is_null($this->deleted_at);
    }

    /**
     * Получает информацию об удалении
     */
    public function getDeleteInfo(): array
    {
        if (!$this->isDeleted()) {
            return [];
        }

        return [
            'deleted_at' => $this->deleted_at,
            'deleted_by' => $this->deletedBy ? $this->deletedBy->name : 'Неизвестно',
            'reason' => $this->delete_reason,
        ];
    }

    // === СКОПЫ ===

    public function scopeOnlyTrashed($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    public function scopeWithTrashed($query)
    {
        return $query->withTrashed();
    }

    public function scopeWithoutTrashed($query)
    {
        return $query->whereNull('deleted_at');
    }
}
