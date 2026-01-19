<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Department;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $query = EmailTemplate::where('company_id', $company->id)
            ->with(['department', 'creator']);

        // Фильтрация по отделу
        if ($request->has('department_id')) {
            $query->where(function($q) use ($request) {
                $q->where('department_id', $request->department_id)
                    ->orWhere('is_global', true);
            });
        } else {
            // Если пользователь привязан к отделу, показываем глобальные + его отдела
            if ($user->department_id) {
                $query->where(function($q) use ($user) {
                    $q->where('department_id', $user->department_id)
                        ->orWhere('is_global', true);
                });
            }
        }

        // Поиск
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $templates = $query->orderBy('is_global', 'desc')
            ->orderBy('name')
            ->paginate(20);

        $departments = $company->departments()->get();

        return view('email-templates.index', compact('templates', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'is_global' => 'boolean',
            'variables' => 'nullable|array',
        ]);

        $user = Auth::user();
        $company = $user->company;

        // Проверка прав на создание глобального шаблона
        if ($request->boolean('is_global') && !$user->hasPermission('create_global_templates')) {
            return back()->with('error', 'У вас нет прав на создание глобальных шаблонов');
        }

        $template = EmailTemplate::create([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'variables' => $validated['variables'] ?? [],
            'company_id' => $company->id,
            'department_id' => $validated['department_id'] ?? null,
            'created_by' => $user->id,
            'is_global' => $request->boolean('is_global'),
            'is_active' => true,
        ]);

        return back()->with('success', 'Шаблон успешно создан');
    }

    public function update(Request $request, EmailTemplate $template)
    {
        $this->authorize('update', $template);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
            'is_active' => 'boolean',
            'is_global' => 'boolean',
            'variables' => 'nullable|array',
        ]);

        // Проверка прав на изменение глобального шаблона
        if ($request->boolean('is_global') && !Auth::user()->hasPermission('edit_global_templates')) {
            return back()->with('error', 'У вас нет прав на изменение глобальных шаблонов');
        }

        $template->update([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'is_active' => $request->boolean('is_active'),
            'is_global' => $request->boolean('is_global'),
            'variables' => $validated['variables'] ?? $template->variables,
        ]);

        return back()->with('success', 'Шаблон успешно обновлен');
    }

    public function destroy(EmailTemplate $template)
    {
        $this->authorize('delete', $template);

        $template->delete();

        return back()->with('success', 'Шаблон удален');
    }

    public function duplicate(EmailTemplate $template)
    {
        $this->authorize('create', EmailTemplate::class);

        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (копия)';
        $newTemplate->created_by = Auth::id();
        $newTemplate->save();

        return back()->with('success', 'Шаблон скопирован');
    }

    public function preview(EmailTemplate $template)
    {
        $this->authorize('view', $template);

        $previewData = [];
        foreach ($template->variables ?? [] as $variable) {
            $previewData[$variable] = '[' . $variable . ']';
        }

        $parsed = $template->parse($previewData);

        return response()->json([
            'subject' => $parsed['subject'],
            'body' => $parsed['body'],
        ]);
    }

    public function applyTemplate(Request $request, Department $department)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
        ]);

        $template = EmailTemplate::findOrFail($request->template_id);

        // Проверка доступа к шаблону
        if ($template->company_id !== $department->company_id) {
            abort(403);
        }

        if (!$template->is_global && $template->department_id !== $department->id) {
            abort(403);
        }

        $variables = [];
        foreach ($template->variables ?? [] as $variable) {
            $variables[$variable] = $request->input("var_{$variable}", '');
        }

        $parsed = $template->parse($variables);

        return response()->json([
            'subject' => $parsed['subject'],
            'body' => $parsed['body'],
        ]);
    }
}
