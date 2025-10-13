<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index() {
        return view('frontend.tasks.index');
    }

    public function create() {

    }


}
