<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    public function edit(): View
    {
        return view('admin.auth.password');
    }

    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $user->password = Hash::make($request->validated('password'));
        $user->must_change_password = false;
        $user->save();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Пароль успешно изменён.');
    }
}
