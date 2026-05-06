<?php
//This is the file will useing for Extraction and Evaluation the text 
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ResumeAnalysisService
{
    
    public function extractResumeInformation(string $fileUrl)
{
    try{
        //Extract raw text from the resume pdf file(read pdf file,and get the text)
        $rawText = $this->extractTextFrompPdf($fileUrl);

        //I'm recording this information just so I (the programmer) can make sure the code works.
        Log::debug('Successfully extracted text from pdf file: ' . strlen($rawText) . ' characters');

        //Use Grok to organize the text into a structured format
       $response = Http::withHeaders([
       'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
       'Content-Type' => 'application/json',
       ])->post('https://api.groq.com/openai/v1/chat/completions', [
         'model' => 'llama-3.1-8b-instant',
         'messages' => [
        [
            'role' => 'system',
            'content' => 'You are a precise resume parser. Return ONLY a JSON object.',
        ],
        [
            'role' => 'user',
            'content' => "Parse this resume: {$rawText}. Use keys: 'summary','skills','experience','education'.",
        ],
    ],
         'response_format' => ['type' => 'json_object'],
         'temperature' => 0.1,
]);

// التعديل: فحص حالة الاستجابة قبل محاولة قراءة البيانات لتجنب الأخطاء
if ($response->failed()) {
    Log::error('Groq API Error: ' . $response->body());
    throw new \Exception('Failed to analyze resume via Groq');
}

$result = $response->json()['choices'][0]['message']['content'];
Log::debug('Grok response:'.$result);
$parsedResult=json_decode($result,true);

//Validation the parsed result
$requiredKeys=['summary','skills','experience','education'];
$missingKeys=array_diff($requiredKeys,array_keys($parsedResult));

//Return the JSON object
return[
    'summary' => is_array($parsedResult['summary']) 
                 ? json_encode($parsedResult['summary'], JSON_UNESCAPED_UNICODE) 
                 : ($parsedResult['summary'] ?? ''),
    'skills'=>$parsedResult['skills'] ?? [],
    'experience'=>$parsedResult['experience'] ?? [],
    'education'=>$parsedResult['education'] ?? [],

];
    

} catch (\Exception $e) {

   Log::error('Error extracting resume information: '.$e->getMessage());
   return[
    'summary'=>'',
    'skills'=>[],
    'experience'=>[],
    'education'=>[],

   ];
    
}
}
public function analyzeResume($jobVacancy,$resumeData)
{
    try{
        $jobDetails=json_encode([
            'job_title'=>$jobVacancy->title,
            'job_description'=>$jobVacancy->description,
            'job_location'=>$jobVacancy->location,
            'job_type'=>$jobVacancy->type,
            'job_salary'=>$jobVacancy->salary,
        ]);
        //$resumeData is  extractResumeInformation function
        $resumeDetails=json_encode($resumeData);
        
        //Use Grok to organize return feedback
       $response = Http::withHeaders([
       'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
       'Content-Type' => 'application/json',
       ])->post('https://api.groq.com/openai/v1/chat/completions', [
         'model' => 'llama-3.1-8b-instant',
         'messages' => [
        [
            'role' => 'system',
            'content' => "You are an expert HR professional and job recruiter.
            You are given a job vacancy and a resume.
            Your task is to analyze the resume and determine if the candidate is a good fit for the job,
            The output should be in JSON format.Provide a score from 0 to 100 for the candidate's
            suitability for the job,and detailed feedback.Response should be only Json has the
            following keys:'aiGeneratedScore','aiGeneratedFeedback'.Aigenerate feedback should be
            detailed and specific to the job and the candidate's resume.
             ",
        ],
         [
        'role'=>'user',
        'content'=>"Please evaluate this job applications.Job Details:{$jobDetails}.Resume Details: {$resumeDetails}"
         ],
         ],
         'response_format'=>[
            'type'=>'json_object'
         ],
         'temperature'=>0.1
     
]);

$result = $response->json()['choices'][0]['message']['content'];
Log::debug('GrokAI evaluationresponse: '.$result);
$parsedResult=json_decode($result,true);
if(json_last_error()!== JSON_ERROR_NONE)
    {
        log::error('Failed to parse GrokAI response: '.json_last_error_msg());
        throw new \Exception('Failed to parse GrokAI response');
    }
if(!isset($parsedResult['aiGeneratedScore']) || !isset($parsedResult['aiGeneratedFeedback']))
    {
        Log::error('Missing required keys in the parsed result');
        throw new \Exception('Missing required keys in the parsed result');
    }
    return $parsedResult;
}
catch(\Exception $e)
{
    Log::error('Error analyze resume: '.$e->getMessage());
    return [
        'aiGeneratedScore'=>0,
        'aiGeneratedFeedback'=>'An error occurred while analyzing the resume.Please try again later.'

    ];

}

    
}

    private function extractTextFrompPdf(string $fileUrl): string
    {
        //Reading the file from the cloud to local disk storage in temp file
        //$fileUrl is exist on cloud
        //Preparing the temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'resume');

        //Extract file path and prepare
        $filePath = parse_url($fileUrl, PHP_URL_PATH);
        if (!$filePath) {
            throw new \Exception('File not found');
        }
        $filename = basename($filePath);

        //fetch the file from cloud
        $storagePath = "resumes/{$filename}";
        if (!Storage::disk('cloud')->exists($storagePath)) {
            throw new \Exception('File not found');
        }
        $pdfContent = Storage::disk('cloud')->get($storagePath);
        if (!$pdfContent) {
            throw new \Exception('Failed to read file');
        }
        file_put_contents($tempFile, $pdfContent);
        $pdfToTextPaths = 'C:\poppler\poppler-23.07.0\Library\bin\pdftotext.exe';


        $pdfToTextAvailable = false;
        if (file_exists($pdfToTextPaths)) {
            $pdfToTextAvailable = true;
        }
        if (!$pdfToTextAvailable) {
            throw new \Exception('pdf-to-text is not installed');
        }

        // Extract text from the pdf file

        //prepare copy from spatie library to read the file
        $instance = new PDF($pdfToTextPaths);
        //Select the file you want to read
        $instance->setPdf($tempFile);
        //Extract text from pdf
        $text = $instance->text();

        //Clean up the temp file
        //Delete the temporary file from the hard disk
        unlink($tempFile);
        log::info('AI Extracted Successfully');
        return $text;
    }
}