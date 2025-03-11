<section id="contact" class="contact section-bg">
  <div class="container">

    <div class="section-title">
      <p>Schreib uns</p>
    </div>
    <p class="mb-3 text-lg md:text-xl">
      Fragen, Anregungen? Wir freuen uns über Deine Nachricht.
    </p>
  </div>
  @if ($errors->contact->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->contact->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif
  @if (session()->has('success_contact'))
      <div class="alert alert-dismissable alert-success">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
          <strong>
              {!! session()->get('success_contact') !!}
          </strong>
      </div>
  @endif

  <div >
    <iframe style="border:0; width: 100%; height: 350px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d605.6247635940863!2d8.473077610557123!3d47.11300787321241!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47855566fbd6fd7f%3A0xf9c4a22f086c0f22!2sItelfingen%203%2C%206344%20Meierskappel!5e1!3m2!1sde!2sch!4v1613503423612!5m2!1sde!2sch" frameborder="0" allowfullscreen></iframe>
  </div>
  <div class="container" >
    <div class="row mt-5">
      <div class="col-lg-4">
        <div class="info">
          <div class="address">
            <i>
              <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M17.8 13.938h-.011a7 7 0 1 0-11.464.144h-.016l.14.171c.1.127.2.251.3.371L12 21l5.13-6.248c.194-.209.374-.429.54-.659l.13-.155Z"/>
              </svg>
            </i>
            <h4>Hausadresse:</h4>
            <p>{!! nl2br($homepage->address) !!}</p>
          </div>
          {{-- <div class="email">
            <i class="icofont-envelope"></i>
            <h4>Email:</h4>
            <p>{{$homepage->mail}}</p>
          </div> --}}
          {{-- <div class="phone">
            <i class="icofont-phone"></i>
            <h4>Tel. P (abends):</h4>
            <p>{{$homepage->phone}}</p>
          </div> --}}
        </div>
      </div>

      <div class="col-lg-8 mt-5 mt-lg-0">
        <x-forms.form :action="route('contacts.store')" id='contact_form'>
          <x-honeypot />
          <div class="form-row">
            <x-forms.container class="col-md-6">
                <x-forms.text label="Name:" name="name" required=true />
            </x-forms.container>
            <x-forms.container class="col-md-6">
                <x-forms.text label="Email:" name="email" type="email" required=true />
            </x-forms.container>
          </div>
          <x-forms.container>
              <x-forms.text label="Betreff:" name="subject" required=true />
          </x-forms.container>
          <x-forms.container>
              <x-forms.textarea label="Nachricht:" name="content" required=true rows="8"/>
          </x-forms.container>
          <x-forms.container>
            <x-forms.button type="submit" class="btn btn-frontpage bg-gladegreen">
              Sende Nachricht
            </x-forms.button>
          </x-forms.container>
        </x-forms.form>
      </div>
    </div>
  </div>
</section>
