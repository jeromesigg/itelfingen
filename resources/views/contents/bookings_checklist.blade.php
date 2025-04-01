@extends('layouts.app')

@section('content')
  @include('includes.header')
  <div id="app">
    <main id="main">
      <section class="breadcrumbs">
        <div class="container">
          <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-4xl font-bold dark:text-white">Checkliste</h2>
            <ol>
              <li><a href="{{route('home')}}">Home</a></li>
              <li>Checkliste</li>
            </ol>
          </div>
        </div>
      </section>
      
      <div class="container max-width-md margin-top-lg margin-bottom-lg">
        <p class="mb-3 text-gray-800 dark:text-gray-400">
          Hier findest du eine Übersicht über die Checkliste für die Hausabgabe des Ferienhauses.
          <br>Bitte klicke auf die einzelnen Punkte, um sie als erledigt zu markieren.
          <br>Die Punkte werden dann grün markiert und du kannst sie jederzeit wieder abwählen falls nötig.
        </p>
        <div id="accordion-arrow-icon" data-accordion="collapse" class="container max-width-md margin-top-lg margin-bottom-lg" data-active-classes="bg-white dark:bg-gray-900 text-gray-900 dark:text-white" data-inactive-classes="text-gray-500 dark:text-gray-400">
            @foreach ($event->event_rooms as $event_room)
              <div id="id-{{$event_room->id}}">
                <h3 id="accordion-arrow-icon-heading-{{$event_room->id}}">
                  <button type="button" class="flex justify-between w-full p-5 text-3xl font-bold rtl:text-right text-gray-800 border border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3" data-accordion-target="#accordion-arrow-icon-body-{{$event_room->id}}" aria-expanded="false" aria-controls="accordion-arrow-icon-body-{{$event_room->id}}">
                    <span id="room_done_{{$event_room->id}}">
                      @if($event_room->done)
                        <i class="fa-regular fa-circle-check" style="color:darkseagreen"></i>
                      @endif
                    </span>
                    <span>{{$event_room->room->name}}</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                  </button>
                </h3>
                <div id="accordion-arrow-icon-body-{{$event_room->id}}" class="hidden" aria-labelledby="accordion-arrow-icon-heading-{{$event_room->id}}">
                  <div class="p-5 border border-t-0 border-gray-200 dark:border-gray-700">
                    <ul class="grid w-full gap-6 md:grid-cols-4">
                      @if($event_room->event_checkpoints->count() > 0)
                          @foreach ($event_room->event_checkpoints as $event_checkpoint)
                            <li>
                                <input type="checkbox" id="{{$event_checkpoint['id']}}" value="{{$event_checkpoint['done']}}" class="hidden peer"  @if($event_checkpoint->done) checked @endif>
                                <label for="{{$event_checkpoint['id']}}" class="inline-flex items-center justify-between w-full p-5 text-gray-800 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 peer-checked:border-blue-600 dark:peer-checked:border-blue-600 hover:text-gray-600 dark:peer-checked:text-gray-300 peer-checked:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">                           
                                    <div class="block">
                                        <div class="w-full text-lg font-semibold">
                                          <span id="checkpoint_done_{{$event_checkpoint->id}}">
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
                    </ul> 
                  </div> 
                </div> 
              </div> 
            @endforeach
        </div>
      </div>
    </main>
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
                  var checkpoint = document.getElementById('checkpoint_done_' + checkpoint_id);
                  if(data.data.checkpoint_done){
                    checkpoint.innerHTML = '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i>';
                  }
                  else{
                    checkpoint.innerHTML = '';
                  }
                  var room = document.getElementById('room_done_' + data.data.room_id);
                  if(data.data.room_done){
                    room.innerHTML = '<i class="fa-regular fa-circle-check" style="color:darkseagreen"></i>';
                  }
                  else{
                    room.innerHTML = '';
                  }
                }
              }
          });
      });
  });
</script>
@endpush