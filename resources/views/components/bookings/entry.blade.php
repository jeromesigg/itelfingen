<li class="mb-10 ms-6">
    <span class="ring-3 absolute -start-4 flex h-8 w-8 items-center justify-center rounded-full bg-gray-50 ring-white dark:bg-gray-900 dark:ring-gray-900">
        <svg class="h-5 w-5 {{$color}}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{$path}}" />"
        </svg>
    </span>
    <h3 class="mb-0.5 flex items-center pt-1 text-base font-semibold text-gray-900 dark:text-white">{{$title}}</h3>
    @if($time)
        <time class="mb-2 block text-base font-normal text-gray-500 dark:text-gray-400"> {{\Carbon\Carbon::parse($time)->isoFormat('DD.MM.YY')}} </time>   
    @endif
    @if($fulfilled)
        <x-bookings.link :event="$event" :type="$type" :contents="$contents" />
    @endif
</li>