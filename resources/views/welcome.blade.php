<x-main-layout title="Shagalni - Find your dream job">
    <div x-data="{show: false}" x-init="$nextTick(() => show = true)">
        <div x-cloak x-show="show"
             x-transition:enter="ease-out duration-1000"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             class="inline-flex items-center mb-2">
            <h4 class="text-sm text-white/60 rounded-full bg-white-10 px-3 py-1">
                Shagalni
            </h4>
        </div>
    </div>

    <div x-data="{show: false}" x-init="$nextTick(() => show = true)">
        <div x-cloak x-show="show"
             x-transition:enter="ease-out duration-1000"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             class="inline-flex items-center mb-2">
            <h1 class="text-4xl sm:text-6xl md:text-8xl font-bold mb-6 tracking-tight">
                <span class="text-white">Find your</span><br />
                <span class="text-white/60 font-serif italic">Dream Job</span>
            </h1>
        </div>
    </div>
    <div x-data="{show: false}" x-init="$nextTick(() => show = true)">
        <div x-cloak x-show="show"
             x-transition:enter="ease-out duration-1000"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             class="inline-flex items-center mb-2">
           <p class="text-white/50 text-lg">connect with top employers,and find exciting opportunities</p>
        </div>
    </div>
     <div x-data="{show: false}" x-init="$nextTick(() => show = true)">
        <div x-cloak x-show="show"
             x-transition:enter="ease-out duration-1000"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             class="inline-flex items-center mb-2">
         <a href="{{ route('register') }}" class="rounded-lg mr-1 bg-white/10 px-4 py-2 text-white">Create an Account</a>
         <a href="{{ route('login') }}" class="rounded-lg bg-gradient-to-r from-indigo-500 to-rose-500 px-4 py-2 text-white">Login</a>
        </div>
    </div>

</x-main-layout>