<section id="history" class="specials">
    <div class="container" data-aos="fade-up">
  
      <div class="section-title">
        <h2>Historisches</h2>
        <p>Ein paar Eckpunkte über die Geschichte des Hauses</p>
      </div>
  
      <div class="row" data-aos="fade-up" data-aos-delay="100">
        <div class="col-lg-3">
          <ul class="nav nav-tabs flex-column">
            @foreach ($histories as $history)
              <li class="nav-item">
                <a class="nav-link active show" data-toggle="tab" href="#{{$history->shorttitle}}">{{$history->shorttitle}}</a>
              </li>  
            @endforeach
          </ul>
        </div>
        <div class="col-lg-9 mt-4 mt-lg-0">
          @foreach ($histories as $history)
            <div class="tab-content">
              <div class="tab-pane active show" id="{{$history->shorttitle}}">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>{{$history->title}}</h3>
                    <p class="font-italic">{{$history->subtitle}}</p>
                    <p>{!! nl2br($history->description) !!}</p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <img src="{{$history->photo ? $history->photo->file : ''}}" alt="" class="img-fluid">
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
  
    </div>
  </section><!-- End Specials Section -->