<?php

namespace App\Traits;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            $model->logActivity('created');
        });

        static::updated(function (Model $model) {
            if ($model->wasChanged()) {
                $model->logActivity('updated');
            }
        });

        static::deleted(function (Model $model) {
            $model->logActivity('deleted');
        });
    }

    protected function logActivity(string $action, array $additionalData = [])
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        // Получаем company_id из модели или из родительской задачи
        $companyId = $this->company_id ?? null;

        // Если company_id нет и это подзадача, берем из родительской
        if (!$companyId && method_exists($this, 'parent') && $this->parent) {
            $companyId = $this->parent->company_id;
            // Обновляем company_id в подзадаче для будущих логов
            if ($companyId && $this->id) {
                $this->update(['company_id' => $companyId]);
            }
        }

        // Если все еще нет company_id, берем из пользователя
        if (!$companyId) {
            $companyId = $user->company_id;
        }

        // Если company_id все еще null, пропускаем логгирование
        if (!$companyId) {
            \Log::warning('Cannot log activity: company_id is null for model ' . get_class($this) . ' id: ' . ($this->id ?? 'new'));
            return;
        }

        $description = $this->generateDescription($action);

        $activityData = [
            'user_id' => $user->id,
            'company_id' => $companyId,
            'subject_type' => get_class($this),
            'subject_id' => $this->id,
            'action' => $this->getActivityAction($action),
            'description' => $description,
            'properties' => $additionalData,
        ];

        // Добавляем old_values и new_values только для обновления
        if ($action === 'updated') {
            $activityData['old_values'] = $this->getOriginal();
            $activityData['new_values'] = $this->getChanges();
        }

        $activity = Activity::create($activityData);

        return $activity;
    }

    protected function getActivityAction(string $action): string
    {
        $modelName = strtolower(class_basename($this));
        return "{$modelName}_{$action}";
    }

    protected function generateDescription(string $action): string
    {
        $user = auth()->user();
        $userName = $user->name;
        $modelName = $this->getModelName();
        $subjectName = $this->getSubjectName();

        return match($action) {
            'created' => "{$userName} создал {$modelName} «{$subjectName}»",
            'updated' => "{$userName} обновил {$modelName} «{$subjectName}»",
            'deleted' => "{$userName} удалил {$modelName} «{$subjectName}»",
            default => "{$userName} изменил {$modelName} «{$subjectName}»"
        };
    }

    protected function getModelName(): string
    {
        return match(class_basename($this)) {
            'Task' => 'задачу',
            'User' => 'пользователя',
            'Invitation' => 'приглашение',
            'File' => 'файл',
            'Department' => 'отдел',
            default => strtolower(class_basename($this))
        };
    }

    protected function getSubjectName(): string
    {
        if (property_exists($this, 'name')) {
            return $this->name;
        }

        if (property_exists($this, 'title')) {
            return $this->title;
        }

        return '#' . $this->id;
    }
}
