// Service Worker für den Admin-Bereich (registriert in resources/views/layouts/admin.blade.php).
// Speichert bewusst keine Seiten oder Daten: Buchungen sollen nie veraltet angezeigt werden.
// Nur wenn das Netz fehlt, erscheint die Offline-Seite.
// Bei Änderungen an offline.html die Version erhöhen, damit die neue Fassung geladen wird.
const CACHE = 'admin-offline-v1';
const OFFLINE_URL = '/offline.html';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE)
            .then((cache) => cache.addAll([OFFLINE_URL, '/img/pwa/icon-192.png']))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const keys = await caches.keys();
        await Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key)));
        // Seite parallel zum Start des Service Workers laden, spart Zeit beim Öffnen
        if (self.registration.navigationPreload) {
            await self.registration.navigationPreload.enable();
        }
        await self.clients.claim();
    })());
});

self.addEventListener('fetch', (event) => {
    // Nur Seitenaufrufe; Tabellendaten, Bilder und Skripte gehen unverändert ans Netz
    if (event.request.mode !== 'navigate') {
        return;
    }

    event.respondWith((async () => {
        try {
            return (await event.preloadResponse) || await fetch(event.request);
        } catch {
            return caches.match(OFFLINE_URL);
        }
    })());
});
