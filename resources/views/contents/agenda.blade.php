<section id="booking" class="calendar">
  
    <div class="container" data-aos="fade-up">
  
        <div class="section-title">
            <h2>Buchungen</h2>
            <p>Buchen Sie gleich Ihren nächsten Aufenthalt bei uns</p>
        </div>
  
        <div class="hk-reservation hk-reservation__step1">
            <div class="hk-reservation__container container-fluid">
                {!! Form::open(['method' => 'POST', 'action'=>'CalendarsController@create', 'id' => 'calendarform']) !!}
                    <br>
                    <div class="row">
                        <div class="col-lg-12 col-xl-8 hk-calendar">
                            <h3>Verfügbarkeit</h3>
                            <div id="reservation_error" style="display: none" class="error">
                                An diesem Datum kann nicht reserviert werden
                                <br><br>
                            </div>
        
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
        
                            <br>
                            <div class="hk-agenda__legend">
                                <h5>Legende</h5>
                                <div class="row">
                                    <div class="col-md-4 col-sm-6">
                                        <span class="hk-agenda__legend-block hk-agenda__day--FF"></span>
                                        <span class="hk-agenda__legend-label">Frei</span>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <span class="hk-agenda__legend-block hk-agenda__day--PP"></span>
                                        <span class="hk-agenda__legend-label">Provisorisch besetzt</span>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <span class="hk-agenda__legend-block hk-agenda__day--BB"></span>
                                        <span class="hk-agenda__legend-label">Besetzt</span>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <span class="hk-agenda__legend-block hk-agenda__day--NN"></span>
                                        <span class="hk-agenda__legend-label">Nicht verfügbar</span>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <span class="hk-agenda__legend-block hk-agenda__day--SS"></span>
                                        <span class="hk-agenda__legend-label">Gewähltes Datum</span>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <span class="hk-agenda__legend-block hk-agenda__day--BF"></span>
                                        <span class="hk-agenda__legend-label">Halbtage</span>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                {!! Form::submit('Reservieren', ['class' => 'btn btn-primary'])!!}
                            </div>
                            <br>
                            <br>
                        </div>
        
                        <div class="col-md-12 col-xl-4 hk-dateselect">
                            <h3>Gewünschtes Datum</h3>
                            <div class="text-muted">
                                Daten sind auch im Kalender auswählbar.
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col-md-6 form-group">
                                  {!! Form::label('name', 'Name:') !!}
                                  {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                                </div>
                                <div class="col-md-6 form-group">
                                  {!! Form::label('firstname', 'Vorname:') !!}
                                  {!! Form::text('firstname', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('group_name', 'Anlass / Gruppe:') !!}
                                {!! Form::text('group_name', null, ['class' => 'form-control', 'required']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('email', 'E-Mail:') !!}
                                {!! Form::email('email', null, ['class' => 'form-control', 'required', 'email']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('street', 'Strasse:') !!}
                                {!! Form::text('street', null, ['class' => 'form-control', 'required']) !!}
                            </div>
                            <div class="form-row">
                                <div class="col-md-3 form-group">
                                {!! Form::label('plz', 'PLZ:') !!}
                                {!! Form::text('plz', null, ['class' => 'form-control', 'required']) !!}
                                </div>
                                <div class="col-md-9 form-group">
                                {!! Form::label('city', 'Ortschaft:') !!}
                                {!! Form::text('city', null, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('telephone', 'Telefon:') !!}
                                {!! Form::text('telephone', null, ['class' => 'form-control', 'required']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('comment', 'Bemerkungen:') !!}
                                {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' => 3]) !!}
                            </div>
                            <div id="reservation_error_2" style="display: none" class="error">
                                An diesem Datum kann nicht reserviert werden
                                <br><br>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 form-group">
                                {!! Form::label('start_date', 'Start Datum:') !!}
                                {!! Form::date('start_date', null, ['class' => 'form-control', 'required', 'onchange' => "Agenda.change()"]) !!}
                                </div>
                                <div class="col-md-6 form-group">
                                {!! Form::label('end_date', 'End Datum:') !!}
                                {!! Form::date('end_date', null, ['class' => 'form-control', 'required', 'onchange' => "Agenda.change()"]) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::submit('Reservieren', ['class' => 'btn btn-primary'])!!}
                            </div>
                        </div>
                    </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
   
  </section>