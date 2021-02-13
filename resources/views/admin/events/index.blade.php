@extends('layouts.admin')

@section('content')
<section>
    <div class="container-fluid">

        <header> 
            <h1>Buchungen</h1>
        </header>
    
        <div class="row">
    
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Datum von</th>
                        <th scope="col">Datum bis</th>
                        <th scope="col">Name</th>
                        <th scope="col">Vorname</th>
                        <th scope="col">E-Mail</th>
                        <th scope="col">Gruppe</th>
                        <th scope="col">Bemerkung</th>
                        <th scope="col">Bemerkung Intern</th>
                        <th scope="col">Vertrag</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if($events)
                    @foreach ($events as $event)
                        <tr>
                            <td>{{$event->start_date}}</td>
                            <td>{{$event->end_date}}</td>
                            <td><a href="{{route('events.edit', $event->id)}}">{{$event->name}}</a></td>
                            <td>{{$event->firstname}}</td>
                            <td>{{$event->email}}</td>
                            <td>{{$event->group_name}}</td>
                            <td>{{$event->comment}}</td>
                            <td>{{$event->comment_intern}}</td>
                            <td>{{$event->contract_status['name']}}</td>
                            <td>{{$event->event_status['name']}}</td>
                        </tr>   
                    @endforeach
                    @endif
                </tbody>
            </table>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-5">
                    {{$events->render()}}
                </div>
            </div>
            <div class="hk-reservation hk-reservation__step1">
                <div class="hk-reservation__container container-fluid">
                    <div class="row">
                        <div class="col-lg-12 hk-calendar">            
                            <div class="hk-agenda">
                                <div class="d-none d-sm-block">
                                    <a class="hk-agenda__prev" onclick="Agenda.prev(3); return false" href="#">
                                        früheres Datum
                                    </a>
                                </div>
                                <div class="row">
                                    @for ($i = 0; $i <= 9; $i++)
                                        <div class="col-md-4 col-sm-6 {{($i>1)?'d-none d-sm-block':''}}">
                                            <h4 id="agendaTitel{{$i}}" class="hk-agenda__title"> </h4>
                                            <table class="hk-agenda__month">
                                                <tbody id="agendaMonat{{$i}}"> </tbody>
                                            </table>
                                        </div>
                                    @endfor
                                </div>
                                <div class="d-none d-sm-block">
                                    <a class="hk-agenda__next" onclick="Agenda.next(3); return false" href="#">
                                        späteres Datum
                                    </a>
                                </div>
                                <!-- mobile buttons -->
                                <div class="row d-flex d-sm-none">
                                    <div class="col-6">
                                        <a class="hk-agenda__prev hk-agenda__prev--mobile" onclick="Agenda.prev(1); return false" href="#">
                                            früheres Datum
                                        </a>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a class="hk-agenda__next hk-agenda__next--mobile" onclick="Agenda.next(1); return false" href="#">
                                            späteres Datum
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</section>
@endsection

@section('scripts')

  <!-- ======= Javascript Section ======= -->
  @include('contents.event_js')

@endsection