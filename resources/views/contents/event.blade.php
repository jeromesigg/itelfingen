<section id="booking" class="calendar section">

    <div class="container" >
        <div class="section-title">
            <p>Jetzt Buchungsanfrage senden</p>
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
                <x-forms.form :action="route('event.create')" id='calendarform' lang="de-CH">
                    <x-honeypot />
                    <div class="row">
                        <div class="col-lg-12 hk-calendar" id="wizard_calendar">
                            <h3 class="text-3xl dark:text-white mb-2.5">Verfügbarkeit</h3>
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
                                    <a class="hk-agenda__prev text-orientalpink mb-1" onclick="Agenda.prev(3); return false" href="#">
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
                                    <a class="hk-agenda__next text-orientalpink mb-1" onclick="Agenda.next(3); return false" href="#">
                                        späteres Datum
                                    </a>
                                </div>
                                <!-- mobile buttons -->
                                <div class="row d-flex d-sm-none">
                                    <div class="col-6">
                                        <a class="hk-agenda__prev hk-agenda__prev--mobile text-orientalpink mb-1" onclick="Agenda.prev(1); return false" href="#">
                                            früheres Datum
                                        </a>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a class="hk-agenda__next hk-agenda__next--mobile text-orientalpink mb-1" onclick="Agenda.next(1); return false" href="#">
                                            späteres Datum
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-10 col-sm-12">
                                    <div class="hk-agenda__legend">
                                        <h5 class="text-xl">Legende</h5>
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 text-base">
                                                <span class="hk-agenda__legend-block hk-agenda__day--FF"></span>
                                                <span class="hk-agenda__legend-label align-top">Frei</span>
                                            </div>
                                            <div class="col-md-3 col-sm-6 text-base">
                                                <span class="hk-agenda__legend-block hk-agenda__day--PP"></span>
                                                <span class="hk-agenda__legend-label align-top">Provisorisch besetzt</span>
                                            </div>
                                            <div class="col-md-3 col-sm-6 text-base">
                                                <span class="hk-agenda__legend-block hk-agenda__day--BB"></span>
                                                <span class="hk-agenda__legend-label align-top">Besetzt</span>
                                            </div>
                                            <div class="col-md-3 col-sm-6 text-base">
                                                <span class="hk-agenda__legend-block hk-agenda__day--FB"></span>
                                                <span class="hk-agenda__legend-label align-top">Halbtage</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-frontpage bg-gladegreen" onclick="wizard_step(2)">Weiter</button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <br>
                        </div>
                        <div class="col-md-12 col-xl-12 hk-dateselect" id="wizard_formular" style="display: none">
                            <h3 class="text-3xl dark:text-white mb-2.5">Buchungs Informationen</h3>
                            <table>
                                <tr>
                                    <td>
                                        <div class="row" >
                                            <div class="col-md-12 col-xl-4" >
                                                <div class="form-row">
                                                    <x-forms.container class="col-md-6">
                                                        <x-forms.text label="Vorname:" name="firstname" />
                                                    </x-forms.container>
                                                    <x-forms.container class="col-md-6">
                                                        <x-forms.text label="Nachname*:" name="name" required=true />
                                                    </x-forms.container>
                                                </div>
                                                <x-forms.container>
                                                    <x-forms.text label="Anlass / Gruppe:" name="group" />
                                                </x-forms.container>
                                                <x-forms.container>
                                                    <x-forms.text label="Strasse*:" name="street" required=true />
                                                </x-forms.container>
                                                <div class="form-row">
                                                    <x-forms.container class="col-md-3">
                                                        <x-forms.text label="PLZ*:" name="zipcode" required=true class="autocomplete_txt"/>
                                                    </x-forms.container>
                                                    <x-forms.container class="col-md-9">
                                                        <x-forms.text label="Ortschaft*:" name="city" required=true class="autocomplete_txt"/>
                                                    </x-forms.container>
                                                    <x-forms.hidden name="city_id" class="autocomplete_txt"/>
                                                </div>
                                                <x-forms.container>
                                                    <x-forms.text label="E-Mail*:" name="email"  required=true type="email"/>
                                                </x-forms.container>
                                                <x-forms.container>
                                                    <x-forms.text label="Telefon / Mobil:" name="telephone"/>
                                                </x-forms.container>
                                                <x-forms.container>
                                                    <x-forms.text-area label="Wie sind Sie auf uns aufmerksam geworden?" name="marketing_comment" rows=3/>
                                                </x-forms.container>
                                                <x-forms.container>
                                                    <label for="terms">Ich akzeptiere die <a href='/files/Hausordnung.pdf' target="blank">Hausordnung</a>*:</label>
                                                    <x-forms.text name="terms" type="checkbox" required=true/>
                                                </x-forms.container>
                                            </div>
                                            <div class="col-md-12 col-xl-8">
                                                <div class="h5" style="text-align: end" id="multiday_text">
                                                    Buchung von <span id="start_date_text"></span> bis <span id="end_date_text"></span>
                                                    <x-forms.hidden name="start_date" />
                                                    <x-forms.hidden name="end_date"/>
                                                </div>
                                                <div class="h5" style="text-align: end" id="oneday_text">
                                                    Buchung vom <span id="date_text"></span></span>
                                                    <x-forms.hidden name="date"/>
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
                                                                <x-forms.hidden name="total_amount"/>
                                                                <x-forms.hidden name="total_people"/>
                                                                <x-forms.hidden name="discount"/>
                                                                <x-forms.hidden name="total_days"/>
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
                                                                   
                                                                        <th scope="row"><x-forms.text name="{{'positions['.$position['bexio_code'].']'}}" id="{{'position_'.$position['bexio_code']}}" type="number" onChange="Total_Change()"/></th>
                                                                    @endif
                                                                    <td>{{$position['name']}}</td>
                                                                    <td>{{$position['price']}}.-</td>
                                                                    <td><span id="position_{{$position['bexio_code']}}_total"></span>.-</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <x-forms.container>
                                                    <x-forms.text-area label="Bemerkungen" name="comment" rows=3 placeholder="z.B. Genossenschafts-Nr. oder Name der Genossenschafter, Anzahl Parkplätze etc."/>
                                                </x-forms.container>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-row">
                                            <div class="col-md-4 mb-1">
                                                <button type="button" class="btn btn-frontpage bg-gladegreen" onclick="wizard_step(1)">Zurück</button>
                                            </div>
                                            <div class="col-md-4 mb-1">
                                                <x-forms.button type="submit" class="btn btn-frontpage bg-gladegreen">
                                                    Reservieren
                                                </x-forms.button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </x-forms.form>

            </div>
        </div>
    </div>

  </section>
