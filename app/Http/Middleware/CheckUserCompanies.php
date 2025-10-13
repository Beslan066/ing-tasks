<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserCompanies
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, аутентифицирован ли пользователь
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Проверяем, является ли пользователь руководителем
        $isCompanyOwner = $user->isCompanyOwner();

        if (!$isCompanyOwner) {
            // Если пользователь не является руководителем, перенаправляем на специальный роут
            return redirect()->route('no.companies');
        }

        // Если пользователь является руководителем, продолжаем запрос
        return $next($request);
    }
}
