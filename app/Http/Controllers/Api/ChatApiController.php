<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use App\Models\Department;
use App\Models\Message;
use App\Models\ChatUser;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatApiController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function getChats()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $chats = $this->chatService->getUserChats($user);

            return response()->json([
                'success' => true,
                'chats' => $chats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getColleagues()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            if (!$user->company_id) {
                return response()->json([
                    'success' => true,
                    'colleagues' => []
                ]);
            }

            $colleagues = User::where('company_id', $user->company_id)
                ->where('id', '!=', $user->id)
                ->where('is_active', true)
                ->get()
                ->map(function ($colleague) {
                    $primaryDepartment = null;
                    try {
                        $primaryDepartment = $colleague->primaryDepartment();
                    } catch (\Exception $e) {
                        $primaryDepartment = $colleague->departments()->first();
                    }

                    $avatarUrl = null;
                    if ($colleague->avatar && !empty($colleague->avatar)) {
                        if (Storage::exists($colleague->avatar)) {
                            $avatarUrl = Storage::url($colleague->avatar);
                        }
                    } elseif ($colleague->provider_avatar && !empty($colleague->provider_avatar)) {
                        $avatarUrl = $colleague->provider_avatar;
                    }

                    return [
                        'id' => $colleague->id,
                        'name' => $colleague->name,
                        'email' => $colleague->email,
                        'avatar' => $avatarUrl,
                        'initials' => $colleague->getInitials(),
                        'avatar_color' => $colleague->getAvatarColor(), // <-- ЭТО ДОБАВИТЬ!
                        'is_online' => $colleague->isOnline(),
                        'last_activity' => $colleague->last_activity_at,
                        'department' => $primaryDepartment?->name,
                        'role' => $colleague->role?->name,
                    ];
                });

            return response()->json([
                'success' => true,
                'colleagues' => $colleagues,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading colleagues: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки коллег: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMessages(Request $request, Chat $chat)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            if (!$chat->isUserInChat($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому чату'
                ], 403);
            }

            $limit = $request->get('limit', 50);
            $after = $request->get('after', 0);
            $before = $request->get('before', 0);

            $query = $chat->messages()
                ->with(['user', 'statuses' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }]);

            if ($after) {
                // Получаем сообщения после указанного ID (новые)
                $messages = $query->where('id', '>', $after)
                    ->orderBy('id', 'asc')
                    ->get();
            } elseif ($before) {
                // Получаем сообщения до указанного ID (старые)
                $messages = $query->where('id', '<', $before)
                    ->orderBy('id', 'desc')
                    ->take($limit)
                    ->get()
                    ->reverse()
                    ->values();
            } else {
                // Первая загрузка - последние сообщения
                $messages = $query->orderBy('id', 'desc')
                    ->take($limit)
                    ->get()
                    ->reverse()
                    ->values();
            }

            return response()->json([
                'success' => true,
                'messages' => [
                    'data' => $messages->map(function ($message) use ($user) {
                        return $this->formatMessage($message, $user);
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading messages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            if (!$chat->isUserInChat($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы не являетесь участником этого чата'
                ], 403);
            }

            $request->validate([
                'content' => 'required|string',
                'type' => 'sometimes|in:text,file,image',
            ]);

            $message = $this->chatService->sendMessage(
                $chat,
                $user,
                $request->content,
                $request->type ?? 'text',
                $request->parent_id
            );

            return response()->json([
                'success' => true,
                'message' => $this->formatMessage($message, $user),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadFile(Request $request, Chat $chat)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            if (!$chat->isUserInChat($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы не являетесь участником этого чата',
                ], 403);
            }

            $request->validate([
                'file' => 'required|file|max:102400',
            ]);

            $file = $request->file('file');
            $path = $file->store('chat-files', 'public');

            $message = $this->chatService->sendMessage(
                $chat,
                $user,
                $file->getClientOriginalName(),
                str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file'
            );

            $message->update([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_mime_type' => $file->getMimeType(),
            ]);

            return response()->json([
                'success' => true,
                'message' => $this->formatMessage($message->fresh(['user', 'statuses']), $user),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function createPrivateChat(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $otherUser = User::find($request->user_id);

            $chat = $this->chatService->createPrivateChat(
                $user,
                $otherUser,
                $user->company
            );

            $chats = $this->chatService->getUserChats($user);
            $formattedChat = $chats->firstWhere('id', $chat->id);

            return response()->json([
                'success' => true,
                'chat' => $formattedChat,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function createGroupChat(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id',
            ]);

            $chat = $this->chatService->createGroupChat(
                $request->only(['name', 'description', 'user_ids']),
                $user,
                $user->company
            );

            $chats = $this->chatService->getUserChats($user);
            $formattedChat = $chats->firstWhere('id', $chat->id);

            return response()->json([
                'success' => true,
                'chat' => $formattedChat,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function createDepartmentChat(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $request->validate([
                'department_id' => 'required|exists:departments,id',
            ]);

            $department = Department::find($request->department_id);

            if ($user->id !== $department->supervisor_id && !$user->isCompanyOwner()) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав для создания чата отдела',
                ], 403);
            }

            $chat = $this->chatService->createDepartmentChat($department, $user);

            $chats = $this->chatService->getUserChats($user);
            $formattedChat = $chats->firstWhere('id', $chat->id);

            return response()->json([
                'success' => true,
                'chat' => $formattedChat,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function createCompanyChat(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            if (!$user->isCompanyOwner()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Только владелец компании может создать общий чат',
                ], 403);
            }

            $chat = $this->chatService->createCompanyChat($user->company, $user);

            $chats = $this->chatService->getUserChats($user);
            $formattedChat = $chats->firstWhere('id', $chat->id);

            return response()->json([
                'success' => true,
                'chat' => $formattedChat,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function markRead(Request $request, Chat $chat)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $request->validate([
                'message_ids' => 'required|array',
                'message_ids.*' => 'exists:messages,id',
            ]);

            if (!$chat->isUserInChat($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому чату',
                ], 403);
            }

            $this->chatService->markMessagesAsRead($chat, $user, $request->message_ids);

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking messages as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function addUsers(Request $request, Chat $chat)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $request->validate([
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id',
            ]);

            $this->chatService->addUsersToChat($chat, $request->user_ids, $user);

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    public function removeUser(Request $request, Chat $chat)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $targetUser = User::find($request->user_id);

            $this->chatService->removeUserFromChat($chat, $targetUser, $user);

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    public function deleteChat(Chat $chat)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $this->chatService->deleteChat($chat, $user);

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    public function getAvailableUsers(Request $request, Chat $chat)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            if (!$chat->users()->where('user_id', $user->id)->wherePivot('role', ChatUser::ROLE_ADMIN)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав для просмотра доступных пользователей',
                ], 403);
            }

            $users = User::where('company_id', $user->company_id)
                ->where('is_active', true)
                ->whereNotIn('id', $chat->users()->pluck('user_id'))
                ->get()
                ->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'name' => $u->name,
                        'avatar' => $u->getAvatarUrlAttribute(),
                        'initials' => $u->getInitials(),
                        'avatar_color' => $u->getAvatarColor(),
                        'is_online' => $u->isOnline(),
                        'department' => $u->primaryDepartment?->name,
                    ];
                });

            return response()->json([
                'success' => true,
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUnreadCounts()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            $chats = Chat::forUser($user)->get();
            $counts = $chats->map(function ($chat) use ($user) {
                return [
                    'id' => $chat->id,
                    'unread_count' => $chat->getUnreadCount($user),
                ];
            });

            return response()->json([
                'success' => true,
                'counts' => $counts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function formatMessage($message, $user)
    {
        $status = $message->statuses()->where('user_id', $user->id)->first();

        // ПОЛУЧАЕМ РОДИТЕЛЬСКОЕ СООБЩЕНИЕ
        $parent = null;
        if ($message->parent_id) {
            $parent = Message::with('user')->find($message->parent_id);
        }

        return [
            'id' => $message->id,
            'chat_id' => $message->chat_id,
            'user_id' => $message->user_id,
            'parent_id' => $message->parent_id,
            'user' => [
                'id' => $message->user->id,
                'name' => $message->user->name,
                'avatar' => $message->user->avatar ? Storage::url($message->user->avatar) : null,
                'avatar_color' => $message->user->getAvatarColor(),
                'initials' => $message->user->getInitials(),
            ],
            'parent' => $parent ? [
                'id' => $parent->id,
                'content' => $parent->content,
                'user' => [
                    'id' => $parent->user->id,
                    'name' => $parent->user->name,
                ],
            ] : null,
            'content' => $message->content,
            'type' => $message->type,
            'file_url' => $message->file_path ? Storage::url($message->file_path) : null,
            'file_name' => $message->file_name,
            'formatted_file_size' => $message->getFormattedFileSize(),
            'file_icon' => $message->getFileIcon(),
            'is_edited' => $message->is_edited,
            'created_at' => $message->created_at,
            'updated_at' => $message->updated_at,
            'status' => $status ? $status->status : 'sent',
            'statuses' => $status ? [
                'status' => $status->status,
                'delivered_at' => $status->delivered_at,
                'read_at' => $status->read_at,
            ] : null,
        ];
    }
}
