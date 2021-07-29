<?php

namespace App\Http\Controllers;

use App\Http\Requests\PassReset;
use App\Http\Requests\UserCreator;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function userInfo(Request $request)
    {
        return $request->user();
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect'
            ]);
        }
        $user->tokens()->delete();
        $user->token = $user->createToken('assignment')->plainTextToken;
        return response()->json(['user' => $user]);
    }
    public function register(UserCreator $request)
    {
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->passowrd),
        ]);
        return response()->json(['user' => $user]);
    }
    public function changePassword(PassReset $request)
    {
        $request->user->forceFill([
            'password' => Hash::make($request->new_password),
        ]);
        $request->user->tokens()->delete();
        $request->user->save();
        return response()->json(['message' => 'Password has been changed']);
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return response()->json(['user' => $request->user(), 'message' => 'Your email has been verified']);
    }
    public function sendVerificationMail(Request $request)
    {
        $request->user()->sendVerificationMail();
        return response()->json(['message' => 'Email verification link sent']);
    }
    
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );
    
        return $status === Password::RESET_LINK_SENT
                    ? response()->json(['message' => __($status)])
                    : response()->json(['message' => __($status)]); 
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);
    
                $user->save();
    
                event(new PasswordReset($user));
            }
        );
        return $status === Password::PASSWORD_RESET ? response()->json(['status' => $status]) : response()->json(['errors' => $status],422);
    }
}
