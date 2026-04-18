<?php

namespace App\Http\Controllers;
use OpenAI\Laravel\Facades\OpenAI;

use Illuminate\Http\Request;
use App\Models\JobVacancy;

class JobVacancyController extends Controller
{
    public function show(string $id)
    {
        $jobVacancy=JobVacancy::findOrFail($id);
        return view('job-vacancies.show',compact('jobVacancy'));

    }
     public function apply(string $id)
    {
        $jobVacancy=JobVacancy::findOrFail($id);
        return view('job-vacancies.apply',compact('jobVacancy'));

    }
    // proccessing Application
    public function processApplication(Request $request, string $id)
    {

    }
   
}
