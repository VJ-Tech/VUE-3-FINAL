<?php

namespace App\Http\Controllers;

use App\Models\Easy;
use Illuminate\Http\Request;

class EasyController extends Controller
{
    public function index() {
        $easy = Easy::inRandomOrder()->first();

        return response()->json(['content' =>$easy->content]);
    }
}
