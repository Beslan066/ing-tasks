<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Проверяем, есть ли у пользователя доступ к чату
        if (!$user->company_id) {
            return redirect()->route('dashboard')->with('error', 'Вы не состоите в компании');
        }

        return view('frontend.chat.index', [
            'user' => $user,
            'company' => $user->company,
            'isLeader' => $user->isLeader(),
            'isManager' => $user->isManagerRole(),
            'isCompanyOwner' => $user->isCompanyOwner(),
        ]);
    }
}
