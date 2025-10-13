<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


// Домашняя страница
Route::get('/', [\App\Http\Controllers\Frontend\HomeController::class, 'index'])->middleware(['auth', 'verified', 'haveCompanies'])->name('home');
Route::get('/create-company', [\App\Http\Controllers\Frontend\HomeController::class, 'noCompanies'])->middleware(['auth', 'verified'])->name('no.companies');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/photobank', [\App\Http\Controllers\Frontend\PhotobankController::class, 'index'])->name('photobank');
    Route::post('/photobank/categories', [\App\Http\Controllers\Frontend\PhotobankController::class, 'createCategory'])->name('photobank.categories.store');
    Route::post('/photobank/tags', [\App\Http\Controllers\Frontend\PhotobankController::class, 'createTag'])->name('photobank.tags.store');
    Route::post('/photobank/photos', [\App\Http\Controllers\Frontend\PhotobankController::class, 'storePhoto'])->name('photobank.photos.store');
    Route::get('/photobank/categories', [\App\Http\Controllers\Frontend\PhotobankController::class, 'getCategories'])->name('photobank.categories.index');
    Route::get('/photobank/tags', [\App\Http\Controllers\Frontend\PhotobankController::class, 'getTags'])->name('photobank.tags.index');
});

Route::post('/category/create', [App\Http\Controllers\Frontend\CategoryController::class, 'store'])->name('category.store');

Route::group(['prefix' => 'departments', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', [App\Http\Controllers\Frontend\DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/store', [App\Http\Controllers\Frontend\DepartmentController::class, 'store'])->name('departments.store');
});


Route::get('/teams', [App\Http\Controllers\Frontend\TeamController::class, 'index'])->name('teams.index');

Route::group(['prefix' => 'companies', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', [App\Http\Controllers\Frontend\CompanyController::class, 'index'])->name('companies.index');
    Route::get('/create', [App\Http\Controllers\Frontend\CompanyController::class, 'create'])->name('companies.create');
    Route::post('/store', [App\Http\Controllers\Frontend\CompanyController::class, 'store'])->name('companies.store');
});

Route::group(['prefix' => 'tasks', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', [App\Http\Controllers\Frontend\TaskController::class, 'index'])->name('tasks.index');
    Route::get('/create', [App\Http\Controllers\Frontend\TaskController::class, 'create'])->name('tasks.create');
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
