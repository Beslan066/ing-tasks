<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Приватный канал для задач компании
Broadcast::channel('company.{companyId}.tasks', function ($user, $companyId) {
    // Проверяем, что пользователь принадлежит компании
    return (int) $user->company_id === (int) $companyId;
});

// Presence канал для отслеживания онлайн пользователей
Broadcast::channel('company.{companyId}.tasks', function ($user, $companyId) {
    if ((int) $user->company_id === (int) $companyId) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role->name ?? 'user',
        ];
    }
});
