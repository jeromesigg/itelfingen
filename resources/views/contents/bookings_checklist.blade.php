@extends('layouts.app')

@section('content')
    @include('includes.header')
    <div id="app"><main id="main">
        <section class="breadcrumbs">
          <div class="container">
            <div class="d-flex justify-content-between align-items-center">
              <h2>Checkliste</h2>
              <ol>
                <li><a href="{{route('home')}}">Home</a></li>
                <li>Checkliste</li>
              </ol>
            </div>
          </div>
        </section>
  
        <div class="cd-faq js-cd-faq container max-width-md margin-top-lg margin-bottom-lg">
          <ul class="cd-faq__categories">
            @foreach ($event->event_rooms as $event_room)
              <li>
                  <a class="cd-faq__category cd-faq__category truncate" href="#{{$event_room->room->name}}">
                    {{$event_room->room->name}}
                  </a>
              </li>
            @endforeach
          </ul> <!-- cd-faq__categories -->
  
          <div class="cd-faq__items">
            @foreach ($event->event_rooms as $event_room)
              <ul id=" {{$event_room->room->name}}" class="cd-faq__group mb-4">
  
                <div class="cd-faq__title">
                    <h2 class="cd-faq__title-text">
                        {{$event_room->room->name}}
                    </h2>
                </div>
                @if($event_room->event_checkpoints->count() > 0)
                    @foreach ($event_room->event_checkpoints as $event_checkpoint)
                    <li class="cd-faq__item">
                        <a class="cd-faq__trigger" href="#0"><span>{{$event_checkpoint->checkpoint->name}}</span></a>
                        <div class="cd-faq__content pt-3 border border-norway">
                        <div class="text-component">
                            <div class="row">
                                <div class="col-lg-12 order-1 order-lg-1">
                                <p>{!! $event_checkpoint->checkpoint->description !!}</p>
                                </div>
                            </div>
                        </div>
                        </div> <!-- cd-faq__content -->
                    </li>
                    @endforeach
                @endif
              </ul> <!-- cd-faq__group -->
            @endforeach
          </div> <!-- cd-faq__items -->
  
          <a href="#0" class="cd-faq__close-panel text-replace">Schliessen</a>
  
          <div class="cd-faq__overlay" aria-hidden="true"></div>
        </div> <!-- cd-faq -->
  
      </main><!-- End #main -->
        @include('includes.footer')
    </div>
@endsection
