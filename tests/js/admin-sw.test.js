import { beforeEach, describe, expect, it, vi } from 'vitest';

let handlers;
let worker;

/** Service Worker (public/admin-sw.js) in einem nachgebauten Umfeld laden; die Handler landen in handlers. */
async function loadWorker({ windows = [] } = {}) {
    handlers = {};
    worker = {
        location: { origin: 'https://www.itelfingen.ch' },
        addEventListener: (type, handler) => {
            handlers[type] = handler;
        },
        registration: { showNotification: vi.fn(async () => {}) },
        clients: { matchAll: vi.fn(async () => windows), openWindow: vi.fn(async () => {}) },
    };
    vi.stubGlobal('self', worker);
    vi.resetModules();
    await import('../../public/admin-sw.js');
}

/** Ereignis mit waitUntil, das auf das Versprechen wartet. */
function workerEvent(properties) {
    const pending = [];
    return {
        ...properties,
        waitUntil: (promise) => pending.push(promise),
        done: () => Promise.all(pending),
    };
}

describe('admin-sw push', () => {
    beforeEach(() => {
        vi.unstubAllGlobals();
    });

    it('zeigt Titel, Text und Link aus der Push-Nachricht an', async () => {
        await loadWorker();
        // Gleicher Aufbau wie App\Notifications\AdminPushNotification::toWebPush()
        const message = { title: 'Neue Buchungsanfrage', body: 'Hans Muster', icon: '/img/pwa/icon-192.png', data: { url: '/admin/events/1/edit' } };
        const event = workerEvent({ data: { json: () => message } });

        handlers.push(event);
        await event.done();

        expect(worker.registration.showNotification).toHaveBeenCalledWith('Neue Buchungsanfrage', {
            body: 'Hans Muster',
            icon: '/img/pwa/icon-192.png',
            data: { url: '/admin/events/1/edit' },
        });
    });

    it('öffnet den Link in einem neuen Fenster, wenn die App nicht offen ist', async () => {
        await loadWorker();
        const event = workerEvent({ notification: { close: vi.fn(), data: { url: '/admin/events/1/edit' } } });

        handlers.notificationclick(event);
        await event.done();

        expect(event.notification.close).toHaveBeenCalled();
        expect(worker.clients.openWindow).toHaveBeenCalledWith('https://www.itelfingen.ch/admin/events/1/edit');
    });

    it('verwendet das offene Fenster der App weiter', async () => {
        const appWindow = { focus: vi.fn(async () => {}), navigate: vi.fn(async () => {}) };
        await loadWorker({ windows: [appWindow] });
        const event = workerEvent({ notification: { close: vi.fn(), data: { url: '/admin/contacts' } } });

        handlers.notificationclick(event);
        await event.done();

        expect(appWindow.focus).toHaveBeenCalled();
        expect(appWindow.navigate).toHaveBeenCalledWith('https://www.itelfingen.ch/admin/contacts');
        expect(worker.clients.openWindow).not.toHaveBeenCalled();
    });
});
