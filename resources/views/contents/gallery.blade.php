<section id="gallery" class="gallery section-bg">

  <div class="container" data-aos="fade-up">
    <div class="section-title">
      <h2>Gallerie</h2>
      <p>Ein paar Impressionen des Ferienhauses</p>
    </div>
    <div class="tm-img-gallery gallery-one">
    <!-- Gallery One pop up connected with JS code below -->
      @foreach ($pictures as $picture)
        <div class="grid-item">
          <figure class="effect-sadie">
              <img src="{{$picture->cropped_photo->file}}" alt="Image" class="img-fluid tm-img">
              <figcaption>
                  <a href="{{$picture->photo->file}}"></a>
              </figcaption>           
          </figure>
        </div>                         
      @endforeach                                                 
    </div>               
  </div> 
</section>