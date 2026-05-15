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
        $driver = Socialite::driver('google');
        $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
        return $driver->redirect();
    }

    public function callback()
    {
        try {
            $driver = Socialite::driver('google');
            $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
            $googleUser = $driver->user();

            // Validate domain (optional, if you only want ans.edu.ni users)
            // if (!str_ends_with($googleUser->email, '@ans.edu.ni')) {
            //     return redirect('/')->with('error', 'Solo se permiten cuentas institucionales.');
            // }

            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    // If they already exist, we don't overwrite the role. 
                    // If they are new, they default to 'teacher' per migration.
                ]
            );

            Auth::login($user);

            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect('/admin/solicitudes');
            }

            return redirect('/')->with('success', 'Sesión iniciada con éxito.');

        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getTraceAsString());
            return redirect('/')->with('error', 'Error al iniciar sesión con Google.');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
