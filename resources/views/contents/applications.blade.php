@extends('layouts.app')

@section('content')

  @include('includes.header')
  <div id="app">

    <main id="main">
      <section class="breadcrumbs">
        <div class="container">
          <div class="d-flex justify-content-between align-items-center">
            <h2>Bewerbung Genossenschaft</h2>
            <ol>
              <li><a href="{{route('home')}}">Home</a></li>
              <li>Bewerbung Genossenschaft</li>
            </ol>
          </div>
        </div>
      </section>
        <div class="container max-width-md margin-top-lg margin-bottom-lg">
            @if (session()->has('success'))
                <div class="alert alert-dismissable alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>
                        {!! session()->get('success') !!}
                    </strong>
                </div>
            @endif
            <h3>Ich möchte Genossenschafter/in werden... </h3>
            <p>
                Wir freuen uns über dein Interesse an der Genossenschaft Ferienhaus Itelfingen.
                Hier hast du die Möglichkeit, dich als Genossenschafter/in zu bewerben.
                Neben den Vorteilen bei der Miete des Hauses freuen wir uns über jeden und jede, welche die Zukunft und Stimmung des Ferienhauses positiv mitprägen!
            </p>
            <h5>Warum mitmachen?</h5>
            <ul>
                <li>Auf unserer Webseite findest du die reduzierten Preise für Genossenschafter: <a href="https://www.itelfingen.ch" target="_blank">www.itelfingen.ch</a></li>
                <li>Gemeinsam das schöne Haus mitprägen und mitunterhalten.</li>
                <li>Genossenschafter/innen können Ideen einbringen und umsetzen und sich bei der GV mit einbringen.</li>
                <li>Ein Gemeinschaftsgefühl, gemeinsam etwas Begeisterndes schaffen und prägen.</li>
                <li>In Zukunft möchten wir gemeinsam ein soziales Angebot schaffen wie z.B. "Ferien für Benachteiligte".</li>
                <li>Einladung zu Genossenschafs-Events.</li>
                <li>Möglichkeit zur Teilnahme an Genossenschafts-Wochenenden bei denen man kostenlos übernachten und beim Unterhalt oder
                    der Erweiterung der Hauses und Angebotes mitarbeiten kann.</li>
            </ul>
            <h5>Bist du einverstanden mit den folgenden Punkten?</h5>
            <ul>
                <li>Sämtliche Infos zur Genossenschaft werden per Email kommuniziert.</li>
                <li>Es gibt keine Familien-Mitgliedschaften sondern Einzelmitgliedschaften (CHF 100.-) für Erwachsene ab 18 Jahren - daher muss pro Person 1 Formular ausgefüllt werden.</li>
                <li>Der Vorstand der Genossenschaft prüft die Bewerbungen innert zwei Wochen. Bei positivem Entscheid Rückmeldung inkl. Rechnung für deinen Genossenschafts-Anteil per Email. Der Vorstand vergibt den Genossenschafter-Status nach Bezahlung der Rechnung.</li>
                <li>Durch den Beitritt gelten die <a href='/files/Statuten_Genossenschaft_Ferienhaus_Itelfingen.pdf' target="blank">Statuten</a> als akzeptiert.</li>
            </ul>
            <x-forms.form :action="route('application.store')">
                <div class="form-row">
                  <x-forms.container class="col-md-4">
                      <x-forms.text label="Vorname:" name="firstname"/>
                  </x-forms.container>
                  <x-forms.container class="col-md-4">
                      <x-forms.text label="Nachname*:" name="firstname" required=true/>
                  </x-forms.container>
                  <x-forms.container class="col-md-4">
                      <x-forms.text label="Organisation:" name="organisation"/>
                  </x-forms.container>
                </div>
                <div class="form-row">
                    <x-forms.container class="col-md-5">
                        <x-forms.text label="Strasse*:" name="street" required=true/>
                    </x-forms.container>
                    <x-forms.container class="col-md-2">
                        <x-forms.text label="PLZ*:" name="zipcode" type="number" required=true class="autocomplete_txt"/>
                    </x-forms.container>
                    <x-forms.container class="col-md-5">
                        <x-forms.text label="Ortschaft*:" name="city" required=true class="autocomplete_txt"/>
                    </x-forms.container>
                    <x-forms.hidden name="city_id" class="autocomplete_txt"/>
                </div>
                <div class="form-row">
                    <x-forms.container class="col-md-6">
                        <x-forms.text label="E-Mail:" name="email" type="email" required=true/>
                    </x-forms.container>
                    <x-forms.container class="col-md-6">
                        <x-forms.text label="Telefon / Mobil:" name="telephone"/>
                    </x-forms.container>
                </div>
                <div class="form-row">
                    <x-forms.container class="col-md-6">
                        <x-forms.text-area label="Warum willst Du Genossenschafter:in werden?*" name="why" required=true rows=3 placeholder="Wie gedenkst du vom Angebot des Ferienhauses Itelfingen Gebrauch zu machen? Wie möchtest du dich einbringen und welche Ideen hast du?"/>
                    </x-forms.container>
                    <x-forms.container class="col-md-6">
                        <x-forms.text-area label="Hast du noch Fragen oder Bemerkungen?" name="comment" rows=3/>
                    </x-forms.container>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>Danke! Noch ein Klick und deine Bewerbung ist bei uns eingegangen!</p>
                    </div>
                    <div class="col-md-6">
                        <x-forms.button type="submit" class="btn btn-frontpage">
                            Bewerbung absenden
                        </x-forms.button>
                    </div>
                </div>
            </x-forms.form>
        </div>
    </main><!-- End #main -->
        <!-- ======= Footer ======= -->
      @include('includes.footer')

    {{-- @include('cookieConsent::index') --}}
  </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script type="text/javascript">

    //autocomplete script
    $(document).on('focus','.autocomplete_txt',function(){
        type = $(this).attr('name');

        if(type =='city')autoType='name';
        if(type =='zipcode')autoType='plz';
        if(type =='city_id')autoType='id';

        $(this).autocomplete({
            minLength: 2,
            highlight: true,
            source: function( request, response ) {
                $.ajax({
                    url: "{{ route('searchajaxcity') }}",
                    dataType: "json",
                    data: {
                        term : request.term,
                        type : type,
                    },
                    success: function(data) {
                        var array = $.map(data, function (item) {
                                return {
                                label: item['plz'] + ' ' + item['name'],
                                value: item[autoType],
                                data : item
                            }
                        });
                        response(array)
                    }
                });
            },
            select: function( event, ui ) {
                var data = ui.item.data;
                $("[name='city']").val(data.name);
                $("[name='zipcode']").val(data.plz);
                $("[name='city_id']").val(data.id);
            }
        });


    });
    </script>
@endsection
