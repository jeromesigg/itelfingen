<section id="pricelist" class="pricelist section-bg">
    <div class="container" data-aos="fade-up">
  
      <div class="section-title">
        <h2>Preisliste</h2>
        <p>Hier finden Sie die Preise für Ihren Aufenthalt</p>
      </div>

      <div class="row pricelist-container" data-aos="fade-up" data-aos-delay="200">
        @foreach ($pricelists as $price)
          <div class="col-lg-6 pricelist-item">
            <div class="pricelist-content">
              {{$price->name}}<span>{{$price->price}}</span>
            </div>
            <div class="pricelist-details">
              {{$price->details}}
            </div>
          </div>       
        @endforeach
      </div>
    </div>
  </section><!-- End pricelist Section -->