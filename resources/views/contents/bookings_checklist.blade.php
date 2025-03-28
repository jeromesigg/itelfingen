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
                  <a class="cd-faq__category cd-faq__category truncate" href="#id-{{$event_room->id}}">
                    {{$event_room->room->name}}
                  </a>
              </li>
            @endforeach
          </ul> <!-- cd-faq__categories -->
  
          <div class="cd-faq__items">
            @foreach ($event->event_rooms as $event_room)
            <h3  class="mb-5 text-lg font-medium text-gray-900 dark:text-white">{{$event_room->room->name}}</h3>
              <ul id="id-{{$event_room->id}}" class="cd-faq__group grid w-full gap-6 md:grid-cols-3">
                @if($event_room->event_checkpoints->count() > 0)
                    @foreach ($event_room->event_checkpoints as $event_checkpoint)
                      <li>
                          <input type="checkbox" id="{{$event_checkpoint['id']}}" value="{{$event_checkpoint['done']}}" class="hidden peer" required="">
                          <label for="{{$event_checkpoint['id']}}" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 peer-checked:border-blue-600 dark:peer-checked:border-blue-600 hover:text-gray-600 dark:peer-checked:text-gray-300 peer-checked:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">                           
                              <div class="block">
                                  <div class="w-full text-lg font-semibold">
                                    <span id="done">
                                      @if($event_checkpoint->done)
                                        <i class="fa-regular fa-circle-check" style="color:darkseagreen"></i>
                                      @endif
                                    </span>
                                    {{$event_checkpoint->checkpoint->name}}
                                  </div>
                                  <div class="w-full text-sm">{!! $event_checkpoint->checkpoint->description !!}</div>
                              </div>
                          </label>
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

@push('scripts')
<script type="module">
  $(document).ready(function(){
    $("input:checkbox").change(function() {
      var checkpoint_id = $(this).attr('id');
      $.ajax({
              type:'POST',
              url: "{!! route('bookings.checkpointDone') !!}",
              headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
              data: { "checkpoint_id" : checkpoint_id },
              success: function(data){
                if(data.data.success){
                  //do something
                }
              }
          });
      });
  });
</script>
@endpush