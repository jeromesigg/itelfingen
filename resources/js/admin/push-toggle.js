/**
 * Schalter "Push-Meldungen" im eigenen Profil (admin/users/edit.blade.php).
 *
 * Konfiguration als JSON in <script type="application/json" id="push-toggle-config">,
 * Buttons über data-action. Der Service Worker ist public/admin-sw.js.
 */
import { applicationServerKey, contentEncoding, pushState } from '../libs/web-push.js';

const configElement = document.getElementById('push-toggle-config');

if (configElement) {
    initPushToggle(JSON.parse(configElement.textContent));
}

function initPushToggle(config) {
    const status = document.querySelector('[data-push-status]');
    const buttons = {
        subscribe: document.querySelector('[data-action="push-subscribe"]'),
        unsubscribe: document.querySelector('[data-action="push-unsubscribe"]'),
        test: document.querySelector('[data-action="push-test"]'),
    };
    const supported = 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;

    async function currentSubscription() {
        const registration = await navigator.serviceWorker.ready;
        return registration.pushManager.getSubscription();
    }

    async function render(error = null) {
        const state = pushState({
            supported,
            ios: /iPhone|iPad|iPod/.test(navigator.userAgent),
            standalone: window.matchMedia('(display-mode: standalone)').matches,
            permission: supported ? Notification.permission : 'default',
            subscribed: supported && (await currentSubscription()) !== null,
        });
        status.textContent = error ?? state.message;
        buttons.subscribe.hidden = state.action !== 'subscribe';
        buttons.unsubscribe.hidden = state.action !== 'unsubscribe';
        buttons.test.hidden = state.action !== 'unsubscribe';
    }

    function send(method, url, body = {}) {
        return fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(body),
        }).then(async (response) => {
            if (!response.ok) {
                // Meldung des Servers (z. B. Antwort des Push-Dienstes) anzeigen, sonst nur den Status
                const { message } = await response.json().catch(() => ({}));
                throw new Error(message ?? `HTTP ${response.status}`);
            }
            return response;
        });
    }

    const actions = {
        async 'push-subscribe'() {
            if ((await Notification.requestPermission()) !== 'granted') {
                return render();
            }
            const registration = await navigator.serviceWorker.ready;
            // Altes Abo (z. B. mit früherem Schlüssel) zuerst entfernen, sonst lehnt der Browser ab
            await (await registration.pushManager.getSubscription())?.unsubscribe();
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: applicationServerKey(config.vapidPublicKey),
            });
            try {
                await send('POST', config.subscribeUrl, {
                    ...subscription.toJSON(),
                    contentEncoding: contentEncoding(PushManager.supportedContentEncodings),
                });
            } catch (error) {
                await subscription.unsubscribe();
                throw error;
            }
            return render();
        },
        async 'push-unsubscribe'() {
            const subscription = await currentSubscription();
            if (subscription) {
                await send('DELETE', config.unsubscribeUrl, { endpoint: subscription.endpoint });
                await subscription.unsubscribe();
            }
            return render();
        },
        async 'push-test'() {
            const subscription = await currentSubscription();
            try {
                const response = await send('POST', config.testUrl, { endpoint: subscription?.endpoint });
                status.textContent = (await response.json()).message;
            } catch (error) {
                status.textContent = error.message;
            }
        },
    };

    Object.values(buttons).forEach((button) => button.addEventListener('click', async () => {
        button.disabled = true;
        try {
            await actions[button.dataset.action]();
        } catch (error) {
            console.error('Push-Meldungen:', error);
            await render(`Das hat nicht geklappt (${error.message}). Bitte Seite neu laden und nochmals versuchen.`);
        } finally {
            button.disabled = false;
        }
    }));

    render().catch((error) => console.error('Push-Meldungen:', error));
}
