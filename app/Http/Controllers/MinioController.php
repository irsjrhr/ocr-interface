<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MinioController extends Controller
{
    public function index()
    {
        return view('minio.index');
    }
}
