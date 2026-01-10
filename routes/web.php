<?php

use App\Http\Controllers\Frontend\TeamController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/chat', [\App\Http\Controllers\Frontend\ChatController::class, 'index'])->name('chat');
    Route::get('/file-manager', [\App\Http\Controllers\Frontend\FileManagerController::class, 'index'])->name('fileManager');
    Route::get('/mail', [\App\Http\Controllers\Frontend\MailController::class, 'index'])->name('mail.index');
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



Route::group(['prefix' => 'departments', 'middleware' => ['auth', 'verified', 'isManager']], function () {
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
