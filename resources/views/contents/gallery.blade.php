<section id="gallery" class="gallery section">

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="section-title">
      <h2>Gallerie</h2>
      <p>Ein paar Impressionen des Ferienhauses</p>
    </div>      
    <div class="tm-img-gallery gallery" itemscope itemtype="http://schema.org/ImageGallery">
      @foreach ($pictures as $key=>$picture)
      <div class="grid-item">
        <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
          <a href="{{$picture->photo->file}}" itemprop="contentUrl" data-index="{{$key}}">
          <img src="{{$picture->cropped_photo->file}}" itemprop="thumbnail" alt="Image" class="img-fluid tm-img">
          </a>
        </figure>     
      </div>             
      @endforeach    
    </div>
    <h3 style="margin-top: 20px">
      Mehr Bilder gibt es auf unserer <a href="https://www.instagram.com/itelfingen/" target="blank">Instagram-Seite</a>
    </h3>
  </div> 
  
</section>

