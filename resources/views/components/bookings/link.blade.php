@foreach($contents as $content)
    <a href="{{$content['link']}}" {{$content['link'] <> '#' ? 'target="_blank"' : ''}} class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 my-1 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:text-primary-700 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
        <svg class="-ms-0.5 me-1.5 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{$content['path'] ?? "M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"}}" />
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{isset($content['path']) ? '' : "M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"}}" />
        </svg>
        {{$content['text']}}
    </a>
    <br>
@endforeach