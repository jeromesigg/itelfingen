<section id="booking" class="calendar section">

    <div class="px-4 mx-auto max-w-screen-2xl lg:px-6">
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
            <div class="hk-reservation__container">
                <x-forms.form :action="route('event.create')" id='calendarform' lang="de-CH">
                    <x-honeypot />
                    <div class="hk-calendar" id="wizard_calendar">
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
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @for ($i = 0; $i <= 6; $i++)
                                    <div class="{{($i>1)?'d-none d-sm-block':''}}">
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
                        </div>
                        <div class="grid md:grid-cols-6 gap-4 mt-2">
                            <div class="col-span-5">
                                <div class="hk-agenda__legend">
                                    <h5 class="text-xl">Legende</h5>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="text-base">
                                            <span class="hk-agenda__legend-block hk-agenda__day--FF"></span>
                                            <span class="hk-agenda__legend-label align-top">Frei</span>
                                        </div>
                                        <div class="text-base">
                                            <span class="hk-agenda__legend-block hk-agenda__day--PP"></span>
                                            <span class="hk-agenda__legend-label align-top">Provisorisch besetzt</span>
                                        </div>
                                        <div class="text-base">
                                            <span class="hk-agenda__legend-block hk-agenda__day--BB"></span>
                                            <span class="hk-agenda__legend-label align-top">Besetzt</span>
                                        </div>
                                        <div class="text-base">
                                            <span class="hk-agenda__legend-block hk-agenda__day--FB"></span>
                                            <span class="hk-agenda__legend-label align-top">Halbtage</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div class="form-group">
                                    <button type="button" class="btn btn-frontpage bg-gladegreen" onclick="wizard_step(2)">Weiter</button>
                                </div>
                            </div>
                        </div>
                        <br>
                        <br>
                    </div>
                    <div class="hk-dateselect" id="wizard_formular" style="display: none">
                        <h3 class="text-3xl dark:text-white mb-2.5">Buchungs Informationen</h3>
                        <div class="grid xl:grid-cols-3 gap-4" >
                            <div>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <x-forms.container>
                                        <x-forms.text label="Vorname:" name="firstname" />
                                    </x-forms.container>
                                    <x-forms.container>
                                        <x-forms.text label="Nachname*:" name="name" required=true />
                                    </x-forms.container>
                                </div>
                                <x-forms.container>
                                    <x-forms.text label="Anlass / Gruppe:" name="group" />
                                </x-forms.container>
                                <x-forms.container>
                                    <x-forms.text label="Strasse*:" name="street" required=true />
                                </x-forms.container>
                                <div class="grid md:grid-cols-4 gap-4">
                                    <x-forms.container>
                                        <x-forms.text label="PLZ*:" name="zipcode" required=true class="autocomplete_txt"/>
                                    </x-forms.container>
                                    <x-forms.container class="md:col-span-3">
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
                                    <label for="terms">Ich akzeptiere die <a href='/files/Hausordnung.pdf' target="blank" class="text-orientalpink">Hausordnung</a>*:</label>
                                    <x-forms.text name="terms" type="checkbox" required=true/>
                                </x-forms.container>
                            </div>
                            <div class="xl:col-span-2">
                                <div class="h5 text-end" id="multiday_text">
                                    Buchung von <span id="start_date_text"></span> bis <span id="end_date_text"></span>
                                    <x-forms.hidden name="start_date" />
                                    <x-forms.hidden name="end_date"/>
                                </div>
                                <div class="h5 text-end" id="oneday_text">
                                    Buchung vom <span id="date_text"></span></span>
                                    <x-forms.hidden name="date"/>
                                </div>
                                <div class="relative overflow-x-auto">
                                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead class="text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-6 py-3" style="width: 15%">Anzahl</th>
                                                <th scope="col" class="px-6 py-3" style="width: 60%">Artikel</th>
                                                <th scope="col" class="px-6 py-3" style="width: 10%">Kosten</th>
                                                <th scope="col" class="px-6 py-3" style="width: 15%">Total</th>
                                            </tr>
                                        </thead>
                                        <tfoot class="text-xs text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th class="px-6 pt-3" scope="row"></th>
                                                <th class="px-6 pt-3">Total <span id="days"></span></th>
                                                <th class="px-6 pt-3"></th>
                                                <th class="px-6 pt-3"><span id="total_amount_show"></span>.-</th>
                                                <x-forms.hidden name="total_amount"/>
                                                <x-forms.hidden name="total_people"/>
                                                <x-forms.hidden name="discount"/>
                                                <x-forms.hidden name="total_days"/>
                                            </tr>
                                            <tr>
                                                <th class="px-6 pt-3" scope="row"></th>
                                                <th class="px-6 pb-3" colspan="3">
                                                    <span style="text-xs">
                                                        Alle Preise in CHF. Die definitiven Preise werden im Mietvertrag festgelegt.
                                                    </span>
                                                </th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @foreach ($positions as $position)
                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200" id="row_{{$position['bexio_code']}}">
                                                    @if ($position['bexio_code']<100)
                                                        <th class="px-6 py-4" scope="row" id={{'position_'.$position['bexio_code']}}><span id="position_{{$position['bexio_code']}}_amount"></span></th>
                                                    @else
                                                    
                                                        <th class="px-6 py-4" scope="row"><x-forms.text name="{{'positions['.$position['bexio_code'].']'}}" id="{{'position_'.$position['bexio_code']}}" type="number" onChange="Total_Change()"/></th>
                                                    @endif
                                                    <td class="px-6 py-4">{{$position['name']}}</td>
                                                    <td class="px-6 py-4">{{$position['price']}}.-</td>
                                                    <td class="px-6 py-4"><span id="position_{{$position['bexio_code']}}_total"></span>.-</td>
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
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="mb-1">
                                <button type="button" class="btn btn-frontpage bg-gladegreen" onclick="wizard_step(1)">Zurück</button>
                            </div>
                            <div class="md:col-span-2 mb-1">
                                <x-forms.button type="submit" class="btn btn-frontpage bg-gladegreen">
                                    Reservieren
                                </x-forms.button>
                            </div>
                        </div>
                    </div>
                </x-forms.form>

            </div>
        </div>
    </div>

  </section>
