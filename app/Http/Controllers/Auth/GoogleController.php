<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


class GoogleController extends Controller
{
    /**
     * Redirect user to Google login page
     */
    public function redirect()
    {
     
     return Socialite::driver('google')
        ->with(['prompt' => 'select_account']) 
        ->redirect();
    }

    /**
     * Handle callback from Google
     */
    public function callback()
    {
        // get user from Google
      
        $googleUser = Socialite::driver('google')->user();

        // check if user exists (by google_id or email)
        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        // if user does not exist -> create new account
       if (!$user) {
    $user = User::create([
        'name' => $googleUser->getName(),
        'email' => $googleUser->getEmail(),
        'google_id' => $googleUser->getId(),
        'password' => bcrypt(uniqid()),
        'role' => 'job-seeker',
    ]);
}
// اذا المستحدم دخل قبل 
else {
    $user->google_id = $googleUser->getId();

    // 🔥 IMPORTANT FIX
    if (!$user->role) {
        $user->role = 'job-seeker';
    }

    $user->save();
}

        // login user
        Auth::login($user);

        // redirect to dashboard
        return redirect()->route('dashboard');
    }
}