<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Character;

class HomeController extends Controller
{
    public function index()
    {
        $characters = Character::all();
        return view('home', compact('characters'));
    }
}
