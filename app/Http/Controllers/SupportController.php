<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Mail\SupportTicketMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    // Ограничиваем максимальный размер файла (10MB)
    const MAX_FILE_SIZE = 10240; // KB

    // Разрешённые MIME-типы
    const ALLOWED_MIMES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'application/pdf',
        'application/zip',
        'application/x-zip-compressed',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'application/json'
    ];

    // Разрешённые расширения
    const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif',
        'pdf', 'zip', 'rar', '7z',
        'doc', 'docx', 'txt', 'json'
    ];

    public function send(Request $request)
    {
        // Валидация входных данных
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[а-яА-Яa-zA-Z\s\-]+$/u',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255|min:3',
            'message' => 'required|string|min:10|max:5000',
            'attachment' => 'nullable|file|max:' . self::MAX_FILE_SIZE
        ], [
            'name.regex' => 'Имя может содержать только буквы, пробелы и дефисы',
            'name.required' => 'Пожалуйста, укажите ваше имя',
            'email.required' => 'Пожалуйста, укажите email для обратной связи',
            'email.email' => 'Введите корректный email адрес',
            'subject.min' => 'Тема сообщения должна содержать минимум 3 символа',
            'message.min' => 'Сообщение должно содержать минимум 10 символов',
            'attachment.max' => 'Файл не должен превышать 10MB',
        ]);

        // Обработка файла
        $attachmentPath = null;
        $attachmentOriginalName = null;
        $attachmentSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            // Дополнительная проверка MIME-типа
            $mimeType = $file->getMimeType();
            $extension = strtolower($file->getClientOriginalExtension());

            if (!in_array($mimeType, self::ALLOWED_MIMES) && !in_array($extension, self::ALLOWED_EXTENSIONS)) {
                return back()->withErrors(['attachment' => 'Недопустимый тип файла. Разрешены: ' . implode(', ', self::ALLOWED_EXTENSIONS)])->withInput();
            }

            // Проверка на вредоносный контент (базовая)
            if ($this->isPotentiallyDangerous($file)) {
                return back()->withErrors(['attachment' => 'Файл содержит потенциально опасный контент'])->withInput();
            }

            // Генерируем безопасное имя файла
            $safeName = Str::random(40) . '.' . $extension;

            // Сохраняем файл в защищённую директорию
            $attachmentPath = $file->storeAs('support_attachments', $safeName, 'local');

            if (!$attachmentPath) {
                return back()->withErrors(['attachment' => 'Ошибка при загрузке файла'])->withInput();
            }

            $attachmentOriginalName = $file->getClientOriginalName();
            $attachmentSize = $this->formatBytes($file->getSize());
        }

        // Сохраняем в базу данных
        $ticket = SupportTicket::create([
            'name' => strip_tags($validated['name']),
            'email' => strip_tags($validated['email']),
            'subject' => strip_tags($validated['subject']),
            'message' => strip_tags($validated['message']),
            'attachment_path' => $attachmentPath,
            'attachment_original_name' => $attachmentOriginalName,
            'attachment_size' => $attachmentSize,
            'status' => 'new',
            'user_ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Отправляем email-уведомление (администратору)
        try {
            $adminEmail = config('mail.support_email', env('MAIL_FROM_ADDRESS'));

            $ticketData = $validated;
            $ticketData['attachment_original_name'] = $attachmentOriginalName;
            $ticketData['attachment_size'] = $attachmentSize;
            $ticketData['user_ip'] = $request->ip();
            $ticketData['ticket_id'] = $ticket->id;

            Mail::to($adminEmail)->send(new SupportTicketMail($ticketData, $attachmentPath));

            // Опционально: отправить подтверждение пользователю
            // Mail::to($validated['email'])->send(new TicketConfirmationMail($ticketData));

        } catch (\Exception $e) {
            // Логируем ошибку, но не показываем пользователю
            \Log::error('Ошибка отправки email: ' . $e->getMessage());
        }

        // Очищаем временные файлы (если нужно)
        // session()->forget('support_form');

        return redirect()->back()->with('success', 'Ваше сообщение успешно отправлено! Мы ответим вам в ближайшее время.');
    }

    /**
     * Базовая проверка файла на опасный контент
     */
    private function isPotentiallyDangerous($file)
    {
        $dangerousPatterns = [
            '<?php', '<script', 'eval(', 'base64_decode',
            'system(', 'exec(', 'shell_exec', 'passthru',
            'phpinfo', 'require_once', 'include_once'
        ];

        // Для текстовых файлов проверяем содержимое
        if (in_array($file->getMimeType(), ['text/plain', 'application/json'])) {
            $content = file_get_contents($file->getRealPath());
            foreach ($dangerousPatterns as $pattern) {
                if (stripos($content, $pattern) !== false) {
                    return true;
                }
            }
        }

        // Проверка двойных расширений
        $filename = $file->getClientOriginalName();
        if (preg_match('/\.(php|phtml|html?|js|exe|bat|sh|cmd)$/i', $filename)) {
            return true;
        }

        return false;
    }

    /**
     * Форматирует размер файла в читаемый вид
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
