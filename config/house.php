<?php

/*
 * Feste Angaben zum Ferienhaus, die in E-Mails und im Kundenbereich erscheinen.
 * Bei Änderungen (neuer Abfallcode, anderer Vorstand) nur hier anpassen.
 */
return [
    'waste_code' => '4315',

    'checkin' => ['default' => '15:00', 'early' => '10:00'],
    'checkout' => ['default' => '10:00', 'late' => '17:00'],

    'quiet_hours' => '22:00',

    // Erreichbar bei Notfällen während des Aufenthalts
    'emergency_contacts' => [
        ['name' => 'Lukas Affolter', 'phone' => '079 810 30 05'],
        ['name' => 'Jérôme Sigg', 'phone' => '079 587 56 51'],
        ['name' => 'Matthias Bächler', 'phone' => '079 386 73 20'],
    ],

    'feedback_url' => 'https://forms.gle/RMWyPzs8wauakQam9',
    'google_review_url' => 'https://maps.app.goo.gl/nDkcMkAQLsQPfp557',
    'instagram_url' => 'https://www.instagram.com/itelfingen/',
];
