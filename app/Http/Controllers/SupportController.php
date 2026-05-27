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
        // Валидация
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255|min:3',
            'message' => 'required|string|min:10|max:5000',
            'attachment' => 'nullable|file|max:10240'
        ]);

        // Обработка файла (как было раньше)
        $attachmentPath = null;
        $attachmentOriginalName = null;
        $attachmentSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $extension = strtolower($file->getClientOriginalExtension());
            $safeName = Str::random(40) . '.' . $extension;
            $attachmentPath = $file->storeAs('support_attachments', $safeName, 'local');
            $attachmentOriginalName = $file->getClientOriginalName();
            $attachmentSize = $this->formatBytes($file->getSize());
        }

        // Сохраняем в БД
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

        // Подготовка данных для письма
        $ticketData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'attachment_original_name' => $attachmentOriginalName,
            'attachment_size' => $attachmentSize,
            'user_ip' => $request->ip(),
            'ticket_id' => $ticket->id
        ];

        // ОТПРАВКА ПИСЬМА - ИСПРАВЛЕНО
        try {
            $adminEmail = config('mail.support_email', 'taskmanager@xn--d1ababe5abjwjn9m.xn--p1ai');

            // ВАЖНО: В to() указываем EMAIL, а не тему!
            Mail::to($adminEmail)->send(new SupportTicketMail($ticketData, $attachmentPath));

            \Log::info('Письмо отправлено на: ' . $adminEmail);

        } catch (\Exception $e) {
            \Log::error('Ошибка отправки email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Ваше сообщение успешно отправлено!');
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
