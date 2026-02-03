<div class="col-span-full my-4 space-y-4 md:mb-8">
    <h2 class="mb-4 text-xl text-gray-500 dark:text-gray-400">{{$title}}</h2>
    @foreach ($events as $event)
        <div class="relative flex items-center justify-between rounded-lg bg-white p-4 shadow dark:bg-gray-800">
            <div class="me-8 flex items-start sm:items-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <p class="mb-1 text-sm  sm:text-base">                                
                        <a class="text-orientalpink text-bold" href="{{Auth::user()->isManager() ? route('admin.events.edit', $event->id) : '#'}}">{{$event->number()}}, {{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} - {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}}:</a>
                        {{$event->firstname}} {{$event->name}} 
                        {{$event->group_name ? ', ' . $event->group_name : ''}}
                        {{$event->comment ? ' - ' . $event->comment : ''}}
                        @if($event->telephone) - <a class="text-orientalpink" href="tel:{{$event->telephone}}">{{$event->telephone}}</a>@endif
                    </p>
                    <div class="flex items-center text-xs sm:text-sm">
                        <svg class="me-1 h-3 w-3 sm:h-3.5 sm:w-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm11-4a1 1 0 1 0-2 0v4c0 .3.1.5.3.7l3 3a1 1 0 0 0 1.4-1.4L13 11.6V8Z" clip-rule="evenodd" />
                        </svg>
                        {{Carbon\Carbon::parse($event->created_at)->diffForHumans()}},  {{$event->user ? $event->user->username : 'Kein Benutzer zugewiesen'}}
                    </div>
                </div>
            </div>
            <div>
                <div class="text-left absolute end-1 top-1 rounded-md textalign-left p-1 text-sm font-medium text-gray-500 dark:text-gray-400 sm:relative sm:end-0 sm:top-0">
                    {!! $event->status() !!}
                </div>
            </div>
        </div>
    @endforeach
  </div>
  