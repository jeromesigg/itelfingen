<section id="booking" class="calendar section">

    <div class="px-4 mx-auto max-w-(--breakpoint-2xl) lg:px-6">
        <div class="section-title">
            <h2>Jetzt Buchungsanfrage senden</h2>
        </div>
        @if (session()->has('success_event'))
            <div id="toast-simple" class="fixed z-60 flex items-center w-full max-w-sm p-4 text-body text-fg-success-strong bg-success-soft rounded-base shadow-xs border border-success-subtle top-5 inset-e-5" role="alert">
                <svg class="w-5 h-5 text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 18-7 3 7-18 7 18-7-3Zm0 0v-5"/></svg>
                <div class="ms-2.5 text-sm border-s border-default ps-3.5">{!! session()->get('success_event') !!}</div>
                <button type="button" class="ms-auto flex items-center justify-center text-body hover:text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded text-sm h-8 w-8 focus:outline-none" data-dismiss-target="#toast-simple" aria-label="Schliessen">
                    <span class="sr-only">Schliessen</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                </button>
            </div>
        @endif
        @if ($errors->event->any())
            <x-alert>
                <ul class="list-disc list-inside">
                    @foreach ($errors->event->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <div class="hk-reservation hk-reservation__step1" id="reservation_form">
            <div class="hk-reservation__container">
                <x-forms.form :action="route('event.create')" id='calendarform' lang="de-CH" scope="event">
                    <x-honeypot />
                    <div class="hk-calendar" id="wizard_calendar">
                        <p class="text-sm font-medium uppercase tracking-wide text-gladegreen">Schritt 1 von 2</p>
                        <h3 class="text-3xl dark:text-white mb-2.5">Datum wählen</h3>
                        <x-alert id="reservation_error_date" style="display: none">
                            Es muss mindestens eine Übernachtung gewählt werden.
                        </x-alert>
                        <x-alert id="reservation_error" style="display: none">
                            An diesem Datum kann nicht reserviert werden.
                        </x-alert>
                        <div id="discount_message"  style="display: none" class="flex items-center w-full p-4 text-body text-fg-success-strong bg-success-soft rounded-base shadow-xs border border-success-subtle" role="alert">
                            <svg class="w-6 h-6 text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.122 17.645a7.185 7.185 0 0 1-2.656 2.495 7.06 7.06 0 0 1-3.52.853 6.617 6.617 0 0 1-3.306-.718 6.73 6.73 0 0 1-2.54-2.266c-2.672-4.57.287-8.846.887-9.668A4.448 4.448 0 0 0 8.07 6.31 4.49 4.49 0 0 0 7.997 4c1.284.965 6.43 3.258 5.525 10.631 1.496-1.136 2.7-3.046 2.846-6.216 1.43 1.061 3.985 5.462 1.754 9.23Z"/></svg>
                            <div class="ms-2.5 text-sm border-s border-default ps-3.5">Du profitierst von 50 % Buchungsrabatt.</div>
                            <button type="button" class="ms-auto flex items-center justify-center text-body hover:text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded text-sm h-8 w-8 focus:outline-none" data-dismiss-target="#discount_message" aria-label="Schliessen">
                                <span class="sr-only">Schliessen</span>
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                            </button>
                        </div>

                        <div class="hk-agenda">
                            @php
                                $agendaNav = 'inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-hidden focus:ring-4 focus:ring-gray-100';
                            @endphp
                            <div class="mb-4 flex items-center justify-between gap-4">
                                <button type="button" class="{{ $agendaNav }}" onclick="Agenda.prev(Agenda.visibleMonths())">
                                    <span aria-hidden="true">‹</span> Frühere Monate
                                </button>
                                <button type="button" class="{{ $agendaNav }}" onclick="Agenda.next(Agenda.visibleMonths())">
                                    Spätere Monate <span aria-hidden="true">›</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-4">
                                @for ($i = 0; $i <= 5; $i++)
                                    <div class="{{ $i > 1 ? 'hidden sm:block' : '' }}">
                                        <h5 id="agendaTitel{{$i}}" class="hk-agenda__title"> </h5>
                                        <table class="hk-agenda__month w-full sm:w-auto">
                                            <tbody id="agendaMonat{{$i}}"> </tbody>
                                        </table>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="grid md:grid-cols-6 gap-4 mt-2">
                            <div class="md:col-span-4">
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
                                            <span class="hk-agenda__legend-label align-top">An-/Abreisetag</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-start gap-2 md:col-span-2 md:items-end">
                                <p id="selection_text" class="font-medium text-gray-900 md:text-right dark:text-white" aria-live="polite">Wähle im Kalender An- und Abreisetag.</p>
                                <x-forms.button type="button" class="btn btn-frontpage bg-gladegreen hover:bg-gladegreen" onclick="wizard_step(2)">
                                    Weiter
                                </x-forms.button>
                            </div>
                        </div>
                        <br>
                        <br>
                    </div>
                    <div class="hk-dateselect" id="wizard_formular" style="display: none">
                        <p class="text-sm font-medium uppercase tracking-wide text-gladegreen">Schritt 2 von 2</p>
                        <h3 class="text-3xl dark:text-white mb-2.5">Deine Angaben</h3>
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
                                <div class="grid md:grid-cols-4 gap-4">
                                    <x-forms.container class="md:col-span-3">
                                        <x-forms.text label="Strasse*:" name="street" required=true />
                                    </x-forms.container>
                                    <x-forms.container>
                                        <x-forms.text label="Nr.:" name="house_number" class="autocomplete_txt"/>
                                    </x-forms.container>
                                </div>
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
                                    <x-forms.text-area label="Wie bist du auf uns aufmerksam geworden?" name="marketing_comment" rows=3/>
                                </x-forms.container>
                                <x-forms.container>
                                    <label for="terms">Ich akzeptiere die <a href='/files/Hausordnung.pdf' target="_blank" rel="noopener" class="text-orientalpink hover:underline">Hausordnung</a>*:</label>
                                    <x-forms.text name="terms" type="checkbox" required=true/>
                                </x-forms.container>
                            </div>
                            <div class="xl:col-span-2">
                                <div class="mb-2 text-xl font-medium text-end" id="multiday_text">
                                    Buchung von <span id="start_date_text"></span> bis <span id="end_date_text"></span>
                                    <x-forms.hidden name="start_date" />
                                    <x-forms.hidden name="end_date"/>
                                </div>
                                <div class="mb-2 text-xl font-medium text-end" id="oneday_text">
                                    Buchung vom <span id="date_text"></span>
                                    <x-forms.hidden name="date"/>
                                </div>
                                <div class="relative overflow-x-auto">
                                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead class="text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-2 sm:px-6 py-3" style="width: 15%">Anzahl</th>
                                                <th scope="col" class="px-2 sm:px-6 py-3" style="width: 60%">Artikel</th>
                                                <th scope="col" class="px-2 sm:px-6 py-3" style="width: 10%">Kosten</th>
                                                <th scope="col" class="px-2 sm:px-6 py-3" style="width: 15%">Total</th>
                                            </tr>
                                        </thead>
                                        <tfoot class="text-gray-900 dark:bg-gray-700 dark:text-white">
                                            <tr>
                                                <th class="px-2 sm:px-6 pt-3" scope="row"></th>
                                                <th class="px-2 sm:px-6 pt-4 text-base font-semibold sm:text-lg">Total <span id="days" class="font-normal"></span></th>
                                                <th class="px-2 sm:px-6 pt-3"></th>
                                                <th class="px-2 sm:px-6 pt-4 text-lg font-bold whitespace-nowrap sm:text-xl"><span id="total_amount_show"></span>.-</th>
                                                <x-forms.hidden name="total_amount"/>
                                                <x-forms.hidden name="total_people"/>
                                                <x-forms.hidden name="discount"/>
                                                <x-forms.hidden name="total_days"/>
                                            </tr>
                                            <tr>
                                                <th class="px-2 sm:px-6 pt-3" scope="row"></th>
                                                <th class="px-2 sm:px-6 pb-3 pt-1 text-xs text-gray-600 dark:text-gray-400" colspan="3">
                                                    <span class="font-normal normal-case">
                                                        Alle Preise in CHF. Die definitiven Preise werden im Mietvertrag festgelegt.
                                                    </span>
                                                </th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @foreach ($positions as $position)
                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200" id="row_{{$position['bexio_code']}}">
                                                    @if ($position['bexio_code']<100)
                                                        <th class="px-2 sm:px-6 py-4" scope="row" id={{'position_'.$position['bexio_code']}}><span id="position_{{$position['bexio_code']}}_amount"></span></th>
                                                    @else
                                                    
                                                        <th class="px-2 sm:px-6 py-4" scope="row"><x-forms.text name="{{'positions['.$position['bexio_code'].']'}}" id="{{'position_'.$position['bexio_code']}}" type="number" onChange="Total_Change()"/></th>
                                                    @endif
                                                    <td class="px-2 sm:px-6 py-4">{{$position['name']}}</td>
                                                    <td class="px-2 sm:px-6 py-4">{{$position['price']}}.-</td>
                                                    <td class="px-2 sm:px-6 py-4"><span id="position_{{$position['bexio_code']}}_total"></span>.-</td>
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
                                <x-forms.button type="button" class="btn btn-frontpage bg-grannysmith hover:bg-grannysmith" onclick="wizard_step(1)">
                                    Zurück
                                </x-forms.button>
                            </div>
                            <div class="md:col-span-2 mb-1">
                                <x-forms.button type="submit" class="btn btn-frontpage bg-gladegreen hover:bg-gladegreen" id="submit-event-btn">
                                    Buchungsanfrage senden
                                </x-forms.button>
                            </div>
                        </div>
                    </div>
                </x-forms.form>

            </div>
        </div>
    </div>

  </section>

@push('scripts')
  <script type="module">
  	document.getElementById('calendarform').addEventListener('submit', function(e) {
        const btn = document.getElementById('submit-event-btn');
        btn.disabled = true;
        btn.innerHTML = 'Wird gesendet...';
    });
  </script>
@endpush