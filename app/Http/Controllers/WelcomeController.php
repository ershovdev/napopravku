<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    public function index(Request $request) {
        if (Auth::user()) return redirect()->route('folders.root.show');
        return view('welcome');
    }
}
