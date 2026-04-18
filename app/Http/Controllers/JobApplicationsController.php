<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobApplicationsController extends Controller
{
    public function index()
    {
        //for display my applications
        return view('job-applications.index');
    }
}
