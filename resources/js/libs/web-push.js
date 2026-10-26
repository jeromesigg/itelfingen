/**
 * Push-Meldungen im Browser: Zustand für den Schalter im Profil (resources/js/admin/push-toggle.js).
 * Ohne DOM und ohne Browser-APIs, damit testbar (tests/js/web-push.test.js).
 */

/**
 * Text und mögliche Aktion des Schalters.
 * Auf dem iPhone gibt es Push nur in der installierten App (ab iOS 16.4), nicht in Safari.
 *
 * @param {{ supported: boolean, ios: boolean, standalone: boolean, permission: string, subscribed: boolean }} device
 * @returns {{ message: string, action: 'subscribe'|'unsubscribe'|null }}
 */
export function pushState({ supported, ios, standalone, permission, subscribed }) {
    if (!supported) {
        return {
            message: ios && !standalone
                ? 'Auf dem iPhone gehen Push-Meldungen nur in der App: in Safari «Teilen» → «Zum Home-Bildschirm», dann die App von dort öffnen.'
                : 'Dieser Browser unterstützt keine Push-Meldungen.',
            action: null,
        };
    }
    if (permission === 'denied') {
        return {
            message: 'Meldungen sind für diese Seite blockiert. Bitte in den Einstellungen des Browsers bzw. der App erlauben.',
            action: null,
        };
    }
    if (subscribed) {
        return { message: 'Auf diesem Gerät eingeschaltet.', action: 'unsubscribe' };
    }
    return { message: 'Auf diesem Gerät ausgeschaltet.', action: 'subscribe' };
}

/**
 * Öffentlicher VAPID-Schlüssel (base64url) als Bytes für pushManager.subscribe().
 *
 * @param {string} key
 * @returns {Uint8Array}
 */
export function applicationServerKey(key) {
    const base64 = (key + '='.repeat((4 - (key.length % 4)) % 4)).replace(/-/g, '+').replace(/_/g, '/');
    return Uint8Array.from(atob(base64), (character) => character.charCodeAt(0));
}

/**
 * Verschlüsselung, die der Browser kann; aes128gcm ist der Standard, aesgcm nur für alte Browser.
 *
 * @param {string[]|undefined} supported PushManager.supportedContentEncodings
 * @returns {'aes128gcm'|'aesgcm'}
 */
export function contentEncoding(supported) {
    return !supported || supported.includes('aes128gcm') ? 'aes128gcm' : 'aesgcm';
}
