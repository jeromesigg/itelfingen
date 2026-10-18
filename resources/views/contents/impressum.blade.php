@extends('layouts.app')

@section('content')

  @include('includes.header')
  <div id="app">

    <main id="main" class="bg-gray-50 dark:bg-gray-900">
      <div class="mx-auto max-w-4xl px-4 pt-10 pb-14 sm:pt-16">
        <h1 class="font-body text-4xl text-gray-900 sm:text-5xl dark:text-white">Impressum</h1>

        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
          <dl class="space-y-5 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <div>
              <dt class="text-sm text-gray-500 dark:text-gray-400">Hausadresse</dt>
              <dd class="text-gray-900 dark:text-white">{!! nl2br(e($homepage->address)) !!}</dd>
            </div>
            <div>
              <dt class="text-sm text-gray-500 dark:text-gray-400">Postadresse</dt>
              <dd class="text-gray-900 dark:text-white">{!! nl2br(e($homepage->postaddress)) !!}</dd>
            </div>
            <div>
              <dt class="text-sm text-gray-500 dark:text-gray-400">E-Mail</dt>
              <dd><a href="mailto:{{ $homepage->mail }}" class="text-gray-900 underline hover:no-underline dark:text-white">{{ $homepage->mail }}</a></dd>
            </div>
          </dl>
          <x-google-map class="bg-white md:col-span-2" />
        </div>

        <h2 class="mt-14 text-2xl font-semibold text-gray-900 sm:text-3xl dark:text-white">Datenschutzerklärung</h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Stand: Oktober 2026, v1.1, gültig für die Website www.itelfingen.ch</p>

        {{-- Rechtstext: Inhalt nur nach Rücksprache ändern --}}
        <div class="mt-6 divide-y divide-gray-200 rounded-lg border border-gray-200 bg-white text-gray-700 dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 [&_h3]:mb-2 [&_h3]:text-lg [&_h3]:font-semibold [&_h3]:text-gray-900 dark:[&_h3]:text-white [&_p]:mb-2 [&_ul]:mb-2 [&_ul]:list-disc [&_ul]:ps-5 [&>div]:p-5">
          <div>
            <h3>Welche Daten wir erheben</h3>
            <p>Wir erheben nur jene Daten, die für den Betrieb dieser Website und die Vermietung des Ferienhauses erforderlich sind:</p>
            <ul>
              <li><strong>Kontaktformular</strong> – Name, E-Mail-Adresse, Nachricht</li>
              <li><strong>Buchungsanfrage</strong> – Name, E-Mail-Adresse, Adresse und gewünschte Reisedaten</li>
            </ul>
          </div>
          <div>
            <h3>Zweck der Datenbearbeitung</h3>
            <p>Ihre Daten verwenden wir ausschliesslich für folgende Zwecke:</p>
            <ul>
              <li>Beantwortung von Anfragen und Abwicklung von Buchungen</li>
              <li>Erfüllung gesetzlicher Pflichten (z. B. Buchführung)</li>
            </ul>
          </div>
          <div>
            <h3>Cookies &amp; Google Maps</h3>
            <p>Diese Website verwendet nur technisch notwendige Cookies, zum Beispiel für das Absenden von Formularen und den Login. Eine Auswertung des Nutzungsverhaltens findet nicht statt.</p>
            <p>Die Karte von Google Maps wird erst geladen, wenn Sie auf «Karte laden» klicken. Dabei werden Daten wie Ihre IP-Adresse an Google übertragen.</p>
            <p>Sie können Cookies jederzeit in Ihren Browsereinstellungen deaktivieren. Wir weisen darauf hin, dass dies die Funktionalität der Website einschränken kann.</p>
          </div>
          <div>
            <h3>Weitergabe an Dritte</h3>
            <p>Ihre Daten werden nicht an Dritte verkauft. Eine Weitergabe erfolgt nur, wenn dies gesetzlich vorgeschrieben ist oder Sie ausdrücklich zugestimmt haben.</p>
          </div>
          <div>
            <h3>Aufbewahrung</h3>
            <p>Wir speichern Ihre Daten nur so lange, wie es für den jeweiligen Zweck notwendig oder gesetzlich vorgeschrieben ist. Buchungsdaten werden gemäss den gesetzlichen Aufbewahrungsfristen (in der Schweiz i. d. R. 10 Jahre) archiviert.</p>
          </div>
          <div>
            <h3>Ihre Rechte</h3>
            <p>Gemäss Schweizer Datenschutzgesetz (DSG) haben Sie das Recht auf:</p>
            <ul>
              <li>Auskunft über Ihre gespeicherten Daten</li>
              <li>Berichtigung unrichtiger Daten</li>
              <li>Löschung Ihrer Daten (soweit keine gesetzlichen Pflichten entgegenstehen)</li>
            </ul>
            <p>Richten Sie Ihre Anfrage per E-Mail an: <a href="mailto:verwalter@itelfingen.ch" class="underline hover:no-underline">verwalter@itelfingen.ch</a></p>
          </div>
          <div>
            <h3>Änderungen</h3>
            <p>Wir behalten uns vor, diese Datenschutzerklärung bei Bedarf anzupassen. Die jeweils aktuelle Version ist auf dieser Seite verfügbar.</p>
          </div>
        </div>
      </div>
    </main><!-- End #main -->
      @include('includes.footer')
  </div>
@endsection
