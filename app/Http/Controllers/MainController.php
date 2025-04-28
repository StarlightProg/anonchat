<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(){
        $online = 5;

        return view('main', ['online' => $online]);
    }
}
