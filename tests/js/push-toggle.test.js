import { beforeEach, describe, expect, it, vi } from 'vitest';

const config = {
    vapidPublicKey: 'BNcRdreALRFXTkOOUHK1EtK2wtaz5Ry4YfYCA_0QTpQtUbVlUls0VJXg7A8u-Ts1XbjhazAkj7I99e8QcYP7DkM',
    subscribeUrl: '/admin/push-subscriptions',
    unsubscribeUrl: '/admin/push-subscriptions',
    testUrl: '/admin/push-subscriptions/test',
};

let fetchMock;
let pushManager;

/** Abo, wie es der Browser liefert (PushSubscription). */
function browserSubscription(endpoint) {
    const subscription = {
        endpoint,
        toJSON: () => ({ endpoint, expirationTime: null, keys: { p256dh: 'p256dh-key', auth: 'auth-key' } }),
        unsubscribe: vi.fn(async () => {
            pushManager.current = null;
            return true;
        }),
    };
    return subscription;
}

/** Profil-Abschnitt wie in admin/users/edit.blade.php aufbauen und das Modul frisch laden. */
async function setup({ permission = 'default', subscribed = false, serverOk = true } = {}) {
    document.head.innerHTML = '<meta name="csrf-token" content="csrf-123">';
    document.body.innerHTML = `
        <output data-push-status>Wird geprüft …</output>
        <button data-action="push-subscribe" hidden>Einschalten</button>
        <button data-action="push-unsubscribe" hidden>Ausschalten</button>
        <button data-action="push-test" hidden>Testmeldung senden</button>
        <script type="application/json" id="push-toggle-config">${JSON.stringify(config)}</script>`;

    pushManager = {
        current: subscribed ? browserSubscription('https://push.example.com/alt') : null,
        getSubscription: vi.fn(async () => pushManager.current),
        subscribe: vi.fn(async () => {
            pushManager.current = browserSubscription('https://push.example.com/neu');
            return pushManager.current;
        }),
    };
    vi.stubGlobal('PushManager', { supportedContentEncodings: ['aes128gcm', 'aesgcm'] });
    vi.stubGlobal('Notification', { permission, requestPermission: vi.fn(async () => 'granted') });
    Object.defineProperty(navigator, 'serviceWorker', { value: { ready: Promise.resolve({ pushManager }) }, configurable: true });
    fetchMock = vi.fn(async () => ({ ok: serverOk, status: serverOk ? 200 : 500 }));
    vi.stubGlobal('fetch', fetchMock);

    vi.resetModules();
    await import('../../resources/js/admin/push-toggle.js');
    await vi.waitFor(() => expect(status()).not.toBe('Wird geprüft …'));
}

const status = () => document.querySelector('[data-push-status]').textContent;
const button = (action) => document.querySelector(`[data-action="push-${action}"]`);

async function click(action) {
    const before = status();
    button(action).click();
    await vi.waitFor(() => expect(status()).not.toBe(before));
}

describe('push-toggle', () => {
    beforeEach(() => {
        vi.unstubAllGlobals();
    });

    it('bietet ohne Abo nur Einschalten an', async () => {
        await setup();

        expect(status()).toBe('Auf diesem Gerät ausgeschaltet.');
        expect(button('subscribe').hidden).toBe(false);
        expect(button('unsubscribe').hidden).toBe(true);
        expect(button('test').hidden).toBe(true);
    });

    it('meldet das Gerät beim Server an', async () => {
        await setup();

        await click('subscribe');

        expect(fetchMock).toHaveBeenCalledWith('/admin/push-subscriptions', expect.objectContaining({ method: 'POST' }));
        const [, request] = fetchMock.mock.calls[0];
        expect(request.headers['X-CSRF-TOKEN']).toBe('csrf-123');
        expect(JSON.parse(request.body)).toEqual({
            endpoint: 'https://push.example.com/neu',
            expirationTime: null,
            keys: { p256dh: 'p256dh-key', auth: 'auth-key' },
            contentEncoding: 'aes128gcm',
        });
        expect(status()).toBe('Auf diesem Gerät eingeschaltet.');
        expect(button('test').hidden).toBe(false);
    });

    it('nimmt das Abo im Browser zurück, wenn der Server es nicht speichert', async () => {
        await setup({ serverOk: false });

        await click('subscribe');

        expect(pushManager.current).toBeNull();
        expect(status()).toContain('nicht geklappt');
    });

    it('meldet das Gerät beim Server ab', async () => {
        await setup({ permission: 'granted', subscribed: true });

        await click('unsubscribe');

        expect(fetchMock).toHaveBeenCalledWith('/admin/push-subscriptions', expect.objectContaining({
            method: 'DELETE',
            body: JSON.stringify({ endpoint: 'https://push.example.com/alt' }),
        }));
        expect(status()).toBe('Auf diesem Gerät ausgeschaltet.');
    });
});
