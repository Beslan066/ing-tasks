<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SitePasswordController extends Controller
{
    private const SITE_PASSWORD = 'Harbor88';

    public function show()
    {
        if (session('site_unlocked')) {
            return redirect('/');
        }

        return view('protect.site-password');
    }

    public function check(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        if (hash_equals(self::SITE_PASSWORD, $request->input('password'))) {
            $request->session()->put('site_unlocked', true);
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors(['password' => 'Неверный пароль'])->onlyInput();
    }
}
