@extends('layouts.admin')
@section('content')

    <div>
        <div class="container mx-auto px-4">
            <header>
                <h3 class="text-3xl font-bold dark:text-white">Benutzer bearbeiten</h3>
            </header>
            <div class="row">
                 <div class="col-sm-9">
                    <x-forms.form :action="route('users.update', $user)" autocomplete="off" method="PATCH" :model="$user">
                        <x-forms.container>
                            <x-forms.text label="Username:" name="username" required=true/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Name:" name="fullname"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.text label="Handy-Nummer:" name="phone"/>
                        </x-forms.container>
                        @if (Auth::user()->isAdmin())
                        <x-forms.container>
                            <x-forms.select label="Rolle:" name="role_id" required=true :collection="$roles"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.select label="Archiv Status:" name="archive_status_id" required=true :collection="$archive_statuses"/>
                        </x-forms.container>
                        @endif
                        <x-forms.container>
                            <x-forms.text label="Passwort (leer lassen = unverändert, mindestens 10 Zeichen):" name="password" type="password"/>
                        </x-forms.container>
                        <x-forms.container>
                            <x-forms.button type="submit" class="btn btn-primary">
                                Benutzer Updaten
                            </x-forms.button>
                        </x-forms.container>
                    </x-forms.form>
                </div>
            </div>
            <div class="row">
                @include('includes.form_error')
            </div>
            @if ($user->is(Auth::user()) && $user->canManageBookings())
                @php
                    $pushConfig = [
                        'vapidPublicKey' => config('webpush.vapid.public_key'),
                        'subscribeUrl' => route('admin.push-subscriptions.store'),
                        'unsubscribeUrl' => route('admin.push-subscriptions.destroy'),
                        'testUrl' => route('admin.push-subscriptions.test'),
                    ];
                @endphp
                <section class="mt-8 max-w-xl" aria-labelledby="push-heading">
                    <h4 id="push-heading" class="text-xl font-bold dark:text-white">Push-Meldungen</h4>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Meldung auf diesem Gerät bei neuen Buchungsanfragen, Kontaktanfragen und Bewerbungen. Gilt nur für das Gerät, auf dem du es einschaltest.</p>
                    @if ($pushConfig['vapidPublicKey'])
                        <output data-push-status class="mt-3 block text-sm dark:text-white">Wird geprüft …</output>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <x-forms.button data-action="push-subscribe" hidden>Einschalten</x-forms.button>
                            <x-forms.button data-action="push-unsubscribe" variant="danger" hidden>Ausschalten</x-forms.button>
                            <x-forms.button data-action="push-test" hidden>Testmeldung senden</x-forms.button>
                        </div>
                    @else
                        <p class="mt-3 text-sm dark:text-white">Auf dem Server noch nicht eingerichtet: VAPID-Schlüssel fehlen (php artisan webpush:vapid).</p>
                    @endif
                </section>
                @if ($pushConfig['vapidPublicKey'])
                    <script type="application/json" id="push-toggle-config">@json($pushConfig)</script>
                    @vite('resources/js/admin/push-toggle.js')
                @endif
            @endif
        </div>
    </div>
@endsection
