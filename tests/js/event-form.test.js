import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import Swal from 'sweetalert2';

vi.mock('sweetalert2', () => ({
    default: {
        fire: vi.fn(() => Promise.resolve({ isConfirmed: false })),
        showValidationMessage: vi.fn(),
        isLoading: vi.fn(() => false),
    },
}));

// Wie im TestingSeeder bzw. in der echten Preisliste: Pauschale, Erwachsene, Parkplätze (3 inbegriffen)
const positions = [
    { input: 'position_1', bexio_code: 20, price: 290 },
    { input: 'position_2', bexio_code: 120, price: 60 },
    { input: 'position_3', bexio_code: 210, price: 5 },
];

const baseConfig = {
    positions,
    minNights: 0,
    checkinCheckoutPrice: 50,
    calendarUrl: '/admin/events/calendar/7',
    parseUrl: '/admin/events/parse',
    createCodeUrl: '/admin/events/createAPICode',
    eventId: 7,
    importPersonsInput: 'position_2',
    importStatus: 5,
    internStatus: 20,
    cleaningMailAddress: 'putz@example.com',
    cleaningMailSent: false,
    cancelledContractState: 50,
};

let fetchMock;

/** Kalender, wie ihn der Server zeichnet (components/booking-calendar): eine Tabelle mit data-date. */
const serverCalendar = (date) => `<table><tbody><tr><td data-date="${date}">11</td></tr></tbody></table>`;

/** Formular wie in admin/events/create bzw. edit aufbauen und das Modul frisch laden. */
async function setup({ config = {}, values = {}, calendar = '<p>Start und Ende wählen</p>' } = {}) {
    document.body.innerHTML = `
        <form id="main"><input name="_token" value="csrf-123">
            <input id="start_date" type="date"><input id="end_date" type="date">
            <input id="name"><input id="firstname"><input id="group_name"><input id="street"><input id="house_number">
            <input id="plz"><input id="city"><input id="email"><input id="telephone"><input id="foreign_key"><input id="code">
            <select id="event_status_id"><option value="5">Neu</option><option value="20">Eigene</option><option value="10">Bestätigt</option></select>
            <select id="contract_status_id"><option value="5">Offen</option><option value="50">Storniert</option></select>
            <textarea id="comment"></textarea><textarea id="additional_text"></textarea><textarea id="import-text"></textarea>
            <input type="hidden" id="position_1" value="1"><input id="position_2" type="number"><input id="position_3" type="number">
            <input id="total_days" type="number"><input id="discount" type="number"><input type="hidden" id="total_people">
            <input type="checkbox" id="early_checkin"><input type="checkbox" id="late_checkout">
            <span id="total"></span>
            <button type="button" data-action="import">Import</button>
            <button type="button" data-action="intern">Intern</button>
            <button type="button" data-action="cleaning-mail">Putzmail</button>
            <button type="button" data-action="reminder-text">Erinnerung</button>
            <button type="button" data-action="create-code">Code</button>
        </form>
        <div id="booking-calendar">${calendar}</div>
        <div id="cleaning_mail" hidden><input id="cleaning_mail_address"><textarea id="cleaning_mail_text"></textarea></div>
        <script type="application/json" id="event-form-config">${JSON.stringify({ ...baseConfig, ...config })}</script>`;
    Object.entries(values).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element.type === 'checkbox') {
            element.checked = value;
        } else {
            element.value = value;
        }
    });
    vi.resetModules();
    await import('../../resources/js/admin/event-form.js');
}

const $ = (id) => document.getElementById(id);
const digits = (text) => text.replace(/\D/g, '');
const flush = () => new Promise((resolve) => setTimeout(resolve, 0));

function change(id, value, event = 'change') {
    const element = $(id);
    if (element.type === 'checkbox') {
        element.checked = value;
    } else {
        element.value = value;
    }
    element.dispatchEvent(new Event(event));
}

const calendarRequests = () => fetchMock.mock.calls.filter(([url]) => String(url).startsWith(baseConfig.calendarUrl));

beforeEach(() => {
    fetchMock = vi.fn(() => Promise.resolve({ ok: true, text: () => Promise.resolve(serverCalendar('2030-11-11')) }));
    vi.stubGlobal('fetch', fetchMock);
    Swal.fire.mockClear();
    Swal.showValidationMessage.mockClear();
});

afterEach(() => {
    vi.unstubAllGlobals();
    document.body.innerHTML = '';
});

describe('Total und Personen', () => {
    it('rechnet beim Laden wie die Offerte', async () => {
        await setup({ values: { total_days: 3, position_2: 4, position_3: 5 } });

        // 290 + 4 × 60 × 3 + (5 − 3 inbegriffene) × 5 × 3
        expect(digits($('total').textContent)).toBe('1040');
        // Parkplätze sind keine Personen
        expect($('total_people').value).toBe('4');
    });

    it('zieht den Rabatt nur von Positionen ab Code 100 ab, nicht von Frühbezug/Spätabgabe', async () => {
        await setup({ values: { total_days: 3, position_2: 4, position_3: 5, discount: 50 } });
        expect(digits($('total').textContent)).toBe('665'); // 290 + 360 + 15

        change('early_checkin', true);
        change('late_checkout', true);
        expect(digits($('total').textContent)).toBe('765'); // + 2 × 50 ohne Rabatt
    });

    it('rechnet bei jeder Eingabe neu', async () => {
        await setup({ values: { total_days: 2 } });
        change('position_2', '3', 'input');
        expect(digits($('total').textContent)).toBe('650'); // 290 + 3 × 60 × 2
        change('total_days', '1', 'input');
        expect(digits($('total').textContent)).toBe('470');
    });
});

describe('Datum', () => {
    it('setzt die Nächte aus Start und Ende', async () => {
        await setup({ values: { position_2: 2 } });
        change('start_date', '2030-11-11');
        change('end_date', '2030-11-14');

        expect($('total_days').value).toBe('3');
        expect(digits($('total').textContent)).toBe('650'); // 290 + 2 × 60 × 3
    });

    it('zählt beim Bearbeiten einen Tag als eine Nacht', async () => {
        await setup({ config: { minNights: 1 } });
        change('start_date', '2030-11-11');
        change('end_date', '2030-11-11');
        expect($('total_days').value).toBe('1');
    });

    it('lässt die Nächte stehen, wenn das Ende vor dem Start liegt', async () => {
        await setup({ values: { total_days: 5 } });
        change('start_date', '2030-11-14');
        change('end_date', '2030-11-11');
        expect($('total_days').value).toBe('5');
    });
});

describe('Kalender', () => {
    it('lädt die Vorschau mit Datum und Check-In/-Out', async () => {
        await setup({ values: { start_date: '2030-11-11', end_date: '2030-11-13' }, calendar: serverCalendar('2030-11-11') });
        change('early_checkin', true);
        await flush();

        const [url, options] = calendarRequests().at(-1);
        const params = new URL(url, 'http://localhost').searchParams;
        expect(params.get('start_date')).toBe('2030-11-11');
        expect(params.get('early_checkin')).toBe('1');
        expect(params.get('late_checkout')).toBe('0');
        expect(options.headers.Accept).toBe('application/json');
        expect($('booking-calendar').innerHTML).toContain('data-date="2030-11-11"');
    });

    it('lädt beim Öffnen nicht, wenn der Server den Kalender schon gezeichnet hat', async () => {
        await setup({ values: { start_date: '2030-11-11', end_date: '2030-11-13' }, calendar: serverCalendar('2030-11-11') });
        expect(calendarRequests()).toHaveLength(0);
    });

    it('lädt beim Öffnen nach einem Validierungsfehler (Daten schon ausgefüllt)', async () => {
        await setup({ values: { start_date: '2030-11-11', end_date: '2030-11-13' } });
        expect(calendarRequests()).toHaveLength(1);
    });
});

describe('Import', () => {
    function parsed(data, ok = true) {
        fetchMock.mockImplementation((url) => (url === baseConfig.parseUrl
            ? Promise.resolve({ ok, statusText: 'Server Error', json: () => Promise.resolve(data) })
            : Promise.resolve({ ok: true, text: () => Promise.resolve('') })));
    }

    it('füllt die Felder aus der Anfrage-Mail und rechnet neu', async () => {
        await setup();
        parsed({
            buchungsnummer: 'AB-77', name: 'Muster', vorname: 'Max', gruppe: 'Cevi', email: 'max@example.com', mobil: '079 123 45 67',
            mietdauer_von: '2030-11-11', mietdauer_bis: '2030-11-13', anzahl_personen: '12', kommentar: 'Wir kommen spät',
        });
        $('import-text').value = 'Anfragenummer …';

        $('main').querySelector('[data-action="import"]').click();
        await flush();

        const [url, options] = fetchMock.mock.calls[0];
        expect(url).toBe(baseConfig.parseUrl);
        expect(JSON.parse(options.body)).toEqual({ text: 'Anfragenummer …' });
        expect(options.headers['X-CSRF-TOKEN']).toBe('csrf-123');

        expect($('foreign_key').value).toBe('AB-77');
        expect($('name').value).toBe('Muster');
        expect($('telephone').value).toBe('079 123 45 67');
        // Ohne Adresse in der Anfrage: Adresse des Hauses
        expect($('street').value).toBe('Itelfingen');
        expect($('plz').value).toBe('6344');
        expect($('position_2').value).toBe('12');
        expect($('event_status_id').value).toBe('5');
        expect($('total_days').value).toBe('2');
        expect($('comment').value).toBe('Wir kommen spät');
        expect(calendarRequests()).toHaveLength(1);
    });

    it('hängt den Kommentar an und verdoppelt ihn bei einem zweiten Import nicht', async () => {
        await setup({ values: { comment: 'Bemerkung vom Verwalter' } });
        parsed({ kommentar: 'Wir kommen spät' });

        $('main').querySelector('[data-action="import"]').click();
        await flush();
        $('main').querySelector('[data-action="import"]').click();
        await flush();

        expect($('comment').value).toBe('Bemerkung vom Verwalter\n\nWir kommen spät');
    });

    it('meldet einen Fehler, statt die Felder zu leeren', async () => {
        await setup({ values: { name: 'Bisher' } });
        parsed({}, false);

        $('main').querySelector('[data-action="import"]').click();
        await flush();

        expect($('name').value).toBe('Bisher');
        expect(Swal.fire).toHaveBeenCalledWith(expect.objectContaining({ title: 'Import fehlgeschlagen', icon: 'error' }));
    });
});

describe('Buttons', () => {
    it('füllt eine interne Buchung aus', async () => {
        await setup({ values: { total_days: 3, position_2: 4 } });
        $('main').querySelector('[data-action="intern"]').click();

        expect($('name').value).toBe('Interne Buchung');
        expect($('email').value).toBe('verwalter@itelfingen.ch');
        expect($('event_status_id').value).toBe('20');
        expect($('total_days').value).toBe('0');
        expect(digits($('total').textContent)).toBe('290'); // nur noch die Pauschale
    });

    it('bereitet die Mail an die Putzfirma vor', async () => {
        await setup({ values: { start_date: '2030-11-11', end_date: '2030-11-12', total_days: 1, position_2: 6, position_3: 4 } });
        $('main').querySelector('[data-action="cleaning-mail"]').click();

        expect($('cleaning_mail').hidden).toBe(false);
        expect($('cleaning_mail_address').value).toBe('putz@example.com');
        const text = $('cleaning_mail_text').value;
        expect(text).toContain('vom 11.11.2030 bis 12.11.2030 (1 Nacht) für 6 Personen');
    });

    it('fügt den Erinnerungstext ein', async () => {
        await setup();
        $('main').querySelector('[data-action="reminder-text"]').click();
        expect($('additional_text').value).toContain('noch keine Bestätigung');
    });

    it('warnt beim Stornieren, wenn die Putzfirma schon informiert ist', async () => {
        await setup({ config: { cleaningMailSent: true } });
        change('contract_status_id', '5');
        expect(Swal.fire).not.toHaveBeenCalled();
        change('contract_status_id', '50');
        expect(Swal.fire).toHaveBeenCalledWith(expect.objectContaining({ title: 'Buchung stornieren' }));
    });

    it('warnt beim Stornieren nicht, solange die Putzfirma nichts weiss', async () => {
        await setup();
        change('contract_status_id', '50');
        expect(Swal.fire).not.toHaveBeenCalled();
    });
});

describe('Türcode', () => {
    it('verlangt zuerst einen Code', async () => {
        await setup();
        $('main').querySelector('[data-action="create-code"]').click();
        expect(Swal.fire).toHaveBeenCalledWith(expect.objectContaining({ title: 'Bitte zuerst einen Code eingeben.' }));
    });

    it('schickt Buchung und Code erst nach der Bestätigung', async () => {
        await setup({ values: { code: '1234' } });
        $('main').querySelector('[data-action="create-code"]').click();

        const options = Swal.fire.mock.calls[0][0];
        expect(options.text).toContain('"1234"');
        expect(fetchMock).not.toHaveBeenCalled();

        fetchMock.mockImplementationOnce(() => Promise.resolve({ ok: true, json: () => Promise.resolve({ success: 'Code erstellt' }) }));
        await expect(options.preConfirm()).resolves.toEqual({ success: 'Code erstellt' });
        const [url, request] = fetchMock.mock.calls[0];
        expect(url).toBe(baseConfig.createCodeUrl);
        expect(JSON.parse(request.body)).toEqual({ event_id: 7, code: '1234' });
    });

    it('zeigt die Fehlermeldung des Servers im Dialog', async () => {
        await setup({ values: { code: '1234' } });
        $('main').querySelector('[data-action="create-code"]').click();
        fetchMock.mockImplementationOnce(() => Promise.resolve({ ok: false, json: () => Promise.resolve({ error: 'Code bereits in Verwendung' }) }));

        await Swal.fire.mock.calls[0][0].preConfirm();

        expect(Swal.showValidationMessage).toHaveBeenCalledWith(expect.stringContaining('Code bereits in Verwendung'));
    });
});
