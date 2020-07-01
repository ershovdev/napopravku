<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Show main page
     *
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function index(Request $request) {
        if (Auth::user()) return redirect()->route('folders.root.show');
        return view('welcome');
    }
}
