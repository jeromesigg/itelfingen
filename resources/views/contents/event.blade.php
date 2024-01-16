<section id="booking" class="calendar section">

    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <p>Jetzt Buchungsanfrage</p>
        </div>
        @if (session()->has('success_event'))
            <div class="alert alert-dismissable alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>
                    {!! session()->get('success_event') !!}
                </strong>
            </div>
        @endif
        @if ($errors->event->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->event->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="hk-reservation hk-reservation__step1" id="reservation_form">
            <div class="hk-reservation__container container-fluid">
                {!! Form::open(['method' => 'POST', 'action'=>'EventController@create', 'id' => 'calendarform']) !!}
                    <div class="row">
                        <div class="col-lg-12 hk-calendar" id="wizard_calendar">
                            <h3>Verfügbarkeit</h3>
                            <div id="reservation_error_date" style="display: none" class="alert alert-danger">
                                Es muss ein Datum gewählt werden.
                            </div>
                            <div id="reservation_error" style="display: none" class="alert alert-danger">
                                An diesem Datum kann nicht reserviert werden.
                            </div>
                            <div id="discount_message" style="display: none" class="alert alert-dismissable alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                Sie profitieren von 50% Buchungs-Rabatt.
                            </div>

                            <div class="hk-agenda">
                                <div class="d-none d-sm-block">
                                    <a class="hk-agenda__prev" onclick="Agenda.prev(3); return false" href="#">
                                        früheres Datum
                                    </a>
                                </div>
                                <div class="row">
                                    @for ($i = 0; $i <= 6; $i++)
                                        <div class="col-md-4 col-sm-6 {{($i>1)?'d-none d-sm-block':''}}">
                                            <h5 id="agendaTitel{{$i}}" class="hk-agenda__title"> </h5>
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
                            <div class="row">
                                <div class="col-md-10 col-sm-12">
                                    <div class="hk-agenda__legend">
                                        <h5>Legende</h5>
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6">
                                                <span class="hk-agenda__legend-block hk-agenda__day--FF"></span>
                                                <span class="hk-agenda__legend-label">Frei</span>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <span class="hk-agenda__legend-block hk-agenda__day--PP"></span>
                                                <span class="hk-agenda__legend-label">Provisorisch besetzt</span>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <span class="hk-agenda__legend-block hk-agenda__day--BB"></span>
                                                <span class="hk-agenda__legend-label">Besetzt</span>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <span class="hk-agenda__legend-block hk-agenda__day--BF"></span>
                                                <span class="hk-agenda__legend-label">Halbtage</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-frontpage" onclick="wizard_step(2)">Weiter</button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <br>
                        </div>
                        <div class="col-md-12 col-xl-12 hk-dateselect" id="wizard_formular" style="display: none">
                            <h3>Buchungs Informationen</h3>
                            <table>
                                <tr>
                                    <td>
                                        <div class="row" >
                                            <div class="col-md-12 col-xl-4" >
                                                <div class="form-row">
                                                    <div class="col-md-6 form-group">
                                                    {!! Form::label('firstname', 'Vorname:') !!}
                                                    {!! Form::text('firstname', null, ['class' => 'form-control']) !!}
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                    {!! Form::label('name', 'Nachname*:') !!}
                                                    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::label('group', 'Anlass / Gruppe:') !!}
                                                    {!! Form::text('group', null, ['class' => 'form-control']) !!}
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::label('street', 'Strasse*:') !!}
                                                    {!! Form::text('street', null, ['class' => 'form-control', 'required']) !!}
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-3 form-group">
                                                        {!! Form::label('zipcode', 'PLZ*:') !!}
                                                        {!! Form::text('zipcode', null, ['class' => 'form-control autocomplete_txt', 'required', 'numeric']) !!}
                                                    </div>
                                                    <div class="col-md-9 form-group">
                                                        {!! Form::label('city', 'Ortschaft*:') !!}
                                                        {!! Form::text('city', null, ['class' => 'form-control autocomplete_txt', 'required']) !!}
                                                    </div>
                                                    {!! Form::hidden('city_id', null, ['class' => 'form-control autocomplete_txt']) !!}
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::label('email', 'E-Mail*:') !!}
                                                    {!! Form::email('email', null, ['class' => 'form-control', 'required', 'email']) !!}
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::label('telephone', 'Telefon / Mobil:') !!}
                                                    {!! Form::text('telephone', null, ['class' => 'form-control']) !!}
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::label('marketing_comment', 'Wie sind Sie auf uns aufmerksam geworden?') !!}
                                                    {!! Form::textarea('marketing_comment', null, ['class' => 'form-control', 'rows' => 5]) !!}
                                                </div>
                                                <div class="form-group">
                                                    <label for="terms">Ich akzeptiere die {!! Html::link('files/Hausordnung.pdf', 'Hausordnung', ['target' => 'blank']) !!}:</label>
                                                    {!! Form::checkbox('terms', '1', null) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xl-8">
                                                <div class="h5" style="text-align: end" id="multiday_text">
                                                    Buchung von <span id="start_date_text"></span> bis <span id="end_date_text"></span>
                                                    {!! Form::hidden('start_date', null, ['class' => 'form-control','id' => 'start_date']) !!}
                                                    {!! Form::hidden('end_date', null, ['class' => 'form-control','id' => 'end_date']) !!}
                                                </div>
                                                <div class="h5" style="text-align: end" id="oneday_text">
                                                    Buchung vom <span id="date_text"></span></span>
                                                    {!! Form::hidden('date', null, ['class' => 'form-control','id' => 'date']) !!}
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" style="width: 15%">Anzahl</th>
                                                                <th scope="col" style="width: 60%">Artikel</th>
                                                                <th scope="col" style="width: 10%">Kosten</th>
                                                                <th scope="col" style="width: 15%">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tfoot>
                                                            <tr>
                                                                <th scope="row"></th>
                                                                <th>Total <span id="days"></span></th>
                                                                <th></th>
                                                                <th><span id="total_amount_show"></span>.-</th>
                                                                {!! Form::hidden('total_amount', null, ['class' => 'form-control', 'id' => 'total_amount']) !!}
                                                                {!! Form::hidden('total_people', null, ['class' => 'form-control', 'id' => 'total_people']) !!}
                                                                {!! Form::hidden('discount', null, ['class' => 'form-control', 'id' => 'discount']) !!}
                                                                {!! Form::hidden('total_days', null, ['class' => 'form-control','id' => 'total_days']) !!}
                                                            </tr>
                                                            <tr>
                                                                <th colspan="4">
                                                                    <span style="font-size: small" id="multiday_comment">
                                                                        G = Genossenschafter<br>
                                                                    </span>
                                                                    <span style="font-size: small">
                                                                        Alle Preise in CHF. Die definitiven Preise werden im Mietvertrag festgelegt.
                                                                    </span>
                                                                </th>
                                                            </tr>
                                                        </tfoot>
                                                        <tbody>
                                                            @foreach ($positions as $position)
                                                                <tr id="row_{{$position['bexio_code']}}">
                                                                    @if ($position['bexio_code']<100)
                                                                        <th scope="row" id={{'position_'.$position['bexio_code']}}><span id="position_{{$position['bexio_code']}}_amount"></span></th>
                                                                    @else
                                                                        <th scope="row">{!! Form::number('positions['.$position['bexio_code'].']', null, [ 'class' => 'form-control', 'id' => 'position_'.$position['bexio_code'], 'onchange' => "Total_Change()"]) !!}</th>
                                                                    @endif
                                                                    <td>{{$position['name']}}</td>
                                                                    <td>{{$position['price']}}.-</td>
                                                                    <td><span id="position_{{$position['bexio_code']}}_total"></span>.-</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::label('comment', 'Bemerkungen:') !!}
                                                    {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' => 3, 'placeholder'=>'z.B. Genossenschafts-Nr. oder Name der Genossenschafter, Anzahl Parkplätze etc.']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-row">
                                            <div class="col-md-4">
                                                <button type="button" class="btn btn-frontpage" onclick="wizard_step(1)">Zurück</button>
                                            </div>
                                            <div class="col-md-4">
                                                {!! Form::submit('Reservieren', ['class' => 'btn btn-frontpage'])!!}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>

  </section>
