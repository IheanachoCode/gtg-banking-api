<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function apiKeyGuide()
    {
        return view('api-key-guide');
    }
}
