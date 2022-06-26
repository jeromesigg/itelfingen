<section id="pricelist" class="pricelist section-blue">
    <div class="container" data-aos="fade-up">

      <div class="section-title">
        <p>Unsere Preise</p>
      </div>

      <div class="row pricelist-container" data-aos="fade-up" data-aos-delay="200">
        <div class="col-lg-6">
            <div class="card card_title-line_white_mobile" style="width: 100%">
              <div class="card_content">
                <span class="card_title"> Übernachtungen</span>
                <p class="card_text">
                  {{-- Buchungspauschale 200.- <br> --}}
                  Reinigungsspauschale {{config('pricelist.cleaning')}}.-
                  <span class="card_subtitle">Genossenschafter:innen</span>
                  Fr. {{config('pricelist.member_adults')}}.- pro Nacht und Person (ab 16 Jahren) <br>
                  Fr. {{config('pricelist.member_kids')}}.- pro Nacht und Person (6-16 Jahren)
                  <span class="card_subtitle">Privatpersonen</span>
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
                <span class="card_title">Tagesaufenthalte (ohne Übernachtung)</span>
                <p class="card_text">
                    Reinigungsspauschale {{config('pricelist.cleaning')/2}}.-<br>
                    Tagespauschale Fr. {{config('pricelist.daily')}}.-
                </p>
              </div>
           </div>
            <div class="card">
              <div class="card_content">
                <ul class="card_list">
                <li>Im Preis sind alle Taxen für Strom, Warmwasser und Heizung enthalten. </li>
                <li>Drei Parkplätze sind im Preis enthalten. Jeder weitere kostet 5.- pro Tag (bis zu 5 weitere Plätze). </li>
                <li>Die Annulationskosten betragen 50% des Totalbetrags bis 14 Tage vor Mietbeginn, danach 100% des Totalbetrags. </li>
                </ul>
              </div>
            </div>
          </div>

      </div>
    </div>
  </section><!-- End pricelist Section -->
