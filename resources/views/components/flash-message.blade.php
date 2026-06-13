@if (session()->has('flash_notification'))
    @foreach (session('flash_notification')->all() as $notification)
        <div class="mb-8 p-8 bg-white rounded-[2.5rem] border {{ $notification->level == 'danger' ? 'border-red-100' : 'border-green-100' }} flex flex-col items-center justify-center text-center shadow-sm transition-all duration-500" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <h4 class="text-xl font-black text-gray-900 mb-4 uppercase tracking-tighter">{{ $notification->message }}</h4>
            <div class="w-14 h-14 {{ $notification->level == 'danger' ? 'bg-red-500 shadow-red-100' : 'bg-green-500 shadow-green-100' }} rounded-full flex items-center justify-center shadow-xl animate-pulse"
                 style="{{ $notification->level == 'danger' ? 'background-color: #ef4444;' : 'background-color: #22c55e;' }}">
                @if($notification->level == 'danger')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                    </svg>
                @endif
            </div>
        </div>
    @endforeach
@endif

@if (session()->has('error'))
    <div class="mb-8 p-8 bg-white rounded-[2.5rem] border border-red-100 flex flex-col items-center justify-center text-center shadow-sm transition-all duration-500" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <h4 class="text-xl font-black text-gray-900 mb-4 uppercase tracking-tighter">{{ session('error') }}</h4>
        <div class="w-14 h-14 bg-red-500 rounded-full flex items-center justify-center shadow-xl shadow-red-100 animate-pulse" style="background-color: #ef4444;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
    </div>
@endif

@if (session()->has('success'))
    <div class="mb-8 p-8 bg-white rounded-[2.5rem] border border-green-100 flex flex-col items-center justify-center text-center shadow-sm transition-all duration-500" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <h4 class="text-xl font-black text-gray-900 mb-4 uppercase tracking-tighter">{{ session('success') }}</h4>
        <div class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center shadow-xl shadow-green-100 animate-pulse" style="background-color: #22c55e;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
            </svg>
        </div>
    </div>
@endif
