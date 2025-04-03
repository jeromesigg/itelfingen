@extends('layouts.admin')
@section('pageTitle', 'Buchungen')
@section('content')
<div>
    <div class="container-fluid">
        <header>
            <h3 class="text-3xl font-bold dark:text-white">Buchung erstellen</h3>
        </header>
        <div>
            <button class="btn btn-primary" onclick="createIntern()">Interne Buchung erstellen</button>
            <button class="btn btn-primary" onclick="createExtern()">Externe Buchung erstellen</button>
        </div>
        <br>
        <x-forms.form :action="route('admin.events.store')">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="form-row">
                        <x-forms.container class="col-md-6">
                            <x-forms.text label="Start:" name="start_date" type="date" required=true onchange="Total_Change()"/>
                        </x-forms.container>
                        <x-forms.container class="col-md-6">
                            <x-forms.text label="Ende:" name="end_date" type="date" required=true onchange="Total_Change()"/>
                        </x-forms.container>
                    </div>
                    <div class="form-row">
                        <x-forms.container class="col-md-6">
                            <x-forms.text label="Name:" name="name" required=true/>
                        </x-forms.container>
                        <x-forms.container class="col-md-6">
                            <x-forms.text label="Vorname:" name="firstname"/>
                        </x-forms.container>
                    </div>
                    <x-forms.container>
                        <x-forms.text label="Gruppe/Anlass:" name="group_name"/>
                    </x-forms.container>
                    <div class="form-row">
                        <x-forms.container class="col-md-6">
                            <x-forms.text label="Email:" name="email" required=true type="email"/>
                        </x-forms.container>
                        <x-forms.container class="col-md-6">
                            <x-forms.text label="Telefon:" name="telephone" />
                        </x-forms.container>
                    </div>
                    <x-forms.container>
                        <x-forms.text label="Strasse:" name="street" required=true/>
                    </x-forms.container>
                    <div class="form-row">
                        <x-forms.container class="col-md-6">
                            <x-forms.text label="PLZ:" name="plz" required=true type="number"/>
                        </x-forms.container>
                        <x-forms.container class="col-md-6">
                            <x-forms.text label="Ortschaft:" name="city" required=true/>
                        </x-forms.container>
                    </div>
                </div>
                <div class="form-group col-md-6" style="padding-left:30px">
                    <x-forms.container>
                        <x-forms.text-area label="Bemerkungen:" name="comment" rows=3/>
                    </x-forms.container>
                    <div class="form-row">
                        <div class="col-md-3 form-group">
                            <x-forms.select label="Status:" name="event_status_id" :collection="$event_statuses"/>
                        </div>
                        <x-forms.container class="col-md-6">
                            <x-forms.text-area label="Bemerkung (intern):" name="comment_intern" rows=3/>
                        </x-forms.container>
                        <x-forms.container class="col-md-3">
                            <x-forms.text label="Externe Buchungs-Nr.:" name="foreign_key"/>
                        </x-forms.container>
                    </div>
                    <div class="form-row">
                        @foreach ($positions as $index => $position)
                            @if ($position['bexio_code']<50)
                                <x-forms.hidden name="{{'positions['.$position['bexio_code'].']'}}" id="{{'position_'.$position['id']}}" onChange="Total_Change()"/>
                            @else
                                <x-forms.container class="col-md-3">
                                    <x-forms.text label="{{ $position['name'] . ' ('. $position['price'] . ' CHF)'}}" name="{{'positions['.$position['bexio_code'].']'}}" type="number" id="{{'position_'.$position['id']}}" onChange="Total_Change()" />
                                </x-forms.container>
                            @endif
                        @endforeach
                        <x-forms.hidden name="total_people" />
                        <x-forms.container class="col-md-3">
                            <x-forms.text label="Tage:" name="total_days" type="number" required=true onChange="Total_Change()"/>
                        </x-forms.container>
                        <x-forms.container class="col-md-3">
                            <x-forms.text label="Rabatt [%]:" name="discount" type="number" onChange="Total_Change()"/>
                        </x-forms.container>
                        <x-forms.container class="col-md-3">
                            <label for="total" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total CHF</label>
                            <span id="total"></span>.-
                        </x-forms.container>
                    </div>
                    <div class="form-row">
                        <x-forms.container class="col-md-6">
                            <x-forms.select label="Verantwortlicher:" name="user_id" required=true :collection="$users"/>
                        </x-forms.container>
                        <x-forms.container class="col-md-6">
                            <x-forms.text label="Tür-Code:" name="code" type="number"/>
                        </x-forms.container>
                    </div>
                </div>
            </div>
            <x-forms.button type="submit" class="btn btn-primary">
                Buchung erstellen
            </x-forms.button>
        </x-forms.form>
        <div class="row">
            @include('includes.form_error')
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="module">
    function createIntern() {
	    $('#name').val("Interne Buchung");
	    $('#street').val("Itelfingen 3");
	    $('#plz').val("6344");
	    $('#city').val("Meierskappel");
	    $('#email').val("verwalter@itelfingen.ch");
	    $('#total_people').val("0");
	    $('#total_days').val("0");
	    $('#total_amount').val("0");
        $('#code').val("290393");
	    $('#event_status_id').val(@json(config('status.event_eigene')));
    }
    function createExtern() {
        $('#name').val("Externe Buchung");
        $('#street').val("Itelfingen 3");
        $('#plz').val("6344");
        $('#city').val("Meierskappel");
        $('#email').val("verwalter@itelfingen.ch");
        $('#total_people').val("0");
        $('#total_days').val("0");
        $('#total_amount').val("0");
        $('#code').val("");
        $('#event_status_id').val(@json(config('status.event_neu')));
    }
    function Total_Change() {
        var start_date = new Date(document.getElementById('start_date').value);
        var end_date = new Date(document.getElementById('end_date').value);
        var days = (end_date - start_date)/(24*3600*1000);
        var positions = @json($positions);
        var total_amount = 0, id = 0, total_person = 0;
        var discount = (100 - (parseInt(document.getElementById("discount").value) || 0)) / 100 ;
        positions.forEach(position => {
            id = 'position_' + position['id'];
            var person = position['bexio_code'] < 100 ? 0 : parseInt(document.getElementById(id).value);
            person = person || 0;
            var subtotal = 0
            if(days === 0){
                if(position['bexio_code'] < 50){
                    subtotal = position['price'] / 2;
                }
                else if(position['bexio_code'] < 100) {
                    subtotal =position['price']
                }
            }
            else {
                if(position['bexio_code'] < 50){
                    subtotal = position['price'];
                }
                else if(position['bexio_code'] > 100) {
                    subtotal = parseInt(document.getElementById(id).value) * position['price'] * days * discount || 0;
                }
            }
            if( position['bexio_code']>200){
                subtotal =  Math.max(parseInt(document.getElementById(id).value) -3,0) * position['price'] * Math.max(days,1) || 0;
            }
            total_amount += subtotal;
            total_person += person;
        });
        $("#total").text(total_amount);
        $("#total_days").val(days);
        $("#total_amount").val(total_amount);
        $("#total_people").val(total_person);
    }
    window.createExtern = createExtern;
    window.createIntern = createIntern;
    window.Total_Change = Total_Change;
</script>
@endpush
