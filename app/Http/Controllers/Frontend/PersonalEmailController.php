<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalEmailController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $department = $user->department_id;

        // Основной запрос для писем пользователя
        $query = $user->emails()->with([
            'sender',
            'files' => function($q) {
                $q->with('file')->take(5);
            },
        ]);

        // Фильтр по типу
        $filter = $request->input('filter', 'inbox');

        switch ($filter) {
            case 'sent':
                // Письма, отправленные пользователем
                $query = Email::where('sender_id', $user->id)
                    ->with(['recipient', 'files.file']);
                break;

            case 'drafts':
                $query->where('is_draft', true);
                break;

            case 'archived':
                $query->where('is_archived', true);
                break;

            case 'important':
                $query->where('is_important', true);
                break;

            case 'with_attachments':
                $query->where('has_attachments', true);
                break;

            default: // inbox
                $query->where('recipient_id', $user->id)
                    ->where('recipient_type', User::class)
                    ->where('is_archived', false);
                break;
        }

        // Поиск
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Фильтры
        if ($request->has('unread')) {
            $query->where('is_read', false);
        }

        if ($request->has('attachments')) {
            $query->where('has_attachments', true);
        }

        if ($request->has('important')) {
            $query->where('is_important', true);
        }


        // Сортировка
        $sort = $request->input('sort', 'sent_at');
        $order = $request->input('order', 'desc');
        $query->orderBy($sort, $order);

        // Получаем данные для пагинации
        $emails = $query->paginate(20)->withQueryString();

        // Статистика для виджетов
        $inboxCount = $user->emails()
            ->where('is_archived', false)
            ->count();

        $sentCount = Email::where('sender_id', $user->id)->count();
        $draftCount = $user->emails()->where('is_draft', true)->count();
        $archivedCount = $user->emails()->where('is_archived', true)->count();
        $importantCount = $user->emails()->where('is_important', true)->count();
        $withAttachmentsCount = $user->emails()->where('has_attachments', true)->count();
        $trashedCount = $user->emails()->onlyTrashed()->count();

        // Отделы компании для быстрого доступа
        $companyDepartments = $user->company->departments()
            ->whereNotNull('email')
            ->orderBy('name')
            ->get();



        return view('frontend.mail.personal.index', compact(
            'emails',
            'filter',
            'inboxCount',
            'sentCount',
            'draftCount',
            'archivedCount',
            'importantCount',
            'withAttachmentsCount',
            'trashedCount',
            'companyDepartments',
            'department'
        ));
    }

    public function create()
    {
        $user = auth()->user();
        $departments = $user->company->departments()->whereNotNull('email')->get();
        $users = $user->company->users()->whereNotNull('email')->get();

        return view('frontend.mail.personal.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'recipient_type' => 'required|in:user,department',
            'recipient_id' => 'required',
            'files' => 'nullable|array',
            'files.*' => 'file|max:51200',
        ]);

        $user = auth()->user();

        // Определяем получателя
        $recipient = null;
        if ($validated['recipient_type'] === 'user') {
            $recipient = User::find($validated['recipient_id']);
        } else {
            $recipient = Department::find($validated['recipient_id']);
        }

        if (!$recipient) {
            return back()->withErrors(['recipient' => 'Получатель не найден']);
        }

        $emailData = [
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'from_email' => $user->email,
            'from_name' => $user->name,
            'to_emails' => [$recipient->email],
            'sender_id' => $user->id,
            'recipient_id' => $recipient->id,
            'recipient_type' => $validated['recipient_type'],
            'is_draft' => $request->has('save_draft'),
            'sent_at' => $request->has('save_draft') ? null : now(),
            'received_at' => $request->has('save_draft') ? null : now(),
        ];

        $email = Email::create($emailData);

        // Отправка через SMTP и обработка файлов...

        return redirect()->route('personal.emails.index')
            ->with('success', $request->has('save_draft') ? 'Письмо сохранено как черновик' : 'Письмо отправлено');
    }

    public function show(Email $email)
    {
        $user = auth()->user();

        // Проверка прав доступа
        if ($email->recipient_id !== $user->id && $email->sender_id !== $user->id) {
            abort(403);
        }

        $email->markAsRead();
        $email->load(['files.file', 'sender']);

        return view('frontend.mail.personal.show', compact('email'));
    }
}
