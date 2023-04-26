<p align="center"><img src="https://itelfingen.ch/img/logo.png" width="400"></p>
<p align="center"><img src="https://itelfingen.ch/images/hero-bg.webp"></p>

## Buchungstool für Ferienhäuser

Die Webseite bietet die Möglichkeit, das Ferienhaus zu präsentieren, Buchungen und Kontaktanfragen zu erstellen und diese auch zu Administrieren

## Lokale Installation

Das Tool ist ein PHP-Projekt basiernd auf dem Framework [Laravel](https://laravel.com/). Um es lokal auszuführen brauchst du einen [Docker Container](https://docs.docker.com/).

Um das Tool lokal bei dir benutzen zu können musst du den Quellcode herunterladen und mittels [Laravel Sail](https://laravel.com/docs/9.x/sail) starten:

```bash
# clone the GitRepo
git clone https://github.com/jeromesigg/itelfingen
cd itelfingen

# install the dependencies
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
    
cp .env.example .env

# launch the application
./vendor/bin/sail up

# initialize the database
./vendor/bin/sail artisan migrate --seed
```

Anschliessend kannst du deine Webseite unter [http://localhost](http://localhost) aufrufen.
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
