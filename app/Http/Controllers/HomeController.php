<?php

namespace App\Http\Controllers;

use App\Models\Berita;

class HomeController extends Controller
{
    public function index()
    {
        $beritas = Berita::latest()->paginate(4);
        return view('home', compact('beritas'));
    }
}