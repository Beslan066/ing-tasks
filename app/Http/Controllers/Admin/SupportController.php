<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Mail\TicketReplyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SupportController extends Controller
{
    // Список всех обращений
    public function index(Request $request)
    {
        $query = SupportTicket::withCount('replies')->latest();

        // Фильтр по статусу
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        $tickets = $query->paginate(15);

        return view('admin.support.index', compact('tickets'));
    }

    // Просмотр одного обращения
    public function show(SupportTicket $ticket)
    {
        $ticket->load('replies.admin');
        return view('admin.support.show', compact('ticket'));
    }

    // Обновление статуса
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,answered,closed'
        ]);

        $ticket->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Статус обновлён']);
    }

    // Отправка ответа пользователю
    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string|min:3|max:5000'
        ]);

        // Сохраняем ответ
        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => strip_tags($request->message),
            'is_admin' => true
        ]);

        // Меняем статус на "отвечено"
        $ticket->update(['status' => SupportTicket::STATUS_ANSWERED]);

        // Отправляем email пользователю
        try {
            Mail::to($ticket->email)->send(new TicketReplyMail($ticket, $reply, auth()->user()->name));
        } catch (\Exception $e) {
            \Log::error('Ошибка отправки ответа: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Ответ отправлен пользователю!');
    }

    // Скачивание файла
    public function download(SupportTicket $ticket)
    {
        if (!$ticket->hasAttachment()) {
            abort(404, 'Файл не найден');
        }

        return Storage::download($ticket->attachment_path, $ticket->attachment_original_name);
    }

    // Удаление обращения
    public function destroy(SupportTicket $ticket)
    {
        // Удаляем файл, если он есть
        if ($ticket->hasAttachment()) {
            Storage::delete($ticket->attachment_path);
        }

        $ticket->delete();

        return redirect()->route('admin.support.index')->with('success', 'Обращение удалено');
    }

    // Статистика (для дашборда)
    public function statistics()
    {
        return [
            'total' => SupportTicket::count(),
            'new' => SupportTicket::where('status', SupportTicket::STATUS_NEW)->count(),
            'in_progress' => SupportTicket::where('status', SupportTicket::STATUS_IN_PROGRESS)->count(),
            'answered' => SupportTicket::where('status', SupportTicket::STATUS_ANSWERED)->count(),
            'closed' => SupportTicket::where('status', SupportTicket::STATUS_CLOSED)->count(),
        ];
    }
}
