<section id="testimonials" class="testimonials section-bg">
    <div class="container" data-aos="fade-up">
  
      <div class="section-title">
        <h2>Erfahrungen</h2>
        <p>Was bisherige Gäste über das Lagerhaus gesagt haben</p>
      </div>
  
      <div class="owl-carousel testimonials-carousel" data-aos="zoom-in" data-aos-delay="100">
        @foreach ($testimonials as $testimonial)
          <div class="testimonial-item">
            <p>
              <i class="bx bxs-quote-alt-left quote-icon-left"></i>
              {{$testimonial->comment}}
              <i class="bx bxs-quote-alt-right quote-icon-right"></i>
            </p>
            <h3>{{$testimonial->name}}</h3>
            <h4>{{$testimonial->function}}</h4>
          </div>
        @endforeach
      </div>
    </div>
  </section>