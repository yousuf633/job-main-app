<?php

namespace App\Http\Controllers;
use OpenAI\Laravel\Facades\OpenAI;
use App\Services\ResumeAnalysisService;

use Illuminate\Http\Request;
use App\Models\JobVacancy;
use App\Models\Resume;
use App\Http\Requests\ApplyJobRequest;
use App\Models\JobApplication;

class JobVacancyController extends Controller
{
     //This is the variable where I want to put the instance
    protected $resumeAnalysisService;
    public function __construct(ResumeAnalysisService $resumeAnalysisService)
    {
       $this->resumeAnalysisService=$resumeAnalysisService;
    }
    public function show(string $id)
    {
        $jobVacancy=JobVacancy::findOrFail($id);
        return view('job-vacancies.show',compact('jobVacancy'));

    }
     public function apply(string $id)
    {
        $jobVacancy=JobVacancy::findOrFail($id);
        $resumes=auth()->user()->resumes;
        return view('job-vacancies.apply',compact('jobVacancy','resumes'));

    }
    // proccessing Application
    //upload cv on cloud when click apply button
   public function processApplication(ApplyJobRequest $request, string $id)
{
    $jobVacancy=JobVacancy::findOrFail($id);
    if ($request->input('resume_option') === 'new_resume') {
        $resumeId=null;
        $extractedInfo=null;

        $file = $request->file('resume_file');

        $extension = $file->getClientOriginalExtension();
        $originalFileName = $file->getClientOriginalName();
        $fileName = 'resume_' . time() . '.' . $extension;

        //Store in laravel cloud
        $path = $file->storeAs('resumes', $fileName, 'cloud');
        $fileUrl=config('filesystems.disks.cloud.url') . '/' .$path;
       
        //TODO:Extract information from the resume
        $extractedInfo=$this->resumeAnalysisService->extractResumeInformation($fileUrl);
        
        $resume = Resume::create([
            'filename' => $originalFileName,
            'fileUri' => $path,
            'userId' => auth()->id(),
            'contactDetails' => json_encode([
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ]),
            'summary' => $extractedInfo['summary'],
            'skills' => $extractedInfo['skills'],
            'experience' => $extractedInfo['experience'],
            'education' => $extractedInfo['education'],
        ]);
        //after create the resume I want set the resume in $resumeId because will set in JobApplication in resumeId here
        
          $resumeId=$resume->id;

    } else {

        // existing resume
        $resumeId=$request->input('resume_option');
        //Give me th resume now
        $resume = Resume::findOrFail($resumeId);
        $extractedInfo=[
            'summary'=>$resume->summary,
            'skills'=>$resume->skills,
            'experience'=>$resume->experience,
            'education'=>$resume->education,
        ];
    }

    //TODO: Evaluate Job Application
    $evaluation=$this->resumeAnalysisService->analyzeResume($jobVacancy,$extractedInfo);
    //Use the $extractedInfo to evaluate the job application
     JobApplication::create([
        'status' => 'pending',
        'jobVacancyId' => $id,
        'resumeId' => $resumeId,
        'userId' => auth()->id(),
        'aiGeneratedScore' => $evaluation['aiGeneratedScore'],
        'aiGeneratedFeedback' => is_array($evaluation['aiGeneratedFeedback']) 
        ? json_encode($evaluation['aiGeneratedFeedback'], JSON_UNESCAPED_UNICODE) 
        : ($evaluation['aiGeneratedFeedback'] ?? ''),
    ]);


    return redirect()->route('job-applications.index', $id)->with('success', 'Application submitted successfully!');
}
}