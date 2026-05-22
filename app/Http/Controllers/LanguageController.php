<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        if (in_array($locale, ['en', 'es'])) {
            if (Auth::check()) {
                $user = Auth::user();
                $user->locale = $locale;
                $user->save();
            } else {
                Session::put('locale', $locale);
            }
            App::setLocale($locale);
        }

        return redirect()->back();
    }
}
