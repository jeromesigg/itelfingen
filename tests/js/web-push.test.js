import { describe, expect, it } from 'vitest';
import { applicationServerKey, contentEncoding, pushState } from '../../resources/js/libs/web-push.js';

const device = { supported: true, ios: false, standalone: false, permission: 'default', subscribed: false };

describe('pushState', () => {
    it('bietet Einschalten an, solange das Gerät kein Abo hat', () => {
        expect(pushState(device)).toEqual({ message: 'Auf diesem Gerät ausgeschaltet.', action: 'subscribe' });
    });

    it('bietet Ausschalten an, wenn das Gerät ein Abo hat', () => {
        expect(pushState({ ...device, permission: 'granted', subscribed: true }).action).toBe('unsubscribe');
    });

    it('erklärt blockierte Meldungen statt einen Knopf zu zeigen', () => {
        const state = pushState({ ...device, permission: 'denied' });

        expect(state.action).toBeNull();
        expect(state.message).toContain('blockiert');
    });

    it('verweist auf dem iPhone in Safari auf die installierte App', () => {
        const state = pushState({ ...device, supported: false, ios: true });

        expect(state.action).toBeNull();
        expect(state.message).toContain('Zum Home-Bildschirm');
    });

    it('meldet fehlende Unterstützung in der installierten iPhone-App vor iOS 16.4', () => {
        expect(pushState({ ...device, supported: false, ios: true, standalone: true }).message).toBe('Dieser Browser unterstützt keine Push-Meldungen.');
    });
});

describe('applicationServerKey', () => {
    it('wandelt base64url ohne Auffüllzeichen in Bytes um', () => {
        // "-_8" in base64url entspricht "+/8" in base64: Bytes 0xFB 0xFF
        expect(Array.from(applicationServerKey('-_8'))).toEqual([0xfb, 0xff]);
    });

    it('liefert für einen VAPID-Schlüssel 65 Bytes', () => {
        const key = 'BNcRdreALRFXTkOOUHK1EtK2wtaz5Ry4YfYCA_0QTpQtUbVlUls0VJXg7A8u-Ts1XbjhazAkj7I99e8QcYP7DkM';

        expect(applicationServerKey(key)).toHaveLength(65);
    });
});

describe('contentEncoding', () => {
    it.each([
        [['aes128gcm', 'aesgcm'], 'aes128gcm'],
        [['aesgcm'], 'aesgcm'],
        [undefined, 'aes128gcm'],
    ])('%j → %s', (supported, expected) => {
        expect(contentEncoding(supported)).toBe(expected);
    });
});
