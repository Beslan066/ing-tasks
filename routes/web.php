<?php

use App\Http\Controllers\Admin\UserLocationController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\Frontend\ChatController;
use App\Http\Controllers\Frontend\CompanyController;
use App\Http\Controllers\Frontend\EmailTrashController;
use App\Http\Controllers\Frontend\EventController;
use App\Http\Controllers\Frontend\FileStorageController;
use App\Http\Controllers\Frontend\DepartmentEmailController;
use App\Http\Controllers\Frontend\EmailTemplateController;
use App\Http\Controllers\Frontend\LicenceAndPaymentController;
use App\Http\Controllers\Frontend\PersonalEmailController;
use App\Http\Controllers\Frontend\TeamController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Frontend\SmtpSettingController;
use App\Http\Controllers\SupportController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


Route::post('/licence/payment/webhook', [LicenceAndPaymentController::class, 'paymentWebhook'])
    ->name('payment.webhook');

// Тестовый маршрут для ручного подтверждения платежа (для отладки)
Route::get('/licence/payment/check/{paymentId}', [LicenceAndPaymentController::class, 'checkPaymentStatus'])
    ->name('licence.payment.check')
    ->middleware(['auth', 'verified']);

Route::get('/licence/payment/activate/{paymentId}', [LicenceAndPaymentController::class, 'manualActivate'])
    ->name('licence.payment.activate')
    ->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->post('/track-work-time', function (Request $request) {
    $user = auth()->user();
    $workSeconds = (int)$request->input('work_seconds', 0);

    \Log::info('Track work time', [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'work_seconds' => $workSeconds,
        'datetime' => now()->toDateTimeString()
    ]);

    if ($workSeconds > 0) {
        $today = now()->toDateString();

        // Находим или создаем запись за сегодня
        $visit = \App\Models\UserVisit::firstOrCreate(
            [
                'user_id' => $user->id,
                'date' => $today,
            ],
            [
                'first_visit_at' => now(),
                'last_visit_at' => now(),
                'page_views' => 0,
                'total_time_seconds' => 0,
                'total_work_seconds' => 0,
            ]
        );

        // Добавляем время работы
        $visit->increment('total_work_seconds', $workSeconds);

        // Также для обратной совместимости обновляем total_time_seconds
        $visit->increment('total_time_seconds', $workSeconds);
        $visit->update(['last_visit_at' => now()]);

        \Log::info('Work time saved', [
            'user_id' => $user->id,
            'added' => $workSeconds,
            'total_today' => $visit->total_work_seconds
        ]);

        return response()->json([
            'success' => true,
            'added' => $workSeconds,
            'total_today' => $visit->total_work_seconds
        ]);
    }

    return response()->json(['success' => false, 'message' => 'No time to save']);
})->name('track.work.time');



Route::get('/', [\App\Http\Controllers\Frontend\HomeController::class, 'home'])->name('index');
// Домашняя страница
Route::middleware(['auth', 'checkUserRole', 'verified', 'trackUserActivity'])->group(function () {
    Route::get('/home', [\App\Http\Controllers\Frontend\HomeController::class, 'index'])->name('welcome');

    // Роут для типа показа задача в main
    Route::post('/set-view-mode', [App\Http\Controllers\Frontend\HomeController::class, 'setViewMode'])->name('tasks.set-view-mode');
    Route::get('/team/tasks/kanban-data', [App\Http\Controllers\Frontend\HomeController::class, 'getKanbanTasksAjax'])->name('tasks.kanban-data');



    Route::get('/all-tasks', [App\Http\Controllers\Frontend\HomeController::class, 'allTasks'])->middleware('require.company')->name('allTasks');
    Route::get('/team/all-tasks', [App\Http\Controllers\Frontend\HomeController::class, 'allTeamTasks'])->middleware('require.company')->name('allTeamTasks');
    Route::get('/tools', [\App\Http\Controllers\Frontend\ToolController::class, 'index'])->middleware('require.company')->name('tools.index');

    // Лицензия и оплата
    Route::get('/licence-and-payments', [LicenceAndPaymentController::class, 'index'])->middleware('require.company')->name('licence.index');
    Route::post('/licence/payment/premium', [LicenceAndPaymentController::class, 'createPremiumPayment'])
        ->name('licence.payment.premium');

    Route::post('/licence/payment/additional-users', [LicenceAndPaymentController::class, 'createAdditionalUsersPayment'])
        ->name('licence.payment.additional-users');

    // Маршрут для улучшения подписки компании
    Route::post('/company/upgrade-license', [CompanyController::class, 'upgradeLicense'])
        ->name('company.upgrade-license');

    Route::get('/licence/payment/callback', [LicenceAndPaymentController::class, 'paymentCallback'])
        ->name('payment.callback');

    // Лента событий
    Route::get('/activity', [App\Http\Controllers\Frontend\ActivityFeedController::class, 'index'])
        ->name('activity.index');

    Route::get('/activity/{activity}', [App\Http\Controllers\Frontend\ActivityFeedController::class, 'show'])
        ->name('activity.show');


    Route::post('/support/send', [SupportController::class, 'send'])->name('support.send');

    //Индексная страница для новостей на фронте
    Route::get('/news', [\App\Http\Controllers\Frontend\NewsController::class, 'index'])->middleware('require.company')->name('frontend.news.index');


    // Админская страница для руководителей и менеджеров
    Route::get('/team/tasks', [\App\Http\Controllers\Frontend\HomeController::class, 'indexAdmin'])->name('tasks.admin');

    Route::post('/update-activity', function () {
        $user = auth()->user();

        if ($user) {
            $user->update(['last_activity_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Активность обновлена',
                'last_activity_at' => $user->last_activity_at->format('H:i:s'),
                'is_online' => $user->isOnline()
            ]);
        }

        return response()->json(['success' => false], 401);
    })->name('update.activity');

    Route::post('/user/background', [UserController::class, 'updateBackground'])->name('user.updateBackground');


    Route::get('/get-online-users', function () {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['onlineUsers' => [], 'onlineUsersCount' => 0]);
        }

        $onlineUsers = \App\Models\User::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->orderBy('last_activity_at', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($onlineUser) {
                return [
                    'id' => $onlineUser->id,
                    'name' => $onlineUser->name,
                    'initials' => \App\Providers\ViewServiceProvider::generateInitials($onlineUser->name),
                    'color' => \App\Providers\ViewServiceProvider::generateColorFromName($onlineUser->name),
                    'is_online' => true,
                    'last_activity_text' => $onlineUser->getLastActivityText(),
                ];
            });

        $onlineUsersCount = \App\Models\User::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->count();

        return response()->json([
            'onlineUsers' => $onlineUsers,
            'onlineUsersCount' => $onlineUsersCount
        ]);
    })->name('get.online.users');


    Route::post('/user-leaving', function (Request $request) {
        $user = auth()->user();

        if ($user) {
            // Ставим метку, что пользователь ушел
            // Можно установить время на 1-2 минуты назад, чтобы сразу показывать оффлайн
            $user->update([
                'last_activity_at' => now()->subMinutes(2)
            ]);

            // Или альтернатива: удаляем last_activity_at
            // $user->update(['last_activity_at' => null]);

            \Log::info("User {$user->id} left the page");
        }

        return response()->json(['success' => true]);
    });

    Route::post('/user-hidden', function (Request $request) {
        $user = auth()->user();

        if ($user) {
            // Если вкладка скрыта, обновляем активность реже
            // или ставим время активности на 1 минуту назад
            $user->update([
                'last_activity_at' => now()->subMinutes(1)
            ]);
        }

        return response()->json(['success' => true]);
    });

    Route::post('/user-inactive', function (Request $request) {
        $user = auth()->user();

        if ($user) {
            // Пользователь неактивен 30+ секунд
            $user->update([
                'last_activity_at' => now()->subMinutes(1)
            ]);
        }

        return response()->json(['success' => true]);
    });

});


Route::middleware(['auth', 'verified', 'trackUserActivity', 'require.company'])->group(function () {

    Route::middleware(['auth', 'chat.access'])->group(function () {
        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    });

// API маршруты тоже нужно защитить
    Route::prefix('chat')->middleware(['auth', 'chat.access'])->group(function () {
        Route::get('/chats', [ChatApiController::class, 'getChats']);
        Route::get('/chats/unread-counts', [ChatApiController::class, 'getUnreadCounts']);
        Route::get('/colleagues', [ChatApiController::class, 'getColleagues']);

        Route::post('/private-chat', [ChatApiController::class, 'createPrivateChat']);
        Route::post('/group-chat', [ChatApiController::class, 'createGroupChat']);
        Route::post('/department-chat', [ChatApiController::class, 'createDepartmentChat']);
        Route::post('/company-chat', [ChatApiController::class, 'createCompanyChat']);

        Route::prefix('/chats/{chat}')->group(function () {
            Route::get('/messages', [ChatApiController::class, 'getMessages']);
            Route::post('/send', [ChatApiController::class, 'sendMessage']);
            Route::post('/upload', [ChatApiController::class, 'uploadFile']);
            Route::post('/mark-read', [ChatApiController::class, 'markRead']);
            Route::post('/add-users', [ChatApiController::class, 'addUsers']);
            Route::post('/remove-user', [ChatApiController::class, 'removeUser']);
            Route::get('/available-users', [ChatApiController::class, 'getAvailableUsers']);
            Route::delete('/', [ChatApiController::class, 'deleteChat']);
        });
    });

// Отдельный маршрут для отделов без проверки чата
    Route::get('/chat-departments', [App\Http\Controllers\Api\DepartmentController::class, 'index']);


    Route::prefix('files')->group(function () {
        Route::get('/', [FileStorageController::class, 'index'])->name('files.index');
        Route::post('/upload', [FileStorageController::class, 'upload'])->name('files.upload');
        Route::get('/download/{file}', [FileStorageController::class, 'download'])->name('files.download');
        Route::get('/view/{file}', [FileStorageController::class, 'view'])->name('files.view');
        Route::delete('/delete/{file}', [FileStorageController::class, 'destroy'])->name('files.destroy');
        Route::get('/statistics', [FileStorageController::class, 'getStatistics'])->name('files.statistics');

        // Маршрут для удаления файлов
        Route::delete('/files/{file}', [App\Http\Controllers\Frontend\TaskController::class, 'deleteFile'])->name('files.delete');
        Route::post('/files/upload-ajax', [FileStorageController::class, 'uploadAjax'])->name('files.upload.ajax');
    });

    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::post('/', [EventController::class, 'store'])->name('store');
        Route::put('/{event}', [EventController::class, 'update'])->name('update');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
        Route::post('/{event}/respond', [EventController::class, 'respond'])->name('respond');
        Route::get('/list', [EventController::class, 'getEvents'])->name('list');
    });
});

Route::get('/create-company', [\App\Http\Controllers\Frontend\HomeController::class, 'noCompanies'])->middleware(['auth', 'verified', 'trackUserActivity'])->name('no.companies');

// Публичные маршруты для приглашений (доступны без авторизации)
Route::get('/invitation/{token}', [InvitationController::class, 'showInvitationForm'])->name('invitation.accept');
Route::post('/invitation/{token}/accept', [InvitationController::class, 'acceptInvitation'])->name('invitation.process');


Route::get('/team/tasks/{task}', function ($task) {
    return redirect()->to('/team/tasks?open_task=' . $task);
})->name('tasks.admin.with-modal');
// ВСЕ МАРШРУТЫ ДЛЯ ЗАДАЧ
Route::group(['prefix' => 'tasks', 'middleware' => ['auth', 'verified', 'trackUserActivity', 'require.company']], function () {
    // Основные CRUD маршруты
    Route::get('/', [App\Http\Controllers\Frontend\TaskController::class, 'index'])->name('tasks.index');
    Route::get('/create', [App\Http\Controllers\Frontend\TaskController::class, 'create'])->name('tasks.create');
    Route::post('/store', [App\Http\Controllers\Frontend\TaskController::class, 'store'])->name('tasks.store');

    // Задачи себе
    Route::post('/personal/store', [App\Http\Controllers\Frontend\TaskController::class, 'storePersonal'])->name('tasks.personal.store');

    Route::patch('/{task}/archive', [App\Http\Controllers\Frontend\TaskController::class, 'archive'])->name('tasks.archive');
    Route::patch('/{taskId}/restore', [App\Http\Controllers\Frontend\TaskController::class, 'restore'])->name('tasks.restore');
    Route::delete('/{taskId}/force-delete', [App\Http\Controllers\Frontend\TaskController::class, 'forceDelete'])->name('tasks.force-delete');

    // Модальное окно (AJAX)
    Route::get('/{task}', [App\Http\Controllers\Frontend\TaskController::class, 'view'])->name('tasks.show');

    // Маршруты для редактирования задачи
    Route::get('/{task}/get', [App\Http\Controllers\Frontend\TaskController::class, 'getTask'])->name('tasks.get');
    Route::post('/{task}/update', [App\Http\Controllers\Frontend\TaskController::class, 'update'])->name('tasks.update');

    // Подзадачи
    Route::post('/{task}/subtasks', [App\Http\Controllers\Frontend\TaskController::class, 'storeSubtask'])->name('tasks.subtasks.store');
    Route::post('/subtasks/{subtask}/toggle', [App\Http\Controllers\Frontend\TaskController::class, 'toggleSubtaskStatus'])->name('subtasks.toggle');
    Route::delete('/subtasks/{subtask}', [App\Http\Controllers\Frontend\TaskController::class, 'deleteSubtask'])->name('subtasks.delete');

    // МАРШРУТЫ ДЛЯ УПРАВЛЕНИЯ ЗАДАЧАМИ СОТРУДНИКАМИ
    Route::post('/{task}/take', [App\Http\Controllers\Frontend\TaskController::class, 'takeTask'])->name('tasks.take');
    Route::patch('/{task}/status', [App\Http\Controllers\Frontend\TaskController::class, 'updateTaskStatus'])->name('tasks.status');
    Route::post('/{task}/reject', [App\Http\Controllers\Frontend\TaskController::class, 'rejectTask'])->name('tasks.reject');
    Route::post('/{task}/attach-file', [App\Http\Controllers\Frontend\TaskController::class, 'attachFile'])->name('tasks.attach-file');

    // МАРШРУТЫ ДЛЯ АДМИНИСТРАТОРОВ
    Route::post('/{task}/return-to-work', [App\Http\Controllers\Frontend\TaskController::class, 'returnToWork'])->name('admin.tasks.return-to-work');
    Route::delete('/{task}/delete', [App\Http\Controllers\Frontend\TaskController::class, 'destroy'])->name('admin.tasks.delete');
    Route::post('/{task}/add-files', [App\Http\Controllers\Frontend\TaskController::class, 'addFiles'])->name('tasks.add-files');
    Route::get('/file-storage/get-files', [App\Http\Controllers\Frontend\TaskController::class, 'getFiles'])->name('file-storage.get-files');

    Route::get('/{task}/comments', [App\Http\Controllers\Frontend\TaskCommentController::class, 'index']);
    Route::post('/{task}/comments', [App\Http\Controllers\Frontend\TaskCommentController::class, 'store']);
    Route::put('/{task}/comments/{comment}', [App\Http\Controllers\Frontend\TaskCommentController::class, 'update']);
    Route::delete('/{task}/comments/{comment}', [App\Http\Controllers\Frontend\TaskCommentController::class, 'destroy']);
});

Route::middleware(['auth', 'verified', 'trackUserActivity', 'require.company'])->group(function () {
    Route::get('/photobank', [\App\Http\Controllers\Frontend\PhotobankController::class, 'index'])->name('photobank');
    Route::post('/photobank/categories', [\App\Http\Controllers\Frontend\PhotobankController::class, 'createCategory'])->name('photobank.categories.store');
    Route::post('/photobank/tags', [\App\Http\Controllers\Frontend\PhotobankController::class, 'createTag'])->name('photobank.tags.store');
    Route::post('/photobank/photos', [\App\Http\Controllers\Frontend\PhotobankController::class, 'storePhoto'])->name('photobank.photos.store');
    Route::get('/photobank/categories', [\App\Http\Controllers\Frontend\PhotobankController::class, 'getCategories'])->name('photobank.categories.index');
    Route::get('/photobank/tags', [\App\Http\Controllers\Frontend\PhotobankController::class, 'getTags'])->name('photobank.tags.index');
    Route::post('/photobank/photos/{photo}/convert', [\App\Http\Controllers\Frontend\PhotobankController::class, 'convertImage'])
        ->name('photobank.photos.convert');

    Route::put('/photobank/photos/{photo}', [\App\Http\Controllers\Frontend\PhotobankController::class, 'update'])
        ->name('photobank.photos.update');

    // Изменение размера
    Route::post('/photobank/photos/{photo}/resize', [\App\Http\Controllers\Frontend\PhotobankController::class, 'resizeImage'])
        ->name('photobank.photos.resize');

    // Изменение соотношения сторон
    Route::post('/photobank/photos/{photo}/aspect-ratio', [\App\Http\Controllers\Frontend\PhotobankController::class, 'changeAspectRatio'])
        ->name('photobank.photos.aspect');

    // Удаление фото
    Route::delete('/photobank/photos/{photo}', [\App\Http\Controllers\Frontend\PhotobankController::class, 'destroy'])
        ->name('photobank.photos.destroy');

    // Восстановление фото
    Route::patch('/photobank/photos/{id}/restore', [\App\Http\Controllers\Frontend\PhotobankController::class, 'restore'])
        ->name('photobank.photos.restore');

    // Полное удаление (только админ)
    Route::delete('/photobank/photos/{id}/force', [\App\Http\Controllers\Frontend\PhotobankController::class, 'forceDelete'])
        ->name('photobank.photos.force');

    Route::post('/category/create', [App\Http\Controllers\Frontend\CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{id}/edit', [App\Http\Controllers\Frontend\CategoryController::class, 'edit'])->name('category.edit');
    Route::patch('/category/update', [App\Http\Controllers\Frontend\CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/delete', [App\Http\Controllers\Frontend\CategoryController::class, 'destroy'])->name('category.destroy');
});


Route::group(['prefix' => 'departments', 'middleware' => ['auth', 'verified', 'trackUserActivity', 'require.company']], function () {
    Route::get('/', [App\Http\Controllers\Frontend\DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/store', [App\Http\Controllers\Frontend\DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/{id}/edit', [App\Http\Controllers\Frontend\DepartmentController::class, 'edit'])->name('departments.edit');
    Route::patch('/update', [App\Http\Controllers\Frontend\DepartmentController::class, 'update'])->name('departments.update');
    // удаление V
    Route::delete('/{id}/delete', [App\Http\Controllers\Frontend\DepartmentController::class, 'destroy'])->name('departments.destroy');
});

// Маршруты для команды с системой приглашений
Route::middleware(['auth', 'verified', 'trackUserActivity', 'require.company'])->group(function () {
    Route::prefix('team')->name('team.')->group(function () {
        // Основные маршруты команды
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::delete('/{user}', [TeamController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-destroy', [TeamController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::get('/export', [TeamController::class, 'exportTable'])->name('export-table');
        Route::get('/print', [TeamController::class, 'printTable'])->name('print-table');

        Route::get('/departments/list', [TeamController::class, 'getDepartmentsList'])->name('team.departments.list');
        Route::put('/user/{user}/departments', [TeamController::class, 'updateUserDepartments'])->name('team.user.departments.update');

        Route::put('/user/{user}/role', [App\Http\Controllers\Frontend\TeamController::class, 'updateUserRole'])->name('team.update-role');
        Route::get('/roles/list', [App\Http\Controllers\Frontend\TeamController::class, 'getRolesList'])
            ->name('team.roles.list')
            ->middleware('auth');

        // Маршруты для работы с пользователями
        Route::get('/user/{user}', [TeamController::class, 'getUserDetails'])->name('user.details');
        Route::get('/user/{user}/print', [TeamController::class, 'printUserDetails'])->name('user.print');
        Route::get('/user/{user}/tasks', [TeamController::class, 'getUserTasks'])->name('user.tasks');
        Route::get('/user/{user}/export', [TeamController::class, 'exportUserStats'])->name('user.export');

        // Новые маршруты для статистики
        Route::get('/user/{userId}/detailed-stats', [TeamController::class, 'getUserDetailedStats'])->name('team.user.detailed-stats');
        Route::get('/user/{userId}/export-stats', [TeamController::class, 'exportUserStatsToCsv'])->name('team.user.export-stats');

        // Маршруты для системы приглашений
        Route::post('/invite', [InvitationController::class, 'invite'])->name('invite');
        Route::get('/invitations', [InvitationController::class, 'getInvitations'])->name('invitations');
        Route::delete('/invitations/{id}', [InvitationController::class, 'cancelInvitation'])->name('invitations.cancel');
        Route::get('/invitations/search', [InvitationController::class, 'searchUsers'])->name('invitations.search');
    });
});

Route::group(['prefix' => 'companies', 'middleware' => ['auth', 'verified', 'trackUserActivity']], function () {
    Route::get('/', [App\Http\Controllers\Frontend\CompanyController::class, 'index'])->name('companies.index');
    Route::get('/create', [App\Http\Controllers\Frontend\CompanyController::class, 'create'])->name('companies.create');
    Route::post('/store', [App\Http\Controllers\Frontend\CompanyController::class, 'store'])->name('companies.store');
});

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'verified', 'trackUserActivity']], function () {
    Route::get('/', [App\Http\Controllers\Frontend\UserController::class, 'index'])->name('users.index');
    Route::get('/create', [App\Http\Controllers\Frontend\UserController::class, 'create'])->name('users.create');
    Route::post('/store', [App\Http\Controllers\Frontend\UserController::class, 'store'])->name('users.store');
});

Route::middleware(['auth', 'trackUserActivity'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
});


// Роуты админки
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('/', [\App\Http\Controllers\Admin\IndexController::class, 'index'])->name('admin.index');


    Route::group(['namespace' => 'News', 'prefix' => 'news'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\NewsController::class, 'index'])->name('admin.news.index');
        Route::get('/create', [\App\Http\Controllers\Admin\NewsController::class, 'create'])->name('admin.news.create');
        Route::post('/', [\App\Http\Controllers\Admin\NewsController::class, 'store'])->name('admin.news.store');
        Route::get('/{news}/edit', [\App\Http\Controllers\Admin\NewsController::class, 'edit'])->name('admin.news.edit');
        Route::patch('/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'update'])->name('admin.news.update');
        Route::delete('/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'destroy'])->name('admin.news.delete');
    });

    // Роуты поддержки
    Route::get('/support', [\App\Http\Controllers\Admin\SupportController::class, 'index'])->name('admin.support.index');
    Route::get('/support/{ticket}', [\App\Http\Controllers\Admin\SupportController::class, 'show'])->name('admin.support.show');
    Route::patch('/support/{ticket}/status', [\App\Http\Controllers\Admin\SupportController::class, 'updateStatus'])->name('support.status');
    Route::post('/support/{ticket}/reply', [\App\Http\Controllers\Admin\SupportController::class, 'reply'])->name('admin.support.reply');
    Route::get('/support/{ticket}/download', [\App\Http\Controllers\Admin\SupportController::class, 'download'])->name('admin.support.download');
    Route::delete('/support/{ticket}', [\App\Http\Controllers\Admin\SupportController::class, 'destroy'])->name('admin.support.destroy');

    Route::group(['namespace' => 'Subscription'], function () {
        Route::get('/subscriptions', [App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
        Route::get('/companies/{id}/info', [App\Http\Controllers\Admin\SubscriptionController::class, 'companyInfo'])->name('admin.companies.info');
        Route::post('/subscriptions/{subscriptionId}/add-users', [App\Http\Controllers\Admin\SubscriptionController::class, 'addUsers'])->name('admin.subscriptions.add-users');
        Route::post('/subscriptions/{subscriptionId}/cancel', [App\Http\Controllers\Admin\SubscriptionController::class, 'cancel'])->name('admin.subscriptions.cancel');
        Route::delete('/subscriptions/{subscriptionId}', [App\Http\Controllers\Admin\SubscriptionController::class, 'destroy'])->name('admin.subscriptions.destroy');
    });

    Route::get('/users/tracking', [App\Http\Controllers\Admin\UserTrackingController::class, 'index'])
        ->name('admin.users.tracking');
    Route::get('/users/map', [App\Http\Controllers\Admin\UserTrackingController::class, 'map'])
        ->name('admin.users.map');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UserTrackingController::class, 'show'])
        ->name('admin.users.show');

    Route::delete('/sessions/{session}', [App\Http\Controllers\Admin\UserTrackingController::class, 'deleteSession'])
        ->name('admin.sessions.delete');
    Route::delete('/users/{user}/clear-sessions', [App\Http\Controllers\Admin\UserTrackingController::class, 'clearSessions'])
        ->name('admin.users.clear-sessions');

    Route::middleware(['auth'])->group(function () {
        Route::get('/online-users', [App\Http\Controllers\Admin\UserTrackingController::class, 'getOnlineUsers'])
            ->name('online.users');
    });

    Route::post('/update-user-location', [UserLocationController::class, 'update'])->middleware('auth');
});

require __DIR__.'/auth.php';
