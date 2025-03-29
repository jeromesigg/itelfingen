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
  
        <div class="container max-width-md margin-top-lg margin-bottom-lg">
          {{-- <ul class="cd-faq__categories">
            @foreach ($event->event_rooms as $event_room)
              <li>
                  <a class="cd-faq__category cd-faq__category truncate" href="#id-{{$event_room->id}}">
                    {{$event_room->room->name}}
                  </a>
              </li>
            @endforeach
          </ul> <!-- cd-faq__categories --> --}}
  
          {{-- <div class="cd-faq__items"> --}}
            @foreach ($event->event_rooms as $event_room)
              <div id="id-{{$event_room->id}}">
                <h2 class="text-4xl font-bold dark:text-white mb-2 mt-6 items-center justify-between border-2 border-gray-200 rounded-lg p-2">
                  <span id="room_done_{{$event_room->id}}">
                    @if($event_room->done)
                      <i class="fa-regular fa-circle-check" style="color:darkseagreen"></i>
                    @endif
                  </span>
                  {{$event_room->room->name}}
                </h2>
                <ul class="grid w-full gap-6 md:grid-cols-4">
                  @if($event_room->event_checkpoints->count() > 0)
                      @foreach ($event_room->event_checkpoints as $event_checkpoint)
                        <li>
                            <input type="checkbox" id="{{$event_checkpoint['id']}}" value="{{$event_checkpoint['done']}}" class="hidden peer"  @if($event_checkpoint->done) checked @endif>
                            <label for="{{$event_checkpoint['id']}}" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 peer-checked:border-blue-600 dark:peer-checked:border-blue-600 hover:text-gray-600 dark:peer-checked:text-gray-300 peer-checked:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">                           
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
                </ul> <!-- cd-faq__group -->
              </div> 
            @endforeach
          {{-- </div> <!-- cd-faq__items -->
  
          <a href="#0" class="cd-faq__close-panel text-replace">Schliessen</a>
  
          <div class="cd-faq__overlay" aria-hidden="true"></div> --}}
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