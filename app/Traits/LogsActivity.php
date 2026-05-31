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
        $companyId = $this->company_id ?? $user->company_id;

        if (!$companyId) {
            return;
        }

        $description = $this->generateDescription($action);

        $activity = Activity::create(array_merge([
            'user_id' => $user->id,
            'company_id' => $companyId,
            'subject_type' => get_class($this),
            'subject_id' => $this->id,
            'action' => $this->getActivityAction($action),
            'description' => $description,
            'properties' => $additionalData,
            'old_values' => $action === 'updated' ? $this->getOriginal() : null,
            'new_values' => $action === 'updated' ? $this->getChanges() : null,
        ], $additionalData));

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
