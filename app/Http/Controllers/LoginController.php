<?php

namespace App\Http\Controllers;

use App\Dto\LoginRequestDto;
use App\Service\Contract\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return inertia('auth/login');
    }
    public function checkLogin(Request $request, AuthServiceInterface $authService)
    {
        $request->validate([
            'email' => ['required'],
            'password' => ['required']
        ]);
        $user = new LoginRequestDto();
        $user->fromRequest($request);
        return $authService->validateLogin($user);
    }
    public function logout(Request $request){
        Auth::logout(); 
        return to_route('login');
    }
}
