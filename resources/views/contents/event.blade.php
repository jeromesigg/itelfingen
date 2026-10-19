<section id="booking" class="calendar section">

    <div class="px-4 mx-auto max-w-(--breakpoint-2xl) lg:px-6">
        <div class="section-title">
            <h2>Jetzt Buchungsanfrage senden</h2>
        </div>
        @if (session()->has('success_event'))
            <div id="toast-simple" class="fixed z-60 flex items-center w-full max-w-sm p-4 text-body text-fg-success-strong bg-success-soft rounded-base shadow-xs border border-success-subtle top-5 inset-e-5" role="alert">
                <svg class="w-5 h-5 text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 18-7 3 7-18 7 18-7-3Zm0 0v-5"/></svg>
                <div class="ms-2.5 text-sm border-s border-default ps-3.5">{!! session()->get('success_event') !!}</div>
                <button type="button" class="ms-auto flex items-center justify-center text-body hover:text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded text-sm h-8 w-8 focus:outline-none" data-dismiss-target="#toast-simple" aria-label="Schliessen">
                    <span class="sr-only">Schliessen</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                </button>
            </div>
        @endif
        @if ($errors->event->any())
            <x-alert>
                <ul class="list-disc list-inside">
                    @foreach ($errors->event->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        @php
            $oldBooking = fn (string $name, mixed $default = '') => \App\View\Components\Forms\OldInput::value($name, $default, 'event');
            $bookingProps = [
                'days' => (object) $booked_days,
                'positions' => $positions->map(fn ($position) => [
                    'bexio_code' => (int) $position->bexio_code,
                    'name' => $position->name,
                    'price' => (float) $position->price,
                ])->values(),
                'discountEnabled' => $discount,
                'showForm' => $errors->event->any(),
                'old' => collect([
                    'firstname', 'name', 'group', 'street', 'house_number', 'zipcode', 'city', 'email', 'telephone',
                    'marketing_comment', 'comment', 'start_date', 'end_date',
                ])->mapWithKeys(fn ($field) => [$field => $oldBooking($field)])
                    ->put('positions', (object) $oldBooking('positions', [])),
            ];
        @endphp

        <div class="hk-reservation hk-reservation__step1" id="reservation_form">
            <div class="hk-reservation__container">
                <x-forms.form :action="route('event.create')" id='calendarform' lang="de-CH" scope="event">
                    <x-honeypot />
                    <div data-vue-component="booking-wizard" data-props="{{ json_encode($bookingProps) }}"></div>
                    <noscript>
                        <x-alert>Für die Buchungsanfrage wird JavaScript benötigt. Alternativ erreichst du uns über das Kontaktformular.</x-alert>
                    </noscript>
                </x-forms.form>
            </div>
        </div>
    </div>

  </section>
