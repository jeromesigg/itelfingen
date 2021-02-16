<section id="gallery" class="gallery  section-bg">

  <div class="container" data-aos="fade-up">
    <div class="section-title">
      <h2>Gallerie</h2>
      <p>Ein paar Impressionen des Ferienhauses</p>
    </div>

  <div class="row" data-aos="fade-in">
    <div class="col-lg-12 d-flex justify-content-center">
      <ul id="portfolio-flters">
        @foreach ($albums as $album)
          <li data-filter=".filter-{{$album['internal_name']}}" class={{$album['default_album'] ? "filter-active" : ""}}>{{$album['name']}}</li>
        @endforeach
      </ul>
    </div>
  </div>
  <div class="row portfolio-container" data-aos="fade-up" data-aos-delay="100">
    @foreach ($pictures as $picture)
        <div class="col-lg-3 col-md-4 portfolio-item filter-{{$picture->album->internal_name}}">
            <div class="gallery-item">
                <a href="{{$picture->photo->file}}" class="venobox" data-gall="gallery-item">
                  <img src="{{$picture->photo->file}}" alt="" class="img-fluid">
                </a>
            </div>
        </div>   
    @endforeach
  </div>

  </section>