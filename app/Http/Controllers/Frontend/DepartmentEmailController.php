<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Email;
use App\Models\File;
use App\Models\Tag;
use App\Models\EmailTemplate;
use App\Models\SmtpSetting;
use App\Services\EmailService;
use App\Notifications\NewEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmailsExport;

class DepartmentEmailController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index(Request $request, Department $department)
    {
//        // Проверка прав доступа
//        if (!auth()->user()->hasPermission('access_email')) {
//            abort(403, 'У вас нет доступа к почте отдела');
//        }

        $query = $department->emails()->with([
            'sender',
            'files' => function($q) {
                $q->with('file')->take(5);
            },
            'tags'
        ]);

        $trashedCount = $department->emails()->onlyTrashed()->count();

        // Фильтрация
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($from = $request->input('from')) {
            $query->from($from);
        }

        if ($to = $request->input('to')) {
            $query->to($to);
        }

        if ($request->has('attachments')) {
            $query->withAttachments();
        }

        if ($request->has('important')) {
            $query->important();
        }

        if ($request->has('unread')) {
            $query->unread();
        }

        if ($tagIds = $request->input('tags')) {
            $query->withTags((array) $tagIds);
        }

        // Фильтр по типу
        $filter = $request->input('filter', 'inbox');
        switch ($filter) {
            case 'sent':
                $query->sent();
                break;
            case 'drafts':
                $query->drafts();
                break;
            case 'archived':
                $query->archived();
                break;
            case 'important':
                $query->important();
                break;
            case 'with_attachments':
                $query->withAttachments();
                break;
            default: // inbox
                $query->received()->where('is_archived', false);
                break;
        }

        // Сортировка
        $sort = $request->input('sort', 'sent_at');
        $order = $request->input('order', 'desc');
        $query->orderBy($sort, $order);

        $emails = $query->paginate(20)->withQueryString();
        $tags = $department->company->tags()
            ->where('department_id', $department->id)
            ->withCount('emails')
            ->get();

        // Статистика для виджетов
        $draftCount = $department->emails()->drafts()->count();
        $attachmentsCount = $department->emails()->withAttachments()->count();

        return view('frontend.mail.index', compact(
            'department',
            'emails',
            'tags',
            'filter',
            'draftCount',
            'attachmentsCount',
            'trashedCount'
        ));
    }

    public function search(Request $request, Department $department)
    {
        $query = $department->emails();

        if ($search = $request->input('q')) {
            $query->search($search);
        }

        if ($request->has('unread')) {
            $query->unread();
        }

        $emails = $query->limit(10)->get();

        return response()->json([
            'emails' => $emails->map(function ($email) {
                return [
                    'id' => $email->id,
                    'subject' => $email->subject,
                    'from' => $email->from_name,
                    'body_preview' => Str::limit(strip_tags($email->body), 100),
                    'date' => $email->sent_at?->format('d.m.Y H:i'),
                    'is_read' => $email->is_read,
                ];
            })
        ]);
    }

    public function create(Request $request, Department $department)
    {
//        // Проверка прав доступа
//        if (!auth()->user()->hasPermission('send_emails')) {
//            abort(403, 'У вас нет прав на отправку писем');
//        }

        $templates = EmailTemplate::where(function ($query) use ($department) {
            $query->where('company_id', $department->company_id)
                ->where(function ($q) use ($department) {
                    $q->where('department_id', $department->id)
                        ->orWhere('is_global', true);
                });
        })->where('is_active', true)->get();

        $tags = $department->company->tags()
            ->where('department_id', $department->id)
            ->get();

        return view('frontend.mail.create', compact('department', 'templates', 'tags'));
    }

    public function store(Request $request, Department $department)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'to_emails' => 'required|string',
            'cc_emails' => 'nullable|string',
            'bcc_emails' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'file|max:51200', // 50MB
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'is_important' => 'boolean',
            'save_template' => 'boolean',
            'template_name' => 'nullable|string|max:255',
        ]);

        // Используем SMTP настройки отдела
        $smtpSetting = SmtpSetting::where('department_id', $department->id)
            ->where('is_active', true)
            ->first();

        $emailData = [
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'from_email' => auth()->user()->email,
            'from_name' => auth()->user()->name,
            'to_emails' => array_map('trim', explode(',', $validated['to_emails'])),
            'cc_emails' => $validated['cc_emails'] ? array_map('trim', explode(',', $validated['cc_emails'])) : [],
            'bcc_emails' => $validated['bcc_emails'] ? array_map('trim', explode(',', $validated['bcc_emails'])) : [],
            'sent_by' => auth()->id(),
            'is_draft' => $request->has('save_draft'),
            'is_important' => $request->boolean('is_important'),
            'sent_at' => $request->has('save_draft') ? null : now(),
            'received_at' => $request->has('save_draft') ? null : now(),
        ];

        $email = $department->emails()->create($emailData);

        // Прикрепляем теги
        if ($request->has('tags')) {
            $email->tags()->attach($request->input('tags'));
        }

        // Загружаем файлы
        $hasAttachments = false;
        if ($request->hasFile('files')) {
            $hasAttachments = true;
            foreach ($request->file('files') as $uploadedFile) {
                $path = $uploadedFile->store("companies/{$department->company_id}/departments/{$department->id}/emails", 'public');

                $file = File::create([
                    'name' => $uploadedFile->getClientOriginalName(),
                    'path' => $path,
                    'size' => $uploadedFile->getSize(),
                    'mime_type' => $uploadedFile->getMimeType(),
                    'extension' => $uploadedFile->getClientOriginalExtension(),
                    'uploaded_by' => auth()->id(),
                    'company_id' => $department->company_id,
                    'department_id' => $department->id,
                    'disk' => 'public',
                ]);

                $email->files()->create([
                    'file_id' => $file->id,
                    'original_name' => $uploadedFile->getClientOriginalName(),
                ]);
            }
        }

        if ($hasAttachments) {
            $email->update(['has_attachments' => true]);
        }

        // Сохраняем как шаблон
        if ($request->boolean('save_template') && $request->filled('template_name')) {
            EmailTemplate::create([
                'name' => $validated['template_name'],
                'subject' => $validated['subject'],
                'body' => $validated['body'],
                'company_id' => $department->company_id,
                'department_id' => $department->id,
                'created_by' => auth()->id(),
                'is_global' => false,
            ]);
        }

        // Отправляем через SMTP если не черновик
        if (!$request->has('save_draft') && $smtpSetting) {
            $this->emailService->sendViaSmtp($email, $smtpSetting);

            // Создаем уведомления для получателей
            $this->createNotifications($email);
        }

        $message = $request->has('save_draft') ? 'Письмо сохранено как черновик' : 'Письмо отправлено';

        return redirect()->route('departments.emails.index', $department)
            ->with('success', $message);
    }


    public function show(Department $department, Email $email)
    {

        $email->markAsRead();

        // Проверка прав доступа
        if ($email->department_id !== $department->id) {
            abort(404);
        }

        // Загружаем все связанные данные
        $email->load([
            'files.file',
            'tags',
            'sender'
        ]);

        return view('frontend.mail.show', compact('department', 'email'));
    }

    public function export(Request $request, Department $department)
    {
        $format = $request->input('format', 'xlsx');

        return Excel::download(new EmailsExport($department), "emails_{$department->id}.{$format}");
    }


    public function toggleArchive(Request $request, Department $department, Email $email)
    {
        $this->authorize('archive', $email); // Используйте политику

        $email->update([
            'is_archived' => !$email->is_archived,
        ]);

        $message = $email->is_archived
            ? 'Письмо перемещено в архив'
            : 'Письмо извлечено из архива';

        return back()->with('success', $message);
    }


    public function import(Request $request, Department $department)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
            'format' => 'required|in:csv,excel',
        ]);

        $import = new EmailsImport($department);
        Excel::import($import, $request->file('file'));

        return back()->with('success', 'Письма успешно импортированы');
    }

    public function addTag(Request $request, Department $department, Email $email)
    {
        $request->validate(['tag_id' => 'required|exists:tags,id']);

        $email->addTag(Tag::find($request->tag_id));

        return back()->with('success', 'Метка добавлена');
    }

    public function removeTag(Request $request, Department $department, Email $email)
    {
        $request->validate(['tag_id' => 'required|exists:tags,id']);

        $email->removeTag(Tag::find($request->tag_id));

        return back()->with('success', 'Метка удалена');
    }

    public function bulkAction(Request $request, Department $department)
    {
        $request->validate([
            'action' => 'required|in:archive,delete,mark_read,mark_unread,mark_important',
            'emails' => 'required|array',
            'emails.*' => 'exists:emails,id',
        ]);

        $emails = Email::whereIn('id', $request->emails)
            ->where('department_id', $department->id)
            ->get();

        foreach ($emails as $email) {
            switch ($request->action) {
                case 'archive':
                    $email->toggleArchive();
                    break;
                case 'delete':
                    $email->delete();
                    break;
                case 'mark_read':
                    $email->markAsRead();
                    break;
                case 'mark_unread':
                    $email->update(['is_read' => false]);
                    break;
                case 'mark_important':
                    $email->markAsImportant();
                    break;
            }
        }

        return back()->with('success', 'Действие выполнено');
    }

    private function createNotifications(Email $email): void
    {
        foreach ($email->department->users as $user) {
            if ($user->id !== $email->sent_by) {
                $user->notify(new NewEmailNotification($email));

                // Также сохраняем в БД для внутренних уведомлений
                EmailNotification::create([
                    'user_id' => $user->id,
                    'email_id' => $email->id,
                    'department_id' => $email->department_id,
                    'type' => 'new_email',
                ]);
            }
        }
    }
}
