<?php
// app/Http/Controllers/EventController.php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;
        $user = auth()->user();

        $events = Event::forCompany($company->id)
            ->with(['creator', 'participants', 'department'])
            ->get();

        // Получаем пользователей компании
        $users = User::where('company_id', $company->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'email']);

        // Для отладки - проверяем что пользователи есть
        \Log::info('Users count in controller: ' . $users->count());

        $formattedEvents = $events->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->all_day ? $event->start_date->format('Y-m-d') : $event->start_date->toISOString(),
                'end' => $event->all_day ? $event->end_date->format('Y-m-d') : $event->end_date->toISOString(),
                'allDay' => (bool)$event->all_day,
                'color' => $event->color ?? $this->getEventColor($event),
                'extendedProps' => [
                    'id' => $event->id,
                    'description' => $event->description,
                    'location' => $event->location,
                    'creator_id' => $event->creator_id,
                    'creator_name' => $event->creator ? $event->creator->name : null,
                    'department_id' => $event->department_id,
                    'department_name' => $event->department ? $event->department->name : null,
                    'participants' => $event->participants->map(function($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'status' => $p->pivot->status ?? 'invited'
                        ];
                    }),
                    'participants_count' => $event->participants->count(),
                    'status' => $event->status,
                    'status_label' => $event->getStatusLabel(),
                    'type' => $event->type,
                    'type_label' => $event->getTypeLabel(),
                    'priority' => $event->priority,
                    'priority_label' => $event->getPriorityLabel(),
                ],
            ];
        });

        $departments = Department::where('company_id', $company->id)
            ->where('status', 'active')
            ->get();

        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;

        return view('frontend.events.index', compact(
            'formattedEvents',
            'users', // Убедитесь что users передается
            'departments',
            'backgroundEnabled',
            'backgroundImage',
            'company'
        ));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('=== STORE EVENT ===');
            \Log::info('Request data:', $request->all());
            \Log::info('Participants from request:', $request->input('participants', []));

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'all_day' => 'nullable|boolean',
                'location' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:7',
                'department_id' => 'nullable|exists:departments,id',
                'participants' => 'nullable|array',
                'participants.*' => 'exists:users,id',
                'type' => 'nullable|string|in:meeting,deadline,reminder,other',
                'priority' => 'nullable|string|in:low,medium,high',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $company = auth()->user()->company;

            $event = Event::create([
                'company_id' => $company->id,
                'creator_id' => auth()->id(),
                'department_id' => $request->department_id,
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type ?? 'other',
                'priority' => $request->priority ?? 'medium',
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'all_day' => $request->all_day ?? false,
                'location' => $request->location,
                'color' => $request->color,
                'status' => 'planned',
                'is_public' => true,
            ]);

            // ✅ ДОБАВЛЯЕМ ТОЛЬКО ВЫБРАННЫХ УЧАСТНИКОВ
            $participants = $request->input('participants', []);

            if (!is_array($participants)) {
                $participants = [];
            }

            $participants = array_map('intval', $participants);
            $participants = array_unique($participants);

            $participantsData = [];

            // ✅ Добавляем ТОЛЬКО выбранных участников
            foreach ($participants as $userId) {
                if (User::where('id', $userId)->exists()) {
                    // ✅ НЕ ПРОВЕРЯЕМ НА СОЗДАТЕЛЯ - добавляем всех
                    $participantsData[$userId] = ['status' => 'invited'];
                }
            }

            // ✅ НЕ ДОБАВЛЯЕМ СОЗДАТЕЛЯ АВТОМАТИЧЕСКИ

            \Log::info('Final participants data (only selected):', $participantsData);

            $event->participants()->sync($participantsData);

            $saved = $event->participants()->pluck('user_id')->toArray();
            \Log::info('Saved participants IDs:', $saved);

            DB::commit();

            $event->load(['creator', 'participants', 'department']);

            return response()->json([
                'success' => true,
                'message' => 'Событие успешно создано',
                'event' => $this->formatEvent($event),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Event store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании события: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Event $event)
    {
        try {
            \Log::info('=== UPDATE EVENT ===');
            \Log::info('Request data:', $request->all());
            \Log::info('Participants from request:', $request->input('participants', []));

            if (!auth()->user()->can('update', $event)) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав на редактирование этого события',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'all_day' => 'nullable|boolean',
                'location' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:7',
                'department_id' => 'nullable|exists:departments,id',
                'participants' => 'nullable|array',
                'participants.*' => 'exists:users,id',
                'type' => 'nullable|string|in:meeting,deadline,reminder,other',
                'priority' => 'nullable|string|in:low,medium,high',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $event->update([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type ?? $event->type,
                'priority' => $request->priority ?? $event->priority,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'all_day' => $request->all_day ?? false,
                'location' => $request->location,
                'color' => $request->color,
                'department_id' => $request->department_id,
            ]);

            // ✅ ОБНОВЛЯЕМ ТОЛЬКО ВЫБРАННЫХ УЧАСТНИКОВ
            $participants = $request->input('participants', []);

            if (!is_array($participants)) {
                $participants = [];
            }

            $participants = array_map('intval', $participants);
            $participants = array_unique($participants);

            $participantsData = [];

            // ✅ Добавляем ТОЛЬКО выбранных участников
            foreach ($participants as $userId) {
                if (User::where('id', $userId)->exists()) {
                    // ✅ НЕ ПРОВЕРЯЕМ НА СОЗДАТЕЛЯ - добавляем всех
                    $participantsData[$userId] = ['status' => 'invited'];
                }
            }

            // ✅ НЕ ДОБАВЛЯЕМ СОЗДАТЕЛЯ АВТОМАТИЧЕСКИ

            \Log::info('Final participants data (only selected):', $participantsData);

            $event->participants()->sync($participantsData);

            $saved = $event->participants()->pluck('user_id')->toArray();
            \Log::info('Saved participants IDs:', $saved);

            DB::commit();

            $event->load(['creator', 'participants', 'department']);

            return response()->json([
                'success' => true,
                'message' => 'Событие обновлено',
                'event' => $this->formatEvent($event),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Event update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении события: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Event $event)
    {
        try {
            if (!auth()->user()->can('delete', $event)) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав на удаление этого события',
                ], 403);
            }

            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Событие удалено',
            ]);

        } catch (\Exception $e) {
            Log::error('Event delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении события: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function respond(Request $request, Event $event)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:confirmed,declined,maybe',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $event->participants()->updateExistingPivot(auth()->id(), [
                'status' => $request->status,
                'responded_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ваш ответ сохранен',
            ]);

        } catch (\Exception $e) {
            Log::error('Event respond error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getEvents()
    {
        try {
            $company = auth()->user()->company;

            $events = Event::forCompany($company->id)
                ->with(['creator', 'participants', 'department'])
                ->get();

            $formattedEvents = $events->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->all_day ? $event->start_date->format('Y-m-d') : $event->start_date->toISOString(),
                    'end' => $event->all_day ? $event->end_date->format('Y-m-d') : $event->end_date->toISOString(),
                    'allDay' => (bool)$event->all_day,
                    'color' => $event->color ?? $this->getEventColor($event),
                    'extendedProps' => [
                        'id' => $event->id,
                        'description' => $event->description,
                        'location' => $event->location,
                        'creator_id' => $event->creator_id,
                        'creator_name' => $event->creator ? $event->creator->name : null,
                        'department_id' => $event->department_id,
                        'department_name' => $event->department ? $event->department->name : null,
                        'participants' => $event->participants->map(function($p) {
                            return [
                                'id' => $p->id,
                                'name' => $p->name,
                                'status' => $p->pivot->status ?? 'invited'
                            ];
                        }),
                        'participants_count' => $event->participants->count(),
                        'status' => $event->status,
                        'status_label' => $event->getStatusLabel(),
                        'type' => $event->type,
                        'type_label' => $event->getTypeLabel(),
                        'priority' => $event->priority,
                        'priority_label' => $event->getPriorityLabel(),
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'events' => $formattedEvents,
            ]);

        } catch (\Exception $e) {
            Log::error('Get events error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке событий',
            ], 500);
        }
    }

    private function formatEvent($event)
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'start' => $event->all_day ? $event->start_date->format('Y-m-d') : $event->start_date->toISOString(),
            'end' => $event->all_day ? $event->end_date->format('Y-m-d') : $event->end_date->toISOString(),
            'allDay' => (bool)$event->all_day,
            'color' => $event->color ?? $this->getEventColor($event),
            'extendedProps' => [
                'id' => $event->id,
                'description' => $event->description,
                'location' => $event->location,
                'creator_id' => $event->creator_id,
                'creator_name' => $event->creator ? $event->creator->name : null,
                'department_id' => $event->department_id,
                'department_name' => $event->department ? $event->department->name : null,
                'participants' => $event->participants->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'status' => $p->pivot->status ?? 'invited'
                    ];
                }),
                'participants_count' => $event->participants->count(),
                'status' => $event->status,
                'status_label' => $event->getStatusLabel(),
                'type' => $event->type,
                'type_label' => $event->getTypeLabel(),
                'priority' => $event->priority,
                'priority_label' => $event->getPriorityLabel(),
            ],
        ];
    }
}
