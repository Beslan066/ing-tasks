<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\UserInvitationMail;

class InvitationController extends Controller
{
    public function invite(Request $request)
    {
        $request->validate([
            'emails' => 'required|string',
            'role_id' => 'nullable|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $authUser = auth()->user();
        $company = $authUser->company;

        if (!$company) {
            return response()->json([
                'success' => false,
                'error' => 'У вас нет компании для приглашения пользователей'
            ], 403);
        }

        $emails = array_map('trim', explode(',', $request->emails));
        $validEmails = [];
        $invalidEmails = [];

        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = $email;
            } else {
                $invalidEmails[] = $email;
            }
        }

        if (empty($validEmails)) {
            return response()->json([
                'success' => false,
                'error' => 'Не указаны корректные email адреса'
            ], 422);
        }

        $role = $request->role_id ? Role::find($request->role_id) : null;
        $department = $request->department_id ? Department::find($request->department_id) : null;

        $invitedCount = 0;
        $alreadyInvited = [];

        foreach ($validEmails as $email) {
            // Проверяем, не приглашен ли уже пользователь
            $existingInvitation = $company->invitations()
                ->where('email', $email)
                ->where('expires_at', '>', now())
                ->whereNull('accepted_at')
                ->first();

            if ($existingInvitation) {
                $alreadyInvited[] = $email;
                continue;
            }

            // Проверяем, не зарегистрирован ли уже пользователь в компании
            $existingUser = $company->users()
                ->where('email', $email)
                ->first();

            if ($existingUser) {
                $alreadyInvited[] = $email;
                continue;
            }

            // Создаем приглашение
            $invitation = $company->inviteUser($email, $authUser, $role, $department);

            // Отправляем email
            try {
                Mail::to($email)->send(new UserInvitationMail($invitation, $authUser));
                $invitedCount++;
            } catch (\Exception $e) {
                Log::error('Failed to send invitation email: ' . $e->getMessage());
                // Приглашение все равно создаем, но логируем ошибку отправки
                $invitedCount++;
            }
        }

        $response = [
            'success' => true,
            'message' => "Приглашения отправлены: {$invitedCount}",
            'data' => [
                'invited_count' => $invitedCount,
                'invalid_emails' => $invalidEmails,
                'already_invited' => $alreadyInvited,
            ]
        ];

        if (!empty($invalidEmails)) {
            $response['warning'] = 'Некорректные email: ' . implode(', ', $invalidEmails);
        }

        if (!empty($alreadyInvited)) {
            $response['info'] = 'Уже приглашены: ' . implode(', ', $alreadyInvited);
        }

        return response()->json($response);
    }

    public function showInvitationForm($token)
    {
        $invitation = Invitation::where('token', $token)
            ->with(['company', 'role', 'department'])
            ->firstOrFail();

        if (!$invitation->isValid()) {
            return view('frontend.invitation-expired', compact('invitation'));
        }

        // Если пользователь уже авторизован
        if (auth()->check()) {
            $user = auth()->user();

            // Если это приглашение на email текущего пользователя
            if ($user->email === $invitation->email) {
                return $this->processInvitationAcceptance($user, $invitation);
            }

            // Если email не совпадает, предлагаем выйти или использовать другой email
            return view('frontend.invitation-different-email', compact('invitation'));
        }

        return view('frontend.invitation-accept', compact('invitation'));
    }

    public function acceptInvitation(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return redirect()->route('login')->with('error', 'Приглашение истекло или уже использовано.');
        }

        // Если пользователь не авторизован, перенаправляем на регистрацию
        if (!auth()->check()) {
            $request->session()->put('invitation_token', $token);
            return redirect()->route('register')->with('invitation', $invitation);
        }

        $user = auth()->user();

        // Проверяем, что email совпадает
        if ($user->email !== $invitation->email) {
            return back()->with('error', 'Приглашение предназначено для другого email адреса.');
        }

        return $this->processInvitationAcceptance($user, $invitation);
    }

    private function processInvitationAcceptance(User $user, Invitation $invitation)
    {
        try {
            DB::transaction(function () use ($user, $invitation) {
                // Обновляем пользователя
                $user->update([
                    'company_id' => $invitation->company_id,
                    'role_id' => $invitation->role_id,
                    'department_id' => $invitation->department_id,
                ]);

                // Отмечаем приглашение как принятое
                $invitation->markAsAccepted();

                // Логируем событие в файл
                Log::info('User accepted invitation', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'invitation_id' => $invitation->id,
                    'company_id' => $invitation->company_id,
                    'company_name' => $invitation->company->name,
                    'invited_by' => $invitation->inviter->id,
                    'role_id' => $invitation->role_id,
                    'department_id' => $invitation->department_id,
                    'accepted_at' => now()->toDateTimeString(),
                ]);
            });

            return redirect()->route('home')->with('success', 'Вы успешно присоединились к компании!');

        } catch (\Exception $e) {
            Log::error('Invitation acceptance failed: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'invitation_id' => $invitation->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Произошла ошибка при принятии приглашения.');
        }
    }

    public function getInvitations()
    {
        $authUser = auth()->user();
        $company = $authUser->company;

        if (!$company) {
            return response()->json([
                'success' => false,
                'error' => 'Компания не найдена'
            ], 404);
        }

        $invitations = $company->getActiveInvitations();

        return response()->json([
            'success' => true,
            'invitations' => $invitations
        ]);
    }

    public function cancelInvitation($id)
    {
        $authUser = auth()->user();
        $invitation = Invitation::where('id', $id)
            ->whereHas('company', function($query) use ($authUser) {
                $query->where('id', $authUser->company_id);
            })
            ->firstOrFail();

        $invitation->update(['expires_at' => now()]);

        // Логируем отмену приглашения
        Log::info('Invitation cancelled', [
            'invitation_id' => $invitation->id,
            'cancelled_by' => $authUser->id,
            'email' => $invitation->email,
            'cancelled_at' => now()->toDateTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Приглашение отменено'
        ]);
    }

    public function searchUsers(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2'
        ]);

        $authUser = auth()->user();
        $company = $authUser->company;

        if (!$company) {
            return response()->json([
                'success' => false,
                'error' => 'Компания не найдена'
            ], 404);
        }

        $searchTerm = $request->search;

        // Ищем пользователей по имени или email
        $users = User::where(function($query) use ($searchTerm) {
            $query->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('email', 'like', "%{$searchTerm}%");
        })
            ->where('id', '!=', $authUser->id) // Исключаем текущего пользователя
            ->limit(10)
            ->get(['id', 'name', 'email', 'company_id']);

        $results = [];

        foreach ($users as $user) {
            $isInCompany = $user->company_id === $company->id;
            $hasActiveInvitation = false;

            if (!$isInCompany) {
                // Проверяем активные приглашения
                $hasActiveInvitation = $company->invitations()
                    ->where('email', $user->email)
                    ->where('expires_at', '>', now())
                    ->whereNull('accepted_at')
                    ->exists();
            }

            $results[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_in_company' => $isInCompany,
                'has_active_invitation' => $hasActiveInvitation,
                'status' => $isInCompany ? 'already_member' :
                    ($hasActiveInvitation ? 'already_invited' : 'can_invite')
            ];
        }

        return response()->json([
            'success' => true,
            'users' => $results
        ]);
    }
}
