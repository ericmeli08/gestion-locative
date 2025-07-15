<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->input('language');
        
        if (in_array($locale, config('app.available_locales', ['fr', 'en']))) {
            session(['locale' => $locale]);
        }

        return back();
    }
}