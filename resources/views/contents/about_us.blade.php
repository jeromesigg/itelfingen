<section id="chefs" class="chefs">
    <div class="container" data-aos="fade-up">
  
      <div class="section-title">
        <h2>Über uns</h2>
        <p>Wer steckt hinter dem Lagerhaus</p>
      </div>
  
      <div class="row">
        
        @foreach ($people as $person)
          <div class="col-lg-4 col-md-6">
            <div class="member" data-aos="zoom-in" data-aos-delay="100">
              <img src="{{$person->photo ? $person->photo->file : 'http://placehold.it/250x450'}}" class="img-fluid" alt="">
              <div class="member-info">
                <div class="member-info-content">
                  <h4>{{$person->name}}</h4>
                  <span>{{$person->function}}</span>
                </div>
              </div>
            </div>
          </div>    
        @endforeach
      </div>
    </div>
  </section><!-- End Chefs Section -->