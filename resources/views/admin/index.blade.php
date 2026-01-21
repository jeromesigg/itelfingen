@extends('layouts.admin')

@section('content')

    <!-- Counts Section -->
	{{-- <section class="dashboard-counts section-padding">
    <div class="container-fluid">
      <div class="row">
        @foreach ($icon_array as $icon)
          <div class="col-xl-4 col-md-6 col-6">
            <div class="wrapper count-title d-flex">
              <div class="icon"><i class={{$icon->icon}}></i></div>
              <div class="name"><strong class="text-uppercase">{{$icon->name}}</strong>
                <div class="count-number">{{$icon->number}}</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section> --}}
  <div class="my-4 grid gap-4 grid-cols-2 2xl:grid-cols-3">
    @foreach ($icon_array as $icon)
      <div class="flex flex-col p-4 md:p-6 xl:p-8 space-x-0 text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 dark:bg-gray-800 dark:text-white xl:p-8 sm:space-x-4 ">
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg {{$icon->color}} sm:mb-0">
          {!! $icon->icon !!}
        </div>
        <div>
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{$icon->number}}</h2>
          <p class="text-gray-500 dark:text-gray-400">{{$icon->name}}</p>
        </div>
      </div>
    @endforeach
  </div>
  <!-- Updates Section -->
   
  <div class="my-4 grid gap-4 grid-cols-2 2xl:grid-cols-3">
    <!-- Pricing Card -->
    {{-- <div class="flex flex-col p-6 mx-auto max-w-2xl text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white"> --}}
      <div class="flex flex-col p-4 md:p-6 xl:p-8 space-x-0 text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 dark:bg-gray-800 dark:text-white xl:p-8 sm:space-x-4 ">
      <h3 class="mb-4 text-2xl font-semibold">Neue Buchungen</h3>
      <div class="items-baseline my-8">
        <ul class="news list-unstyled">
          <!-- Item-->
          @foreach ($events as $event)
              <li class="justify-content-between">
                  <div class="left-col row">
                      <div class="title col-xl-6">
                          <strong>
                              @if (Auth::user()->isManager())
                                  <a class="text-orientalpink" href="{{route('admin.events.edit', $event->id)}}">{{$event->number()}} <br> {{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} - {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}}</a>
                              @else
                                  {{$event->number()}}: {{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} - {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}}
                              @endif
                          </strong>
                          <p>
                              {{$event->firstname}} {{$event->name}}@if($event->group_name), {{$event->group_name}}@endif
                              @if($event->comment) - {{$event->comment}}@endif
                              @if($event->telephone) - <a href="tel:{{$event->telephone}}">{{$event->telephone}}</a>@endif
                          </p>
                      </div>
                      <div class="title col-xl-6">
                          {!! $event->status() !!}
                      </div>
                  </div>
              </li>
              <hr class="h-px my-3 bg-gray-600 dark:bg-gray-300 border-0">
          @endforeach
        </ul>
      </div>

    </div>
    <!-- Pricing Card -->
    
    {{-- <x-event-dashboard :events="$events_current" title="Neue Buchungen"/> --}}
    <div class="flex flex-col p-4 md:p-6 xl:p-8 space-x-0 text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 dark:bg-gray-800 dark:text-white xl:p-8 sm:space-x-4 ">
      <h3 class="mb-4 text-2xl font-semibold">Nächste Buchungen</h3>
      <div class="flex justify-center items-baseline my-8">
        <ul class="news list-unstyled">
          <!-- Item-->
          @foreach ($events_current as $event)
              <li class="justify-content-between">
                  <div class="left-col row">
                      <div class="title col-xl-6">
                          <strong>
                              @if (Auth::user()->isManager())
                                  <a class="text-orientalpink"  href="{{route('admin.events.edit', $event->id)}}">{{$event->number()}} <br> {{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} - {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}}</a>
                              @else
                                  {{$event->number()}}: {{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} - {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}}
                              @endif
                          </strong>
                          <p>
                              {{$event->firstname}} {{$event->name}}@if($event->group_name), {{$event->group_name}}@endif
                              @if($event->comment) - {{$event->comment}}@endif
                              @if($event->telephone) - <a href="tel:{{$event->telephone}}">{{$event->telephone}}</a>@endif
                          </p>
                      </div>
                      <div class="title col-xl-6">
                          {!! $event->status() !!}
                      </div>
                  </div>
              </li>
              <hr class="h-px my-3 bg-gray-600 dark:bg-gray-300 border-0">
          @endforeach
        </ul>
      </div>

    </div>
    <!-- Pricing Card -->
    <div class="flex flex-col p-4 md:p-6 xl:p-8 space-x-0 text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 dark:bg-gray-800 dark:text-white xl:p-8 sm:space-x-4 ">
      <h3 class="mb-4 text-2xl font-semibold">Offene Anfragen</h3>
      <div class="flex justify-center items-baseline my-8">
        <ul class="news list-unstyled">
          <!-- Item-->
          @foreach ($contacts_new as $contact)
          <li class="clearfix">
              <div class="feed d-flex justify-content-between">
              <div class="feed-body d-flex justify-content-between">
                  <div class="content">
                      @if (Auth::user()->isManager())
                          <a class="text-orientalpink" href="{{route('admin.contacts.index')}}"><strong>{{$contact->firstname}} {{$contact->name}}</strong> </a><br><small>{{$contact->subject}}</small>
                      @else
                          <strong>{{$contact->firstname}} {{$contact->name}}</strong><br><small>{{$contact->subject}}</small>
                      @endif
                  <div class="full-date"><small>{{$contact->content}} </small></div>
                  </div>
              </div>
              <div class="date"><small>{{ \Carbon\Carbon::parse($contact->created_at)->diffForHumans() }} </small></div>
              </div>
          </li>
              <hr class="h-px my-3 bg-gray-600 dark:bg-gray-300 border-0">
          @endforeach
        </ul>
      </div>

    </div>
  </div>
  @endsection