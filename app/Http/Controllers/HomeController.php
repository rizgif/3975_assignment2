<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Assuming you have a User model for DB operations

class HomeController extends Controller
{
    public function index()
    {
        // Adapt your PHP logic here. For example:
        // $user = auth()->user(); // Gets the authenticated user
        // return view('index', ['user' => $user]);

        return view('index', [
            'user' => auth()->user(),
        ]);
    }
}
