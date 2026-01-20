<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTrashController extends Controller
{
    /**
     * Показать корзину удаленных писем
     */
    public function index(Request $request, Department $department)
    {
        $this->authorize('viewTrashed', Email::class);

        $emails = $department->emails()
            ->onlyTrashed()
            ->with(['sender', 'deletedBy', 'tags'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('frontend.mail.trash', compact('department', 'emails'));
    }

    /**
     * Мягкое удаление письма
     */
    public function destroy(Request $request, Department $department, Email $email)
    {
        $this->authorize('delete', $email);

        $validated = $request->validate([
            'delete_reason' => 'nullable|string|max:500',
        ]);

        // Выполняем мягкое удаление
        $email->softDelete(Auth::user(), $validated['delete_reason'] ?? null);

        return redirect()->route('departments.emails.index', $department)
            ->with('success', 'Письмо перемещено в корзину');
    }

    /**
     * Восстановление письма
     */
    public function restore(Request $request, Department $department, $emailId)
    {
        $email = Email::onlyTrashed()->findOrFail($emailId);

        $this->authorize('restore', $email);

        $email->restoreEmail();

        return redirect()->route('departments.emails.trash', $department)
            ->with('success', 'Письмо восстановлено');
    }

    /**
     * Полное удаление письма
     */
    public function forceDestroy(Request $request, Department $department, $emailId)
    {
        $email = Email::onlyTrashed()->findOrFail($emailId);

        $this->authorize('forceDelete', $email);

        $email->forceDeleteEmail();

        return redirect()->route('departments.emails.trash', $department)
            ->with('success', 'Письмо полностью удалено');
    }

    /**
     * Очистка корзины (удаление всех писем старше X дней)
     */
    public function clear(Request $request, Department $department)
    {
        $this->authorize('forceDelete', Email::class);

        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $deletedBefore = now()->subDays($validated['days']);

        $emails = $department->emails()
            ->onlyTrashed()
            ->where('deleted_at', '<=', $deletedBefore)
            ->get();

        $count = 0;
        foreach ($emails as $email) {
            $email->forceDeleteEmail();
            $count++;
        }

        return redirect()->route('departments.emails.trash', $department)
            ->with('success', "Удалено {$count} писем из корзины");
    }

    /**
     * Восстановление всех писем из корзины
     */
    public function restoreAll(Request $request, Department $department)
    {
        $this->authorize('restore', Email::class);

        $emails = $department->emails()
            ->onlyTrashed()
            ->get();

        $count = 0;
        foreach ($emails as $email) {
            if (Auth::user()->can('restore', $email)) {
                $email->restoreEmail();
                $count++;
            }
        }

        return redirect()->route('departments.emails.trash', $department)
            ->with('success', "Восстановлено {$count} писем");
    }
}
