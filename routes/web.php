<?php

use App\Http\Controllers\Frontend\ChatController;
use App\Http\Controllers\Frontend\EmailTrashController;
use App\Http\Controllers\Frontend\FileStorageController;
use App\Http\Controllers\Frontend\DepartmentEmailController;
use App\Http\Controllers\Frontend\EmailTemplateController;
use App\Http\Controllers\Frontend\PersonalEmailController;
use App\Http\Controllers\Frontend\TeamController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Frontend\SmtpSettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::get('/check-overdue-tasks', function() {
    Artisan::call('tasks:check-overdue');
    return response()->json(['message' => 'Checked overdue tasks']);
});

Route::get('/', [\App\Http\Controllers\Frontend\HomeController::class, 'home'])->name('index');
// Домашняя страница
Route::middleware(['auth', 'checkUserRole', 'verified', 'trackUserActivity'])->group(function () {
    Route::get('/home', [\App\Http\Controllers\Frontend\HomeController::class, 'index'])->name('welcome');

    // Админская страница для руководителей и менеджеров
    Route::get('/admin/tasks', [\App\Http\Controllers\Frontend\HomeController::class, 'indexAdmin'])->name('tasks.admin');

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

Route::middleware(['auth', 'verified', 'trackUserActivity'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::prefix('chat/api')->name('chat.api.')->group(function () {
        // GET запросы
        Route::get('/chats', [ChatController::class, 'getChats'])->name('chats');
        Route::get('/colleagues', [ChatController::class, 'getColleagues'])->name('colleagues');
        Route::get('/chats/{chat}/messages', [ChatController::class, 'getMessages'])->name('messages');

        // POST запросы
        Route::post('/private-chat', [ChatController::class, 'startPrivateChat'])->name('private-chat');
        Route::post('/group-chat', [ChatController::class, 'createGroupChat'])->name('group-chat');
        Route::post('/chats/{chat}/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/chats/{chat}/upload', [ChatController::class, 'uploadFile'])->name('upload');
        Route::post('/chats/{chat}/add-users', [ChatController::class, 'addUsers'])->name('add-users');
        Route::post('/chats/{chat}/remove-user', [ChatController::class, 'removeUser'])->name('remove-user');
        Route::post('/chats/{chat}/mark-read', [ChatController::class, 'markAsRead'])->name('mark-read');

        // DELETE запросы
        Route::delete('/chats/{chat}', [ChatController::class, 'deleteChat'])->name('delete');
    });



    // ЛИЧНАЯ ПОЧТА ПОЛЬЗОВАТЕЛЯ
// ============================================
    Route::prefix('personal/emails')->name('personal.emails.')->middleware('auth')->group(function () {
        // Главная страница личной почты
        Route::get('/', [PersonalEmailController::class, 'index'])->name('index');

        // Поиск писем
        Route::get('/search', [PersonalEmailController::class, 'search'])->name('search');

        // Создание письма
        Route::get('/create', [PersonalEmailController::class, 'create'])->name('create');
        Route::post('/', [PersonalEmailController::class, 'store'])->name('store');

        // Просмотр письма
        Route::get('/{email}', [PersonalEmailController::class, 'show'])->name('show');

        // Ответ на письмо
        Route::get('/{email}/reply', [PersonalEmailController::class, 'replyForm'])->name('reply.form');
        Route::post('/{email}/reply', [PersonalEmailController::class, 'reply'])->name('reply');

        // Пересылка письма
        Route::get('/{email}/forward', [PersonalEmailController::class, 'forwardForm'])->name('forward.form');
        Route::post('/{email}/forward', [PersonalEmailController::class, 'forward'])->name('forward');

        // Архивация/разархивация
        Route::post('/{email}/toggle-archive', [PersonalEmailController::class, 'toggleArchive'])->name('toggle-archive');

        // Работа с метками
        Route::post('/{email}/add-tag', [PersonalEmailController::class, 'addTag'])->name('add-tag');
        Route::post('/{email}/remove-tag', [PersonalEmailController::class, 'removeTag'])->name('remove-tag');

        // Удаление в корзину
        Route::delete('/{email}', [PersonalEmailController::class, 'destroy'])->name('destroy');

        // Массовые действия
        Route::post('/bulk', [PersonalEmailController::class, 'bulkAction'])->name('bulk');

        // Импорт/экспорт
        Route::get('/export', [PersonalEmailController::class, 'export'])->name('export');
        Route::post('/import', [PersonalEmailController::class, 'import'])->name('import');

        // Пометить как прочитанное/непрочитанное
        Route::post('/{email}/mark-read', [PersonalEmailController::class, 'markAsRead'])->name('mark.read');
        Route::post('/{email}/mark-unread', [PersonalEmailController::class, 'markAsUnread'])->name('mark.unread');

        // Пометить как важное/неважное
        Route::post('/{email}/mark-important', [PersonalEmailController::class, 'markAsImportant'])->name('mark.important');
        Route::post('/{email}/mark-unimportant', [PersonalEmailController::class, 'markAsUnimportant'])->name('mark.unimportant');
    });

// Корзина личной почты
    Route::prefix('personal/emails/trash')->name('personal.emails.trash.')->middleware('auth')->group(function () {
        Route::get('/', [PersonalEmailController::class, 'trashIndex'])->name('index');
        Route::post('/restore/{email}', [PersonalEmailController::class, 'restore'])->name('restore');
        Route::delete('/force/{email}', [PersonalEmailController::class, 'forceDestroy'])->name('force');
        Route::post('/clear', [PersonalEmailController::class, 'clearTrash'])->name('clear');
        Route::post('/restore-all', [PersonalEmailController::class, 'restoreAll'])->name('restore-all');
        Route::delete('/empty', [PersonalEmailController::class, 'emptyTrash'])->name('empty');
    });

// ============================================
// ПОЧТА ОТДЕЛА
// ============================================
    Route::prefix('departments/{department}/emails')->name('departments.emails.')->group(function () {
        // Главная страница почты отдела
        Route::get('/', [DepartmentEmailController::class, 'index'])->name('index');

        // Поиск писем отдела
        Route::get('/search', [DepartmentEmailController::class, 'search'])->name('search');

        // Создание письма от имени отдела
        Route::get('/create', [DepartmentEmailController::class, 'create'])->name('create');
        Route::post('/', [DepartmentEmailController::class, 'store'])->name('store');

        // Просмотр письма отдела
        Route::get('/{email}', [DepartmentEmailController::class, 'show'])->name('show');

        // Ответ на письмо от имени отдела
        Route::get('/{email}/reply', [DepartmentEmailController::class, 'replyForm'])->name('reply.form');
        Route::post('/{email}/reply', [DepartmentEmailController::class, 'reply'])->name('reply');

        // Пересылка письма отдела
        Route::get('/{email}/forward', [DepartmentEmailController::class, 'forwardForm'])->name('forward.form');
        Route::post('/{email}/forward', [DepartmentEmailController::class, 'forward'])->name('forward');

        // Архивация/разархивация письма отдела
        Route::post('/{email}/toggle-archive', [DepartmentEmailController::class, 'toggleArchive'])->name('toggle-archive');

        // Работа с метками отдела
        Route::post('/{email}/add-tag', [DepartmentEmailController::class, 'addTag'])->name('add-tag');
        Route::post('/{email}/remove-tag', [DepartmentEmailController::class, 'removeTag'])->name('remove-tag');

        // Удаление письма отдела в корзину
        Route::delete('/{email}', [EmailTrashController::class, 'destroy'])->name('destroy');

        // Массовые действия в отделе
        Route::post('/bulk', [DepartmentEmailController::class, 'bulkAction'])->name('bulk');

        // Импорт/экспорт писем отдела
        Route::get('/export', [DepartmentEmailController::class, 'export'])->name('export');
        Route::post('/import', [DepartmentEmailController::class, 'import'])->name('import');

        // Пометить как прочитанное/непрочитанное в отделе
        Route::post('/{email}/mark-read', [DepartmentEmailController::class, 'markAsRead'])->name('mark.read');
        Route::post('/{email}/mark-unread', [DepartmentEmailController::class, 'markAsUnread'])->name('mark.unread');

        // Пометить как важное/неважное в отделе
        Route::post('/{email}/mark-important', [DepartmentEmailController::class, 'markAsImportant'])->name('mark.important');
        Route::post('/{email}/mark-unimportant', [DepartmentEmailController::class, 'markAsUnimportant'])->name('mark.unimportant');

        // Статистика почты отдела
        Route::get('/stats', [DepartmentEmailController::class, 'stats'])->name('stats');

        // Автозаполнение контактов отдела
        Route::get('/contacts', [DepartmentEmailController::class, 'contacts'])->name('contacts');
    });

// Корзина почты отдела
    Route::prefix('departments/{department}/emails/trash')->name('departments.emails.trash.')->group(function () {
        Route::get('/', [EmailTrashController::class, 'index'])->name('index');
        Route::post('/restore/{email}', [EmailTrashController::class, 'restore'])->name('restore');
        Route::delete('/force/{email}', [EmailTrashController::class, 'forceDestroy'])->name('force');
        Route::post('/clear', [EmailTrashController::class, 'clear'])->name('clear');
        Route::post('/restore-all', [EmailTrashController::class, 'restoreAll'])->name('restore-all');
        Route::delete('/empty', [EmailTrashController::class, 'empty'])->name('empty');
    });

// ============================================
// ОБЩИЕ МАРШРУТЫ ДЛЯ ПОЧТЫ
// ============================================

// Шаблоны писем (доступны и для личной почты, и для почты отдела)
    Route::prefix('email-templates')->name('email-templates.')->middleware('auth')->group(function () {
        Route::get('/', [EmailTemplateController::class, 'index'])->name('index');
        Route::get('/create', [EmailTemplateController::class, 'create'])->name('create');
        Route::post('/', [EmailTemplateController::class, 'store'])->name('store');
        Route::get('/{template}/edit', [EmailTemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [EmailTemplateController::class, 'update'])->name('update');
        Route::delete('/{template}', [EmailTemplateController::class, 'destroy'])->name('destroy');
        Route::get('/{template}/preview', [EmailTemplateController::class, 'preview'])->name('preview');
        Route::post('/{template}/duplicate', [EmailTemplateController::class, 'duplicate'])->name('duplicate');

        // Глобальные шаблоны компании
        Route::get('/company/global', [EmailTemplateController::class, 'companyGlobal'])->name('company.global');

        // Шаблоны отдела
        Route::get('/department/{department}', [EmailTemplateController::class, 'departmentTemplates'])->name('department');
    });

// Настройки SMTP (разные для пользователя и отдела)
    Route::prefix('smtp-settings')->name('smtp-settings.')->middleware('auth')->group(function () {
        // Личные настройки SMTP пользователя
        Route::prefix('personal')->name('personal.')->group(function () {
            Route::get('/', [SmtpSettingController::class, 'personalIndex'])->name('index');
            Route::post('/', [SmtpSettingController::class, 'personalStore'])->name('store');
            Route::put('/{setting}', [SmtpSettingController::class, 'personalUpdate'])->name('update');
            Route::delete('/{setting}', [SmtpSettingController::class, 'personalDestroy'])->name('destroy');
            Route::post('/{setting}/test', [SmtpSettingController::class, 'personalTest'])->name('test');
            Route::post('/{setting}/set-default', [SmtpSettingController::class, 'personalSetDefault'])->name('set-default');
        });

        // Настройки SMTP отдела (только для руководителей)
        Route::prefix('department/{department}')->name('department.')->group(function () {
            Route::get('/', [SmtpSettingController::class, 'departmentIndex'])->name('index');
            Route::post('/', [SmtpSettingController::class, 'departmentStore'])->name('store');
            Route::put('/{setting}', [SmtpSettingController::class, 'departmentUpdate'])->name('update');
            Route::delete('/{setting}', [SmtpSettingController::class, 'departmentDestroy'])->name('destroy');
            Route::post('/{setting}/test', [SmtpSettingController::class, 'departmentTest'])->name('test');
            Route::post('/{setting}/set-default', [SmtpSettingController::class, 'departmentSetDefault'])->name('set-default');
        });

        // Настройки SMTP компании (для администраторов)
        Route::prefix('company/{company}')->name('company.')->group(function () {
            Route::get('/', [SmtpSettingController::class, 'companyIndex'])->name('index');
            Route::post('/', [SmtpSettingController::class, 'companyStore'])->name('store');
            Route::put('/{setting}', [SmtpSettingController::class, 'companyUpdate'])->name('update');
            Route::delete('/{setting}', [SmtpSettingController::class, 'companyDestroy'])->name('destroy');
            Route::post('/{setting}/test', [SmtpSettingController::class, 'companyTest'])->name('test');
            Route::post('/{setting}/set-default', [SmtpSettingController::class, 'companySetDefault'])->name('set-default');
        });
    });


    Route::prefix('files')->group(function () {
        Route::get('/', [FileStorageController::class, 'index'])->name('files.index');
        Route::post('/upload', [FileStorageController::class, 'upload'])->name('files.upload');
        Route::get('/download/{file}', [FileStorageController::class, 'download'])->name('files.download');
        Route::get('/view/{file}', [FileStorageController::class, 'view'])->name('files.view');
        Route::delete('/delete/{file}', [FileStorageController::class, 'destroy'])->name('files.destroy');
        Route::get('/statistics', [FileStorageController::class, 'getStatistics'])->name('files.statistics');
    });
});

Route::get('/create-company', [\App\Http\Controllers\Frontend\HomeController::class, 'noCompanies'])->middleware(['auth', 'verified', 'trackUserActivity'])->name('no.companies');

// Публичные маршруты для приглашений (доступны без авторизации)
Route::get('/invitation/{token}', [InvitationController::class, 'showInvitationForm'])->name('invitation.accept');
Route::post('/invitation/{token}/accept', [InvitationController::class, 'acceptInvitation'])->name('invitation.process');

// ВСЕ МАРШРУТЫ ДЛЯ ЗАДАЧ ПЕРЕНЕСЕМ В TaskController
Route::group(['prefix' => 'tasks', 'middleware' => ['auth', 'verified']], function () {
    // Основные CRUD маршруты
    Route::get('/', [App\Http\Controllers\Frontend\TaskController::class, 'index'])->name('tasks.index');
    Route::get('/create', [App\Http\Controllers\Frontend\TaskController::class, 'create'])->name('tasks.create');
    Route::post('/store', [App\Http\Controllers\Frontend\TaskController::class, 'store'])->name('tasks.store');

    // Задачи себе
    Route::post('/personal/store', [App\Http\Controllers\Frontend\TaskController::class, 'storePersonal'])->name('tasks.personal.store');

    // Маршрут для просмотра задачи
    Route::get('/{task}/view', [App\Http\Controllers\Frontend\TaskController::class, 'view'])->name('tasks.view');

    // Маршруты для редактирования задачи (ОСТАВЛЯЕМ ТОЛЬКО ЭТИ)
    Route::get('/{task}/get', [App\Http\Controllers\Frontend\TaskController::class, 'getTask'])->name('tasks.get');
    Route::post('/{task}/update', [App\Http\Controllers\Frontend\TaskController::class, 'update'])->name('tasks.update');

    // МАРШРУТЫ ДЛЯ УПРАВЛЕНИЯ ЗАДАЧАМИ СОТРУДНИКАМИ
    Route::post('/{task}/take', [App\Http\Controllers\Frontend\TaskController::class, 'takeTask'])->name('tasks.take');
    Route::patch('/{task}/status', [App\Http\Controllers\Frontend\TaskController::class, 'updateTaskStatus'])->name('tasks.status');
    Route::post('/{task}/reject', [App\Http\Controllers\Frontend\TaskController::class, 'rejectTask'])->name('tasks.reject');
    Route::post('/{task}/attach-file', [App\Http\Controllers\Frontend\TaskController::class, 'attachFile'])->name('tasks.attach-file');


    // МАРШРУТЫ ДЛЯ АДМИНИСТРАТОРОВ
    Route::post('/{task}/return-to-work', [App\Http\Controllers\Frontend\TaskController::class, 'returnToWork'])->name('admin.tasks.return-to-work');
    Route::delete('/{task}/delete', [App\Http\Controllers\Frontend\TaskController::class, 'destroy'])->name('admin.tasks.delete');
    Route::post('/{task}/add-files', [App\Http\Controllers\Frontend\TaskController::class, 'addFiles'])->name('tasks.add-files');
    Route::get('/file-storage/get-files', [App\Http\Controllers\Frontend\TaskController::class, 'getFiles'])
        ->name('file-storage.get-files');

});

// Маршрут для удаления файлов
Route::delete('/files/{file}', [App\Http\Controllers\Frontend\TaskController::class, 'deleteFile'])->name('files.delete');

// Остальные маршруты остаются без изменений
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/photobank', [\App\Http\Controllers\Frontend\PhotobankController::class, 'index'])->name('photobank');
    Route::post('/photobank/categories', [\App\Http\Controllers\Frontend\PhotobankController::class, 'createCategory'])->name('photobank.categories.store');
    Route::post('/photobank/tags', [\App\Http\Controllers\Frontend\PhotobankController::class, 'createTag'])->name('photobank.tags.store');
    Route::post('/photobank/photos', [\App\Http\Controllers\Frontend\PhotobankController::class, 'storePhoto'])->name('photobank.photos.store');
    Route::get('/photobank/categories', [\App\Http\Controllers\Frontend\PhotobankController::class, 'getCategories'])->name('photobank.categories.index');
    Route::get('/photobank/tags', [\App\Http\Controllers\Frontend\PhotobankController::class, 'getTags'])->name('photobank.tags.index');

    Route::post('/category/create', [App\Http\Controllers\Frontend\CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{id}/edit', [App\Http\Controllers\Frontend\CategoryController::class, 'edit'])->name('category.edit');
    Route::patch('/category/update', [App\Http\Controllers\Frontend\CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/delete', [App\Http\Controllers\Frontend\CategoryController::class, 'destroy'])->name('category.destroy');
});



Route::group(['prefix' => 'departments', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', [App\Http\Controllers\Frontend\DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/store', [App\Http\Controllers\Frontend\DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/{id}/edit', [App\Http\Controllers\Frontend\DepartmentController::class, 'edit'])->name('departments.edit');
    Route::patch('/update', [App\Http\Controllers\Frontend\DepartmentController::class, 'update'])->name('departments.update');
});

// Маршруты для команды с системой приглашений
Route::middleware(['auth', 'verified', 'isLeader'])->group(function () {
    Route::prefix('team')->name('team.')->group(function () {
        // Основные маршруты команды
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::delete('/{user}', [TeamController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-destroy', [TeamController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::get('/export', [TeamController::class, 'exportTable'])->name('export-table');
        Route::get('/print', [TeamController::class, 'printTable'])->name('print-table');

        // Маршруты для работы с пользователями
        Route::get('/user/{user}', [TeamController::class, 'getUserDetails'])->name('user.details');
        Route::get('/user/{user}/print', [TeamController::class, 'printUserDetails'])->name('user.print');
        Route::get('/user/{user}/tasks', [TeamController::class, 'getUserTasks'])->name('user.tasks');
        Route::get('/user/{user}/export', [TeamController::class, 'exportUserStats'])->name('user.export');

        // Маршруты для системы приглашений
        Route::post('/invite', [InvitationController::class, 'invite'])->name('invite');
        Route::get('/invitations', [InvitationController::class, 'getInvitations'])->name('invitations');
        Route::delete('/invitations/{id}', [InvitationController::class, 'cancelInvitation'])->name('invitations.cancel');
        Route::get('/invitations/search', [InvitationController::class, 'searchUsers'])->name('invitations.search');
    });
});

Route::group(['prefix' => 'companies', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', [App\Http\Controllers\Frontend\CompanyController::class, 'index'])->name('companies.index');
    Route::get('/create', [App\Http\Controllers\Frontend\CompanyController::class, 'create'])->name('companies.create');
    Route::post('/store', [App\Http\Controllers\Frontend\CompanyController::class, 'store'])->name('companies.store');
});

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', [App\Http\Controllers\Frontend\UserController::class, 'index'])->name('users.index');
    Route::get('/create', [App\Http\Controllers\Frontend\UserController::class, 'create'])->name('users.create');
    Route::post('/store', [App\Http\Controllers\Frontend\UserController::class, 'store'])->name('users.store');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
