@extends('layouts.admin')
@section('content')
    <div>
        <div class="container-fluid">
            <header>
                <h3  class="text-3xl font-bold dark:text-white">Buchung {{str_pad($event['id'],5,'0', STR_PAD_LEFT)}} bearbeiten</h3>
            </header>
            <div class="form-row">
                <div class="form-group col-xl-10">
                    <x-forms.form :action="route('admin.events.update', $event)" method="PATCH" :model="$event">
                        <div class="form-row">
                            <x-forms.container class="col-xl-2 col-6">
                                <x-forms.text label="Start:" name="start_date" type="date" required=true onchange="Total_Change()"/>
                            </x-forms.container>
                            <x-forms.container class="col-xl-2 col-6">
                                <x-forms.text label="Ende:" name="end_date" type="date" required=true onchange="Total_Change()"/>
                            </x-forms.container>
                            <x-forms.container class="col-xl-3 col-6">
                                <x-forms.text label="Name:" name="name" required=true/>
                            </x-forms.container>
                            <x-forms.container class="col-xl-3 col-6">
                                <x-forms.text label="Vorname:" name="firstname"/>
                            </x-forms.container>
                            <x-forms.container class="col-xl-2">
                                <x-forms.text label="Gruppe/Anlass:" name="group_name"/>
                            </x-forms.container>
                        </div>
                        <div class="form-row">
                            <x-forms.container class="col-xl-3 col-6">
                                <x-forms.text label="Email:" name="email" required=true type="email"/>
                            </x-forms.container>
                            <x-forms.container class="col-xl-2 col-6">
                                <x-forms.text label="Telefon:" name="telephone" />
                            </x-forms.container>
                            <x-forms.container class="col-xl-2">
                                <x-forms.text label="Strasse:" name="street" required=true/>
                            </x-forms.container>
                            <x-forms.container class="col-xl-2 col-3">
                                <x-forms.text label="PLZ:" name="plz" required=true type="number"/>
                            </x-forms.container>
                            <x-forms.container class="col-xl-3 col-9">
                                <x-forms.text label="Ortschaft:" name="city" required=true/>
                            </x-forms.container>
                        </div>
                        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
                        <x-forms.row>
                            @foreach ($positions as $index => $position)
                                @if ($position->pricelist_position['bexio_code']<50)
                                    <x-forms.hidden name="{{'positions['.$position['id'].']'}}" id="{{'position_'.$position['id']}}" onChange="Total_Change()" type="number" value="{{$position['amount']}}"/>
                                @else
                                    <x-forms.container class="col-xl-2 col-4">
                                        <x-forms.text label="{{$position->pricelist_position['name'] . ' ('. $position->pricelist_position['price'] . ' CHF)'}}" name="{{'positions['.$position['id'].']'}}" 
                                            type="number" id="{{'position_'.$position['id']}}" onChange="Total_Change()" value="{{$position['amount']}}"/> 
                                    </x-forms.container>
                                @endif
                            @endforeach
                            <x-forms.hidden name="total_people" />
                        </x-forms.row>
                        <div class="form-row">
                            <x-forms.container class="col-xl-2 col-4">
                                <x-forms.text label="Tage:" name="total_days" type="number" required=true onChange="Total_Change()"/>
                            </x-forms.container>
                            <x-forms.container class="col-xl-2 col-4">
                                <x-forms.text label="Rabatt [%]:" name="discount" type="number" onChange="Total_Change()"/>
                            </x-forms.container>
                            <x-forms.container class="col-xl-2 col-4">
                                <x-forms.hidden label="Total [CHF]:" name="total_amount" type="number"/>
                                <br>
                                <span id="total"></span>.-
                            </x-forms.container>
                            <x-forms.container class="col-xl-2 col-4">
                                <x-forms.text type="checkbox" name="early_checkin" value="{{$event['early_checkin']}}"/>
                                <label for="early_checkin">Early Check-In</label>
                                <br>
                                <x-forms.text type="checkbox" name="late_checkout" value="{{$event['late_checkout']}}"/>
                                <label for="late_checkout">Late Check-Out</label>
                            </x-forms.container>
                            <x-forms.container class="col-xl-2 col-4">
                                <x-forms.text label="Externe Buchungs-Nr.:" name="foreign_key"/>
                            </x-forms.container>
                            <div class="col-xl-2 col-6 form-group">
                                <x-forms.select label="Angebot / Rechnung:" name="contract_status_id" :collection="$contract_statuses" required=true/>
                            </div>
                        </div>
                        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
                        <div class="form-row">
                            <div class="col-xl-2 col-12">
                                <div class="form-row"> 
                                    <x-forms.container class="col-xl-12 col-6">
                                        <x-forms.select label="Verantwortlicher:" name="user_id" required=true :collection="$users"/>
                                    </x-forms.container>
                                    <x-forms.container class="col-xl-12 col-6">
                                        <x-forms.text label="Tür-Code:" name="code" type="number"/>
                                    </x-forms.container>
                                </div>
                                    
                            </div>
                            <x-forms.container class="col-xl-6">
                                <x-forms.text-area label="Bemerkungen:" name="comment" rows=5/>
                            </x-forms.container>
                            <x-forms.container class="col-xl-4">
                                <x-forms.text-area label="Bemerkung (intern):" name="comment_intern" rows=5/>
                            </x-forms.container> 
                        </div> 
                        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
                        <div class="form-row">
                            <x-forms.container class="col-xl-2 col-6">
                                <x-forms.button type="submit" name="submit" class="focus:outline-none text-white bg-gladegreen hover:bg-gladegreen hover:text-white focus:ring-4 focus:ring-gladegreen font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gladegreen dark:hover:bg-gladegreen dark:focus:ring-gladegreen" value="1 Buchung updaten">
                                    1 Buchung updaten
                                </x-forms.button>
                            </x-forms.container>
                            @switch($event['contract_status_id'])
                                @case(config('status.contract_offen'))  
                                    <x-forms.container class="col-xl-2 col-6">
                                        <x-forms.button type="submit" class="focus:outline-none text-white bg-norway hover:bg-norway hover:text-white focus:ring-4 focus:ring-norway font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-norway dark:hover:bg-norway dark:focus:ring-norway" name="submit" value="2 Angebot erstellen">
                                            {{config('mail.direct_send') ? '2 Angebot erstellen & versenden' : '2 Angebot erstellen'}}
                                        </x-forms.button>
                                    </x-forms.container>
                                    <x-forms.container class="col-xl-2 col-6"/>
                                    <x-forms.container class="col-xl-2 col-6"/>
                                    @break
                                @case(config('status.contract_angebot_erstellt'))
                                    <x-forms.container class="col-xl-2 col-6">
                                        <a target="_blank" class = 'focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith' href="https://office.bexio.com/index.php/kb_offer/show/id/{{$event['bexio_offer_id']}}">Angebot anzeigen</a>
                                    </x-forms.container>
                                    <x-forms.container class="col-xl-2 col-6">
                                        <x-forms.button type="submit" class="focus:outline-none text-white bg-ebonyclay hover:bg-ebonyclay hover:text-white focus:ring-4 focus:ring-ebonyclay font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-ebonyclay dark:hover:bg-ebonyclay dark:focus:ring-ebonyclay" name="submit" value=" 3 Angebot versenden">
                                            3 Angebot versenden
                                        </x-forms.button>
                                    </x-forms.container>
                                    <x-forms.container class="col-xl-2 col-6"/>
                                    @break
                                @case(config('status.contract_angebot_versendet'))
                                    <x-forms.container class="col-xl-2 col-6">
                                        <a target="_blank" class = 'focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith' href="https://office.bexio.com/index.php/kb_offer/show/id/{{$event['bexio_offer_id']}}">Angebot anzeigen</a>
                                    </x-forms.container>
                                    <x-forms.container class="col-xl-2 col-6">
                                        <x-forms.button type="submit" class="focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith" name="submit" value="3 Erinnerung versenden">
                                            3 Erinnerung versenden
                                        </x-forms.button>
                                    </x-forms.container>
                                    <x-forms.container class="col-xl-2 col-6">
                                        <x-forms.button type="submit" class="focus:outline-none text-white bg-ebonyclay hover:bg-ebonyclay hover:text-white focus:ring-4 focus:ring-ebonyclay font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-ebonyclay dark:hover:bg-ebonyclay dark:focus:ring-ebonyclay" name="submit" value="4 Rechnung erstellen">
                                            4 Rechnung erstellen
                                        </x-forms.button>
                                    </x-forms.container>
                                    @break
                                @case(config('status.contract_rechnung_erstellt'))
                                    <x-forms.container class="col-xl-2 col-6">
                                        <a target="_blank" class = 'focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith' href="https://office.bexio.com/index.php/kb_offer/show/id/{{$event['bexio_offer_id']}}">Angebot anzeigen</a>
                                    </x-forms.container>
                                    <x-forms.container class="col-xl-2 col-6">
                                        <a target="_blank" class = 'focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith' href="https://office.bexio.com/index.php/kb_invoice/show/id/{{$event['bexio_invoice_id']}}">Rechnung anzeigen</a>
                                    </x-forms.container>
                                    <x-forms.container class="col-xl-2 col-6">
                                        <x-forms.button type="submit" class="focus:outline-none text-white bg-norway hover:bg-norway hover:text-white focus:ring-4 focus:ring-norway font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-norway dark:hover:bg-norway dark:focus:ring-norway" name="submit" value="5 Rechnung versenden">
                                            5 Rechnung versenden
                                        </x-forms.button>
                                    </x-forms.container>
                                    @break
                                @case(config('status.contract_rechnung_versendet'))
                                <x-forms.container class="col-xl-2 col-6">
                                        <a target="_blank" class = 'focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith' href="https://office.bexio.com/index.php/kb_offer/show/id/{{$event['bexio_offer_id']}}">Angebot anzeigen</a>
                                    </x-forms.container>
                                    <x-forms.container class="col-xl-2 col-6">
                                        <a target="_blank" class = 'focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith' href="https://office.bexio.com/index.php/kb_invoice/show/id/{{$event['bexio_invoice_id']}}">Rechnung anzeigen</a>
                                    </x-forms.container>
                                    <x-forms.container class="col-xl-2 col-6">
                                        <x-forms.button type="submit" class="focus:outline-none text-white bg-ebonyclay hover:bg-ebonyclay hover:text-white focus:ring-4 focus:ring-ebonyclay font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-ebonyclay dark:hover:bg-ebonyclay dark:focus:ring-ebonyclay" name="submit" value="6 Genossenschafts Info versenden">
                                            6 Genossenschafts Info versenden
                                        </x-forms.button>
                                    </x-forms.container>
                                    @break
                                @default
                            @endswitch
                            <x-forms.container class="col-xl-4">
                                <x-forms.text-area label="Zusatztext für Mail:" name="additional_text" rows=3/>
                                <br>
                                <div class="form-group">
                                    <button type="button" class="focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith" onclick="PrepareReminderMail()">Erinnerungstext einfügen</button>
                                </div>
                            </x-forms.container>
                        </div>
                    </x-forms.form>
                    <x-forms.form :action="route('admin.events.destroy', $event)" method="DELETE" :model="$event" id="DeleteForm">
                        <x-forms.button type="submit" name="submit-delete" class="confirm-delete focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                            Buchung löschen
                        </x-forms.button>
                    </x-forms.form>
                </div>
                <div class="col-xl-2">

                    <br>
                    <div class="form-group">
                        <a type="button" class="focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith" href="{{route('bookings.uuid',$event['uuid'])}}" target="blank">Zum Kundenlogin</a>
                    </div>
                    @if(!$event->cleaning_mail)
                        <br>
                        <div class="form-group">
                            <button class="focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith" onclick="PrepareMail()">Mail an Putzfirma</button>
                        </div>
                        <br>
                    @endif
                    <div id="cleaning_mail" style="display: none">
                        <x-forms.form :action="route('events.sendCleaningMail', $event)" method="POST" :model="$event">
                            <x-forms.container>
                                <x-forms.text label="Mail Adresse:" name="cleaning_mail_address" type="email"/>
                            </x-forms.container>
                            <x-forms.container>
                                <x-forms.text-area label="Mail Text:" name="cleaning_mail_text" rows=9/>
                            </x-forms.container>
                            <x-forms.button type="submit" name="submit" class="focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith">
                                Mail versenden
                            </x-forms.button>
                        </x-forms.form>
                    </div>
                    <br>
                    <div class="form-group">
                        <a href="{{route('events.downloadParking', $event)}}" class="focus:outline-none text-white bg-grannysmith hover:bg-grannysmith hover:text-white focus:ring-4 focus:ring-grannysmith font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-grannysmith dark:hover:bg-grannysmith dark:focus:ring-grannysmith">Parkplatz-Karte herunterladen</a>
                    </div>
                    <br><br>
                    <table class="table">
                        <tbody>
                            @foreach ($event->notifications->sortBy('created_at') as $notification)
                                <tr>
                                    <td>
                                        {{\Carbon\Carbon::parse($notification->created_at)->isoFormat('DD.MM.YY')}}
                                    </td>
                                    <td>{{$notification->data['action'] ?? 'Buchung erstellt' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                @include('includes.form_error')
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="module">
        window.addEventListener("load", function() {
            Total_Change();
            $('.confirm-delete').on('click', function(e){
                e.preventDefault(); //cancel default action

                Swal.fire({
                    title: 'Buchung löschen?',
                    text: "Lösche Buchungen nur im Notfall. Zur Nachvollziehbarkeit der Buchungen, bitte den Status auf 'Storniert' setzen und nicht löschen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ja',
                    cancelButtonText: 'Abbrechen',
                    confirmButtonColor: 'blue',
                    cancelButtonColor: 'red',
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById("DeleteForm").submit();
                    }
                });
            });
        });
        $("#external").change(function() {
            Total_Change();
        });
        function PrepareMail() {
            document.getElementById("cleaning_mail").style.display = "block";

            var start_date = new Date(document.getElementById('start_date').value).toLocaleDateString();
            var end_date = new Date(document.getElementById('end_date').value).toLocaleDateString();
            var total = 0, subtotal = 0, id = 0;
            var positions = @json($positions);
            positions.forEach(position => {
                id = 'position_' + position['id'];
                if(position.pricelist_position['bexio_code']>100 && position.pricelist_position['bexio_code']<200){
                    subtotal =  parseInt(document.getElementById(id).value) || 0;
                    total += subtotal;
                }
            });
            var text = "Sehr geehrte Damen und Herren,\n" + "Wir haben eine neue Buchung für unser Ferienhaus vom " + start_date + " bis "
            + end_date + " (" + document.getElementById("total_days").value + " " + (document.getElementById("total_days").value ==1 ? "Nacht" : "Nächte") + ") für "
            + total + " Personen. Für einige nachfolgende Reinigung wären wir sehr dankbar.\n\n" + "Vielen Dank und freundliche Grüsse,\n" + "Verwaltung Ferienhaus Itelfingen";
            $('#cleaning_mail_address').val(@json(config('mail.cleaning_mail')));
            $('#cleaning_mail_text').val(text);
        }

        function PrepareReminderMail() {
            var text = "Wir haben bisher noch keine Bestätigung für obenstehendes Angebot von Dir erhalten. Sollten wir innerhalb der nächsten zwei Wochen keine Bestätigung erhalten, müssen wir die Buchung leider wieder freigeben.";
            $('#additional_text').val(text);
        }

        function Total_Change() {
            var start_date = new Date(document.getElementById('start_date').value);
            var end_date = new Date(document.getElementById('end_date').value);
            var days = (end_date - start_date)/(24*3600*1000);
            var one_day = (days === 0);
            if(days === 0){
                days = 1;
            }
            var positions = @json($positions);
            var total_amount = 0, id = 0, total_person = 0;
            var discount = (100 - (parseInt(document.getElementById("discount").value) || 0)) / 100 ;
            positions.forEach(position => {
                id = 'position_' + position['id'];
                var person = position.pricelist_position['bexio_code'] < 100 ? 0 : parseInt(document.getElementById(id).value);
                person = person || 0;
                var subtotal = 0
                if(position.pricelist_position['bexio_code'] < 50){
                    if(one_day){
                        subtotal = position.pricelist_position['price'] / 2;
                        person = 1;
                    }
                    else {
                        subtotal = position.pricelist_position['price'];
                    }
                }
                else if(position.pricelist_position['bexio_code'] < 100) {
                    subtotal = position.pricelist_position['price'] * position['amount'];
                }
                else if(position.pricelist_position['bexio_code'] > 100) {
                    subtotal = parseInt(document.getElementById(id).value) * position.pricelist_position['price'] * days * discount || 0;
                }
                if( position.pricelist_position['bexio_code']>200){
                    subtotal =  Math.max(parseInt(document.getElementById(id).value) -3,0) * position.pricelist_position['price'] * Math.max(days,1) || 0;
                    person = 0;
                }
                total_amount += subtotal;
                total_person += person;
            });
            $("#total").text(total_amount);
            $("#total_days").val(days);
            $("#total_amount").val(total_amount);
            $("#total_people").val(total_person);
        }
        window.PrepareMail = PrepareMail;
        window.PrepareReminderMail = PrepareReminderMail;
        window.Total_Change = Total_Change;
    </script>
@endpush
