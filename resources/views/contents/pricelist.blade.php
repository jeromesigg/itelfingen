<section id="pricelist" class="pricelist section-blue">
    <div class="container" >

      <div class="section-title">
        <p>Unsere Preise</p>
      </div>

      <div class="row pricelist-container">
        <div class="col-lg-6">
            <div class="card card_title-line_white_mobile" style="width: 100%">
              <div class="card_content">
                <h3 class="text-3xl"> Übernachtungen</h3>
                <p class="card_text">
                  {{-- Buchungspauschale 200.- <br> --}}
                    Reinigungs- und Buchungspauschale {{config('pricelist.cleaning')}}.
                  <h4 class="text-2xl mt-3">Genossenschafter:innen</h4>
                  Fr. {{config('pricelist.member_adults')}}.- pro Nacht und Person (ab 16 Jahren) <br>
                  Fr. {{config('pricelist.member_kids')}}.- pro Nacht und Person (6-16 Jahren)<br>
                  <h4 class="text-2xl mt-3">Privatpersonen</h4>
                  Fr. {{config('pricelist.other_adults')}}.- pro Nacht und Person (ab 16 Jahren) <br>
                  Fr. {{config('pricelist.other_kids')}}.- pro Nacht und Person (6-16 Jahren)
                </p>
              </div>
                @if($application_enabled)
                    <a type="button" class="btn btn-dark" href="{{route('applications')}}">Genossenschafter:in werden</a>
                    <br>
                @endif
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card card_title-line_white">
              <div class="card_content">
                <h3 class="text-3xl">Tagesaufenthalte (ohne Übernachtung)</h3>
                <p class="card_text">
                    Reinigungs- und Buchungspauschale {{config('pricelist.cleaning')/2}}.-<br>
                    Tagespauschale Fr. {{config('pricelist.daily')}}.-
                </p>
              </div>
           </div>
            <div class="card">
              <div class="card_content">
                <ul class="card_list">
                <li>Im Preis sind alle Taxen für Strom, Warmwasser und Heizung enthalten. </li>
                <li>Drei Parkplätze sind im Preis enthalten. Jeder weitere kostet 5.- pro Tag (bis zu 5 weitere Plätze). </li>
                <li>Die Annullationskosten betragen ab 3 Monaten vor Mietbeginn bei einer definitiven Buchung 50% des Totalbetrags, ab 14 Tage vor Mietbeginn 100% des Totalbetrags. </li>
                </ul>
              </div>
            </div>
          </div>

      </div>
    </div>
  </section><!-- End pricelist Section -->
