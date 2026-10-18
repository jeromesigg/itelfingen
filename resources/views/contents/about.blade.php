<section id="about" class="about section">

    <div class="px-4 mx-auto max-w-(--breakpoint-2xl) lg:px-6">

        @if($homepage['green_text'])
            <x-alert type="success" class="mb-8">
                <p>
                    {{$homepage['green_text']}}
                </p>
            </x-alert>
        @endif

        <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2 lg:gap-16">
            <div class="content">
                <div class="section-title">
                    <h2>Ferientraum direkt am Zugersee</h2>
                </div>
                <p class="text-lg leading-relaxed sm:text-xl">
                    Eingebettet in die Landwirtschaftszone über dem Zugersee lädt dieses Haus zum Verweilen ein.
                    Wer die Natur liebt, ist hier am richtigen Ort. Lass deine Seele baumeln – einatmen, ausatmen, geniessen!
                </p>
            </div>

            <div class="about-img mx-auto lg:mx-0">
                <img src="images/about-bg.webp" alt="Zimmer mit Holzdecke und Blick ins Grüne" loading="lazy">
            </div>
        </div>
    </div>
  </section><!-- End About Section -->
