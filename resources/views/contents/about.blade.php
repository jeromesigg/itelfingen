<section id="about" class="about section">

    <div class="px-4 mx-auto max-w-screen-2xl lg:px-6">

        @if($homepage['green_text'])
            <div class="alert alert-dismissable alert-success section-title">
                <p>
                    {{$homepage['green_text']}}
                </p>
            </div>
            <br>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 order-1 order-lg-2">
                <div class="about-img">
                    <img src="images/about-bg.webp" alt="">
                </div>
            </div>

            <div class="pt-4 pt-lg-0 order-2 order-lg-1 content">

                <div class="section-title">
                    <p>Ferientraum direkt am Zugersee</p>
                </div>
                <p>
                    Eingebettet in die Landwirtschaftszone über dem Zugersee lädt dieses Haus zum Verweilen ein.
                    Wer die Natur liebt ist hier am richtigen Ort. Lass deine Seele baumeln – einatmen, ausatmen, geniessen!
                </p>
            </div>
        </div>
    </div>
  </section><!-- End About Section -->
