<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class GoogleController extends Controller
{
    public function redirect()
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver('google');
        $driver->setHttpClient(new Client(['verify' => false]));

        return $driver->redirect();
    }

    public function exportAuthorize()
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver('google');
        $driver->setHttpClient(new Client(['verify' => false]));

        return $driver->scopes([
            'https://www.googleapis.com/auth/documents',
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/classroom.courses.readonly',
            'https://www.googleapis.com/auth/classroom.coursework.students',
            'https://www.googleapis.com/auth/classroom.announcements',
            'https://www.googleapis.com/auth/presentations',
            'https://www.googleapis.com/auth/calendar.events',
        ])->redirect();
    }

    public function callback()
    {
        try {
            /** @var AbstractProvider $driver */
            $driver = Socialite::driver('google');
            $driver->setHttpClient(new Client(['verify' => false]));
            $googleUser = $driver->user();

            // Verify allowed domains from env (default to ans.edu.ni)
            $allowedDomains = env('ALLOWED_GOOGLE_DOMAINS', 'ans.edu.ni');
            $allowedDomainsArray = array_map('trim', explode(',', $allowedDomains));
            $email = $googleUser->getEmail();
            $domain = substr(strrchr($email, "@"), 1);

            if ($allowedDomains !== '*' && !in_array($domain, $allowedDomainsArray)) {
                return redirect('/')->with('error', 'Only accounts from authorized domains are allowed: ' . implode(', ', $allowedDomainsArray));
            }

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]
            );

            // Set default role for brand-new users (no role assigned yet)
            if (! $user->role) {
                $user->role = 'student';
                $user->save();
            }

            Auth::login($user);

            // Store google access token in session for API access (Google Docs export)
            session(['google_access_token' => $googleUser->token]);

            if (session()->has('google_export_redirect')) {
                $redirectUrl = session()->pull('google_export_redirect');
                $queryConnector = strpos($redirectUrl, '?') === false ? '?' : '&';
                return redirect($redirectUrl . $queryConnector . 'export=1');
            }

            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.requests.index');
            }

            return redirect('/')->with('success', 'Logged in successfully.');

        } catch (\Exception $e) {
            Log::error('Google Auth Error: '.$e->getMessage()."\n".$e->getTraceAsString());

            return redirect('/')->with('error', 'Error logging in with Google: '.$e->getMessage());
        }
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }
}
