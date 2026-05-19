<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
        $driver = Socialite::driver('google');
        $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
        return $driver->redirect();
    }

    public function callback()
    {
        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver('google');
            $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
            $googleUser = $driver->user();

            // Only allow institutional @ans.edu.ni accounts
            if (!str_ends_with($googleUser->getEmail(), '@ans.edu.ni')) {
                return redirect('/')->with('error', 'Solo se permiten cuentas institucionales (@ans.edu.ni).');
            }

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    // New users get 'student' role by default.
                    // Existing users keep their current role (admin/teacher).
                ]
            );

            // Set default role for brand-new users (no role assigned yet)
            if (!$user->role) {
                $user->role = 'student';
                $user->save();
            }

            Auth::login($user);

            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect('/admin/solicitudes');
            }

            return redirect('/')->with('success', 'Sesión iniciada con éxito.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Auth Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect('/')->with('error', 'Error al iniciar sesión con Google: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
