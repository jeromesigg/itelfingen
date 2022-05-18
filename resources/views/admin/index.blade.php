@extends('layouts.admin')

@section('content')

    <!-- Counts Section -->
	<section class="dashboard-counts section-padding">
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
  </section>
  <!-- Updates Section -->
  <section class="mt-30px mb-30px">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-6 col-md-12">
          <!-- Recent Updates Widget          -->
          <div id="new-events" class="card updates recent-updated">
            <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
              <h2 class="h5 display"><a data-toggle="collapse" data-parent="#new-events" href="#events-box" aria-expanded="true" aria-controls="events-box">Die nächsten Buchungen</a></h2>
              <a data-toggle="collapse" data-parent="#new-events" href="#events-box" aria-expanded="true" aria-controls="events-box"><i class="fa fa-angle-down"></i></a>
            </div>
            <div id="events-box" role="tabpanel" class="collapse show">
              <ul class="news list-unstyled">
                <!-- Item-->
                @foreach ($events as $event)
                    <li class="d-flex justify-content-between">
                        <div class="left-col d-flex">
                            <div class="title"><strong>
                                    @if (Auth::user()->isManager())
                                        <a href="{{route('events.edit', $event->id)}}">{{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} - {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}}</a>
                                    @else
                                        {{Carbon\Carbon::parse($event->start_date)->format('d.m.Y')}} - {{Carbon\Carbon::parse($event->end_date)->format('d.m.Y')}}
                                    @endif
                                </strong>
                            <p>{{$event->firstname}} {{$event->name}}@if($event->group_name), {{$event->group_name}}@endif
                                @if($event->comment) - {{$event->comment}}@endif
                                @if($event->telephone) - <a href="tel:{{$event->telephone}}">{{$event->telephone}}</a>@endif
                                </p>
                            </div>
                        </div>
                    </li>
                @endforeach
              </ul>
            </div>
          </div>
          <!-- Recent Updates Widget End-->
        </div>
        <div class="col-lg-6 col-md-12">
          <!-- Daily Feed Widget-->
          <div id="daily-feeds" class="card updates daily-feeds">
            <div id="feeds-header" class="card-header d-flex justify-content-between align-items-center">
              <h2 class="h5 display"><a data-toggle="collapse" data-parent="#daily-feeds" href="#feeds-box" aria-expanded="true" aria-controls="feeds-box">Offene Anfragen</a></h2>
              <div class="right-column">
                <a data-toggle="collapse" data-parent="#daily-feeds" href="#feeds-box" aria-expanded="true" aria-controls="feeds-box"><i class="fa fa-angle-down"></i></a>
              </div>
            </div>
            <div id="feeds-box" role="tabpanel" class="collapse show">
              <div class="feed-box">
                <ul class="feed-elements list-unstyled">
                  <!-- List-->
                    @foreach ($contacts_new as $contact)
                    <li class="clearfix">
                        <div class="feed d-flex justify-content-between">
                        <div class="feed-body d-flex justify-content-between">
                            <div class="content">
                                @if (Auth::user()->isManager())
                                    <a href="{{route('contacts.index')}}"><strong>{{$contact->firstname}} {{$contact->name}}</strong></a><small>{{$contact->subject}}</small>
                                @else
                                    <strong>{{$contact->firstname}} {{$contact->name}}</strong><small>{{$contact->subject}}</small>
                                @endif
                            <div class="full-date"><small>{{$contact->content}} </small></div>
                            </div>
                        </div>
                        <div class="date"><small>{{ \Carbon\Carbon::parse($contact->created_at)->diffForHumans() }} </small></div>
                        </div>
                    </li>
                    @endforeach
                </ul>
              </div>
            </div>
          </div>
          <!-- Daily Feed Widget End-->
        </div>
      </div>
    </div>
  </section>
@endsection

@section('scripts')


@endsection

