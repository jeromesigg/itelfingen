{{-- Fehlerseiten: bewusst ohne Vite, Datenbank und Session, damit sie auch bei einem Serverfehler funktionieren --}}
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') – Ferienhaus Itelfingen</title>
        <style>
            body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f9fafb; color: #1f2937; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
            main { max-width: 32rem; margin: 1.5rem; padding: 2rem; background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem; text-align: center; }
            img { height: 3rem; margin-bottom: 1.5rem; }
            .code { margin: 0; font-size: 0.875rem; font-weight: 600; letter-spacing: 0.05em; color: #657F61; }
            h1 { margin: 0.25rem 0 0.75rem; font-size: 1.75rem; }
            p { margin: 0 0 1.5rem; line-height: 1.6; color: #4b5563; }
            .buttons { display: flex; flex-wrap: wrap; gap: 0.75rem; justify-content: center; }
            a.button { display: inline-block; padding: 0.625rem 1.25rem; border-radius: 0.5rem; font-weight: 500; text-decoration: none; }
            .primary { background: #657F61; color: #fff; }
            .secondary { border: 1px solid #d1d5db; color: #1f2937; }
        </style>
    </head>
    <body>
        <main>
            <img src="/img/logo.png" alt="Ferienhaus Itelfingen">
            <p class="code">Fehler @yield('code')</p>
            <h1>@yield('title')</h1>
            <p>@yield('message')</p>
            <div class="buttons">
                @hasSection('back')
                    <a class="button primary" href="javascript:history.back()">Zurück</a>
                    <a class="button secondary" href="/">Zur Startseite</a>
                @else
                    <a class="button primary" href="/">Zur Startseite</a>
                @endif
            </div>
        </main>
    </body>
</html>
