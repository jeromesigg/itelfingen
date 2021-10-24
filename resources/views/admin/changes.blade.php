@extends('layouts.admin')

@section('content')
    <section class="dashboard-header section-padding section-features shadow">
        <div class="container-fluid">
            <div id="features-wrapper" class="card features">
                <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 display"><a data-toggle="collapse" data-parent="#features-wrapper" href="#features-box" aria-expanded="true" aria-controls="features-box">Änderungen und Anpassungen</a></h2><a data-toggle="collapse" data-parent="#features-wrapper" href="#features-box" aria-expanded="true" aria-controls="features-box"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="features-box" role="tabpanel" class="card-body collapse show">
                    <h3>V1.0</h3>
                    <ul>
                        <li>1. Version der Seite mit Kalender</li>
                    </ul>
                    <h3>V2.0</h3>
                    <ul>
                        <li>Neues Design</li>
                    </ul>
                    <h3>V2.1</h3>
                    <ul>
                        <li>API mit Bexio über Angebote und Rechnungen.</li>
                        <li>Rabatt eingebaut für Buchungen.</li>
                        <li>Anzahl Parkplätze verrechenbar.</li>
                        <li>Verwalter Rolle eingeführt.</li>
                    </ul>
                    <h3>V2.2</h3>
                    <ul>
                        <li>FAQ</li>
                        <li>kleinere Layout-Anpassungen</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="dashboard-header section-padding section-features shadow">
        <div class="container-fluid">
            <div id="roadmap-wrapper" class="card roadmap">
                <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 display"><a data-toggle="collapse" data-parent="#roadmap-wrapper" href="#roadmap-box" aria-expanded="true" aria-controls="roadmap-box">Geplante Änderungen</a></h2><a data-toggle="collapse" data-parent="#roadmap-wrapper" href="#roadmap-box" aria-expanded="true" aria-controls="roadmap-box"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="roadmap-box" role="tabpanel" class="card-body collapse show">
                    <h3>Nächste Versionen</h3>
                    <ul>
                        <li>Tagesaufenthalte abbilden</li>
                        <li>Login für Vorstand</li>
                        <li>ics-Datei mit Mail versenden</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

@endsection