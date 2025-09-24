<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {

        if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            if (auth()->user()->role === 'manager'){
              
                // Log::info('Redirecting to manager dashboard.');
                return redirect()->intended(route('dashboard_sales.index'));
            }
            elseif (auth()->user()->role === 'employee'){
           
                // Log::info('Redirecting to employee dashboard.');
                return redirect()->intended(route('dashboard_sales.index'));
            }
            elseif (auth()->user()->role === 'client'){
                // Log::info('Redirecting to employee dashboard.');
               
                return redirect()->intended(route('clients.personal'));
            }
            auth()->logout();
            return back()->withErrors(['email' => 'الدور غير صالح.']);
        }
        return back()->withErrors(['email' => 'بيانات الاعتماد غير صحيحة.']);

    }

    public function logout()
    {
        session()->forget('user_id');
        return redirect()->route('login');
    }



}
