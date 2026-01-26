<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SmtpSetting;
use App\Models\Department;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SmtpSettingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $query = SmtpSetting::where('company_id', $company->id)
            ->with('department');

        // Фильтрация по отделу
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $settings = $query->orderBy('is_default', 'desc')
            ->orderBy('is_active', 'desc')
            ->paginate(20);

        $departments = $company->departments()->get();

        return view('smtp-settings.index', compact('settings', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'encryption' => 'nullable|in:ssl,tls,starttls',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'from_address' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $user = Auth::user();
        $company = $user->company;

        // Проверяем, что отдел принадлежит компании
        $department = Department::findOrFail($validated['department_id']);
        if ($department->company_id !== $company->id) {
            return back()->with('error', 'Отдел не принадлежит вашей компании');
        }

        // Если это настройка по умолчанию, снимаем флаг с других
        if ($request->boolean('is_default')) {
            SmtpSetting::where('company_id', $company->id)
                ->where('department_id', $department->id)
                ->update(['is_default' => false]);
        }

        $setting = SmtpSetting::create([
            'company_id' => $company->id,
            'department_id' => $department->id,
            'host' => $validated['host'],
            'port' => $validated['port'],
            'encryption' => $validated['encryption'],
            'username' => $validated['username'],
            'password' => $validated['password'],
            'from_address' => $validated['from_address'],
            'from_name' => $validated['from_name'],
            'is_active' => $request->boolean('is_active'),
            'is_default' => $request->boolean('is_default'),
        ]);

        return back()->with('success', 'Настройки SMTP успешно сохранены');
    }

    public function update(Request $request, SmtpSetting $smtpSetting)
    {
        $this->authorize('update', $smtpSetting);

        $validated = $request->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'encryption' => 'nullable|in:ssl,tls,starttls',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string', // пароль можно не менять
            'from_address' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // Если это настройка по умолчанию, снимаем флаг с других
        if ($request->boolean('is_default')) {
            SmtpSetting::where('company_id', $smtpSetting->company_id)
                ->where('department_id', $smtpSetting->department_id)
                ->where('id', '!=', $smtpSetting->id)
                ->update(['is_default' => false]);
        }

        $updateData = [
            'host' => $validated['host'],
            'port' => $validated['port'],
            'encryption' => $validated['encryption'],
            'username' => $validated['username'],
            'from_address' => $validated['from_address'],
            'from_name' => $validated['from_name'],
            'is_active' => $request->boolean('is_active'),
            'is_default' => $request->boolean('is_default'),
        ];

        // Обновляем пароль только если он указан
        if (!empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        $smtpSetting->update($updateData);

        return back()->with('success', 'Настройки SMTP успешно обновлены');
    }

    public function destroy(SmtpSetting $smtpSetting)
    {
        $this->authorize('delete', $smtpSetting);

        // Нельзя удалить единственную настройку
        $count = SmtpSetting::where('department_id', $smtpSetting->department_id)->count();
        if ($count <= 1) {
            return back()->with('error', 'Нельзя удалить единственную SMTP настройку отдела');
        }

        // Если удаляем настройку по умолчанию, назначаем другую
        if ($smtpSetting->is_default) {
            $newDefault = SmtpSetting::where('department_id', $smtpSetting->department_id)
                ->where('id', '!=', $smtpSetting->id)
                ->first();

            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $smtpSetting->delete();

        return back()->with('success', 'Настройка SMTP удалена');
    }

    public function test(SmtpSetting $smtpSetting)
    {
        $this->authorize('test', $smtpSetting);

        $user = Auth::user();

        try {
            // Настраиваем временную конфигурацию SMTP
            config([
                'mail.mailers.smtp_test' => [
                    'transport' => 'smtp',
                    'host' => $smtpSetting->host,
                    'port' => $smtpSetting->port,
                    'encryption' => $smtpSetting->encryption,
                    'username' => $smtpSetting->username,
                    'password' => $smtpSetting->password,
                    'timeout' => 30,
                ],
            ]);

            Mail::mailer('smtp_test')->to($user->email)->send(
                new \App\Mail\SmtpTestMail($smtpSetting)
            );

            // Обновляем метаданные с результатом теста
            $smtpSetting->update([
                'meta' => array_merge($smtpSetting->meta ?? [], [
                    'last_test_at' => now()->toISOString(),
                    'last_test_result' => 'success',
                    'last_error' => null,
                ]),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Тестовое письмо отправлено успешно',
            ]);

        } catch (\Exception $e) {
            \Log::error('SMTP тест не удался: ' . $e->getMessage());

            $smtpSetting->update([
                'meta' => array_merge($smtpSetting->meta ?? [], [
                    'last_test_at' => now()->toISOString(),
                    'last_test_result' => 'failed',
                    'last_error' => $e->getMessage(),
                ]),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка отправки: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleActive(SmtpSetting $smtpSetting)
    {
        $this->authorize('update', $smtpSetting);

        $smtpSetting->update([
            'is_active' => !$smtpSetting->is_active,
        ]);

        return back()->with('success', 'Статус обновлен');
    }

    public function setDefault(SmtpSetting $smtpSetting)
    {
        $this->authorize('update', $smtpSetting);

        // Снимаем флаг со всех настроек отдела
        SmtpSetting::where('department_id', $smtpSetting->department_id)
            ->update(['is_default' => false]);

        // Устанавливаем флаг для выбранной настройки
        $smtpSetting->update(['is_default' => true]);

        return back()->with('success', 'Настройка установлена по умолчанию');
    }

    public function getByDepartment(Department $department)
    {
        $this->authorize('view', $department);

        $settings = SmtpSetting::where('department_id', $department->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($setting) {
                return [
                    'id' => $setting->id,
                    'name' => $setting->from_name . ' <' . $setting->from_address . '>',
                    'is_default' => $setting->is_default,
                ];
            });

        return response()->json($settings);
    }
}
