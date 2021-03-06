<?php

namespace App\Http\Controllers;

use App\Http\Requests\PassReset;
use App\Http\Requests\UserCreator;
use App\Models\User;
use App\Notifications\Verificationurl;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function userInfo(Request $request)
    {
        return response()->json($request->user(), 200) ;
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
        return response()->json($user);
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        response()->json('logout successful', 200);
    }
    public function register(UserCreator $request)
    {
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->passowrd),
        ]);
        $user->token = $user->createToken('assignment')->plainTextToken;
        $user->verificationNotification();
        return response()->json(['user' => $user, 'message' => 'verification link has been sent to your email']);
    }
    public function updateUserInfo(Request $request)
    {
        $user = $request->user();
        $user->name = $request->name ?? $user->name;
        if ($request->email) {
            $user->email = $request->email;
            $user->email_verified_at = null;
            $request->user()->verificationNotification();
        }
        return $user->save();
    }
    public function changePassword(PassReset $request)
    {
        $user = $request->user()->forceFill([
            'password' => Hash::make($request->new_password),
        ]);
        $user->tokens()->delete();
        $user->save();
        return response()->json(['message' => 'Password has been changed']);
    }

    public function verifyEmail(Request $request, User $user, $code)
    {
        $value = Cache::store('database')->pull($user->id . '_email_verification_code');
        if (!$value || $value !== $code) {
            return response()->json(['message' => 'url is invalid'], 400);
        }
        $user->markEmailAsVerified();
        return response()->json(['message' => 'Your email is verified now']);
    }
    public function sendVerificationMail(Request $request)
    {
        $request->user()->verificationNotification();
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
        return $status === Password::PASSWORD_RESET ? response()->json(['status' => __($status)]) : response()->json(['errors' => __($status)],422);
    }
}
