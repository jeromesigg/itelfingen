<section id="contact" class="contact section-bg">
  <div class="px-4 mx-auto max-w-(--breakpoint-2xl) lg:px-6 space-y-8 lg:grid lg:grid-cols-2 sm:gap-6 xl:gap-10 lg:space-y-0" >
    <div class="p-6">
      <div class="section-title">
        <p>So findest du uns:</p>
      </div>
      <div >
        <iframe style="border:0; width: 100%; height: 350px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d605.6247635940863!2d8.473077610557123!3d47.11300787321241!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47855566fbd6fd7f%3A0xf9c4a22f086c0f22!2sItelfingen%203%2C%206344%20Meierskappel!5e1!3m2!1sde!2sch!4v1613503423612!5m2!1sde!2sch" frameborder="0" allowfullscreen></iframe>
      </div>
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
      </div>
    </div>
    <div class="p-6">
      <div class="section-title">
        <p>Schreib uns</p>
      </div>
      @if (session()->has('success_contact'))
          <div id="toast-simple" class="fixed flex items-center w-full max-w-sm p-4 text-body text-fg-success-strong bg-success-soft rounded-base shadow-xs border border-success-subtle top-5 inset-e-5" role="alert">
            <svg class="w-5 h-5 text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 18-7 3 7-18 7 18-7-3Zm0 0v-5"/></svg>
            <div class="ms-2.5 text-sm border-s border-default ps-3.5">{!! session()->get('success_contact') !!}</div>
            <button type="button" class="ms-auto flex items-center justify-center text-body hover:text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded text-sm h-8 w-8 focus:outline-none" data-dismiss-target="#toast-simple" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
            </button>
          </div>
      @endif
      @if ($errors->contact->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->contact->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
      <p class="mb-3 text-lg">
        Fragen, Anregungen? Wir freuen uns über Deine Nachricht. <br>
        Solltest du Fragen zu einer Buchung (Verfügbarkeit, Preis) haben, erstelle bitte direkt eine <a href="/#booking" class="text-red-300 hover:underline">Buchungsanfrage</a>.
      </p>
      
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
          <x-forms.button type="submit" class="btn btn-frontpage bg-gladegreen hover:bg-gladegreen">
            Sende Nachricht
          </x-forms.button>
        </x-forms.container>
      </x-forms.form>
    </div>
  </div>
</section>
