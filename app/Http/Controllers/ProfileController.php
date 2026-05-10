<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Category;
use App\Models\Department;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {

        $user = Auth::user();

        // Статистика
        $stats = [
            'total' => Task::where('company_id', $user->company_id)->count(),
            'assigned' => Task::where('company_id', $user->company_id)
                ->where('status', Task::STATUS_ASSIGNED)->count(),
            'not_assigned' => Task::where('company_id', $user->company_id)
                ->where('status', Task::STATUS_NOT_ASSIGNED)->count(),
            'in_progress' => Task::where('company_id', $user->company_id)
                ->where('status', Task::STATUS_IN_PROGRESS)->count(),
            'review' => Task::where('company_id', $user->company_id)
                ->where('status', Task::STATUS_REVIEW)->count(),
            'overdue' => Task::where('company_id', $user->company_id)
                ->where('status', Task::STATUS_OVERDUE)->count(),
            'completed' => Task::where('company_id', $user->company_id)
                ->where('status', Task::STATUS_COMPLETED)->count(),
        ];

        // Данные для фильтров
        $filterData = [
            'users' => User::where('company_id', $user->company_id)->get(),
            'departments' => Department::where('company_id', $user->company_id)->get(),
            'categories' => Category::whereHas('tasks', function($query) use ($user) {
                $query->where('company_id', $user->company_id);
            })->get(),
            'statuses' => Task::getStatuses(),
            'priorities' => ['низкий', 'средний', 'высокий', 'критический'],
        ];

        return view('profile.edit', [
            'user' => $request->user(),
            'stats' => $stats,
            'filterData' => $filterData,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // 2MB max
        ]);

        $user = $request->user();

        // Удаляем старый аватар, если он существует
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Сохраняем новый аватар
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return Redirect::route('profile.edit')->with('avatar-status', 'Аватар успешно обновлен!');
    }

    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();

            return Redirect::route('profile.edit')->with('avatar-deleted', 'Аватар успешно удален!');
        }

        return Redirect::route('profile.edit')->with('error', 'Аватар не найден');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
