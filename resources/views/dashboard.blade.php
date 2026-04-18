<!--هاي الاي اوت الي بتعمل الديزاين الجاهز تبع الداش بوورد-->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="bg-black shadow-lg rounded-lg p-6 max-w-7xl mx-auto">
            
            <h3 class="text-white text-2xl font-bold mb-6">
                {{ __('Welcome back,') }} {{ Auth::user()->name }}
            </h3>

            <div class="flex items-center justify-between gap-4">
                <!--خليناها جيت لانو بدي اياها تروح ك كويري سترينج والكويري هاي رح يكون اسمها سيرتش-->
                <form action="{{ route('dashboard') }}" method="get"  class="flex items-center w-1/4">
                  
                    <input type="text" value="{{ request('search') }}" name="search" class="w-full bg-gray-800 text-white p-2 rounded-l-lg" placeholder="Search for a job"/>
                    <button type="submit" class="bg-indigo-500 text-white p-2 rounded-r-lg border border-indigo-500">Search</button>
                    @if(request('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif
                @if(request('search'))
                <a href="{{ route('dashboard',['search'=>request('filter')]) }}" class="bg-red-500 text-white p-2 rounded-lg text-sm">Clear</a>

                @endif
                </form>

                <div class="flex space-x-2">
                    <!--هيك لم تضغك على اللينك رح يروح على الداش ورح يبعث في الباراميتر او الريكويست تاع الكويري سترينج فلتر اسمو فل تايم وهكذا-->
                    <!--والفل تايم وهاي الامور لم تبعثهم في الباراميتر او الريكويست لازم تبعثهم نفس ما هم موجودين في الداتا بيس بالضبط-->
                    <!--لنفترض انا بدي ابحث عن فل ستاك وبنفس الوقت يكون فل تايم فعشان هيك انا عملت باراميتر ثاتي ابعثلو فيه  السيرتش الي جاي معاي في الفلتر-->
                    <a href="{{ route('dashboard',['filter' => 'Full-Time','search' => request('search')]) }}" class="bg-indigo-500 text-white p-2 rounded-lg text-sm">Full-Time</a>
                    <a href="{{ route('dashboard',['filter' => 'Remote','search' => request('search')]) }}" class="bg-indigo-500 text-white p-2 rounded-lg text-sm">Remote</a>
                    <a href="{{ route('dashboard',['filter' => 'Hybrid','search' => request('search')]) }}" class="bg-indigo-500 text-white p-2 rounded-lg text-sm">Hybrid</a>
                    <a href="{{ route('dashboard',['filter'=>'Contract','search' => request('search')]) }}" class="bg-indigo-500 text-white p-2 rounded-lg text-sm">Contract</a>

                    @if(request('filter'))
                    <!--انو لو حب يعمل كلير بس للفاتر ما يروح يعمل كلير للسيرتش عشان هيك حطينا هاذ الباراميتر-->
                    <a href="{{ route('dashboard',['search'=>request('search')]) }}" class="bg-red-500 text-white p-2 rounded-lg text-sm">Clear</a>
                    @endif

              
                </div>
            </div>
            <!--Job List-->
             <div class="space-y-4 mt-10">
                @forelse ($jobs as $job )
                    
               
                  <!--Job Item-->

                <div class="border-b border-white/10 pb-4 flex justify-between items-center">
                    <div>
                        <a href="{{ route('job-vacancies.show',$job->id) }}" class="text-lg font-semibold text-blue-400 hover:underline">{{ $job->title }}</a>
                        <p class="text-sm text-white">{{ $job->company->name }} - {{ $job->location }}</p>
                        <p class="text-sm text-white">{{'$'.number_format($job->salary)}} / Year</p>
                    </div>
                    <span class="bg-blue-500 text-white p-2 rounded-lg">{{ $job->type }}</span>
                </div>
                 @empty
                 <p class="text-white text-2xl font-bold">No jobs found!</p>

                 @endforelse
                </div> 
                <div class="mt-6">
                {{ $jobs->links() }}
                </div>
            </div>
    </div>
    </x-app-layout>
