<li class="mb-10 ms-6">
    <span class="ring-3 absolute -start-4 flex h-8 w-8 items-center justify-center rounded-full bg-gray-50 ring-white dark:bg-gray-900 dark:ring-gray-900">
        <svg class="h-5 w-5 {{$color}}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{$path}}" />"
        </svg>
    </span>
    <h3 class="mb-0.5 flex items-center pt-1 text-base font-semibold text-gray-900 dark:text-white">{{$title}}</h3>
    @foreach ($event->notifications->sortBy('created_at') as $notification)
        @if($notification->type === $notification_type)
            <time class="mb-2 block text-base font-normal text-gray-500 dark:text-gray-400"> {{\Carbon\Carbon::parse($notification->created_at)->isoFormat('DD.MM.YY')}} </time>
        @endif
    @endforeach     
    @if($fulfilled)
        <x-bookings.link :event="$event" :type="$type" :contents="$contents" />
        {{-- <a href="#" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:text-primary-700 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
            <svg class="-ms-0.5 me-1.5 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
            <path
                fill-rule="evenodd"
                d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
                clip-rule="evenodd"
            />
            </svg>
            FAQ ansehen
        </a> --}}
    @endif
</li>