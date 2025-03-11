@extends('layouts.admin')

@section('content')
    <div class="block p-6 mb-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-4xl font-bold dark:text-white mb-5">Änderungen und Anpassungen</h2>
        <ol class="relative border-s border-gray-200 dark:border-gray-700">         
            <li class="mb-10 ms-6">     
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                    </svg>
                </span>
                <h3 class="text-3xl font-bold dark:text-white">V2.5</h3>
                <ul class="max-w-md space-y-1 text-gray-700 list-none list-inside dark:text-gray-400">
                    <li>
                        <h4 class="text-2xl font-bold dark:text-white">Features:</h4>
                        <ul class="max-w-md space-y-1 text-gray-700 list-disc list-inside dark:text-gray-400">
                            <li>Bessere Übersicht der Buchungen</li>
                            <li>Mehr Informationen an Slack übermittelt</li>
                            <li>ics-Kalender-Anfrage in Buchungsbestätigung</li>
                            <li>Buchungsübersicht inkl. csv-Download</li>
                            <li>"Impressum" und "Über uns" aufgetrennt</li>
                            <li>Footer mit Impressum, Über uns, FAQ und Login vereinheitlicht</li>
                            <li>Möglichkeit eigener Text für Mail-Verkehr</li>
                            <li>Early Check-In und Late Check-Out werden im Kalender angezeigt</li>
                        </ul>
                    </li>
                    <li>
                        <h4 class="text-2xl font-bold dark:text-white">Bug-Fix:</h4>
                        <ul class="max-w-md space-y-1 text-gray-700 list-disc list-inside dark:text-gray-400">
                            <li>FAQ ohne übermässige Abstände</li>
                            <li>Bewerbungen ohne Anrede und mit Grund als Pflicht</li>
                            <li>Nur zukünftige Buchungen werden auf Homepage geladen</li>
                            <li>50% Last-Minute Rabatt</li>
                            <li>Anpassung HotTub Text</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="mb-10 ms-6"> 
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                    </svg>
                </span>
                <h3 class="text-3xl font-bold dark:text-white">V2.4</h3>
                <ul class="max-w-md space-y-1 text-gray-700 list-none list-inside dark:text-gray-400">
                    <li>
                        <h4 class="text-2xl font-bold dark:text-white">Features:</h4>
                        <ul class="max-w-md space-y-1 text-gray-700 list-disc list-inside dark:text-gray-400">
                            <li>Alle E-Mails aus der Applikation</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="mb-10 ms-6"> 
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                    </svg>
                </span>
                <h3 class="text-3xl font-bold dark:text-white">V2.3</h3>
                <ul class="max-w-md space-y-1 text-gray-700 list-none list-inside dark:text-gray-400">
                    <li>
                        <h4 class="text-2xl font-bold dark:text-white">Features:</h4>
                        <ul class="max-w-md space-y-1 text-gray-700 list-disc list-inside dark:text-gray-400">
                            <li>Tagesaufenthalte abbilden</li>
                            <li>Login für Vorstand</li>
                        </ul>
                    </li>
                </ul>     
            </li>               
            <li class="mb-10 ms-6"> 
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                    </svg>
                </span>
                <h3 class="text-3xl font-bold dark:text-white">V2.2</h3>
                <ul class="max-w-md space-y-1 text-gray-700 list-none list-inside dark:text-gray-400">
                    <li>
                        <h4 class="text-2xl font-bold dark:text-white">Features:</h4>
                        <ul class="max-w-md space-y-1 text-gray-700 list-disc list-inside dark:text-gray-400">
                            <li>FAQ</li>
                            <li>kleinere Layout-Anpassungen</li>
                        </ul>
                    </li>
                </ul>     
            </li>           
            <li class="mb-10 ms-6"> 
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                    </svg>
                </span>
                <h3 class="text-3xl font-bold dark:text-white">V2.1</h3>
                <ul class="max-w-md space-y-1 text-gray-700 list-none list-inside dark:text-gray-400">
                    <li>
                        <h4 class="text-2xl font-bold dark:text-white">Features:</h4>
                        <ul class="max-w-md space-y-1 text-gray-700 list-disc list-inside dark:text-gray-400">
                            <li>API mit Bexio über Angebote und Rechnungen.</li>
                            <li>Rabatt eingebaut für Buchungen.</li>
                            <li>Anzahl Parkplätze verrechenbar.</li>
                            <li>Verwalter Rolle eingeführt.</li>
                        </ul>
                    </li>
                </ul>     
            </li>          
            <li class="mb-10 ms-6"> 
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                    </svg>
                </span>
                <h3 class="text-3xl font-bold dark:text-white">V2.0</h3>
                <ul class="max-w-md space-y-1 text-gray-700 list-none list-inside dark:text-gray-400">
                    <li>
                        <h4 class="text-2xl font-bold dark:text-white">Features:</h4>
                        <ul class="max-w-md space-y-1 text-gray-700 list-disc list-inside dark:text-gray-400">
                            <li>Neues Design</li>
                        </ul>
                    </li>
                </ul>     
            </li>     
            <li class="mb-10 ms-6"> 
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                    </svg>
                </span>
                <h3 class="text-3xl font-bold dark:text-white">V1.0</h3>
                <ul class="max-w-md space-y-1 text-gray-700 list-none list-inside dark:text-gray-400">
                    <li>
                        <h4 class="text-2xl font-bold dark:text-white">Features:</h4>
                        <ul class="max-w-md space-y-1 text-gray-700 list-disc list-inside dark:text-gray-400">
                            <li>1. Version der Seite mit Kalender</li>
                        </ul>
                    </li>
                </ul>     
            </li>   
        </ol>
    </div>
    <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-4xl font-bold dark:text-white mb-5">Geplante Änderungen</h2>
        <ul class="max-w-md space-y-1 text-gray-700 list-disc list-inside dark:text-gray-400">
            <li>Buchungs-Rabatt an Wochen-Tagen</li>
            <li>Facebook Conversion API</li>
            <li>Testing</li>
        </ul>
    </div>

@endsection
