/**
 * Admin-Formulare "Buchung erstellen" und "Buchung bearbeiten".
 *
 * Konfiguration als JSON in <script type="application/json" id="event-form-config">,
 * Buttons über data-action. Felder, die eine Seite nicht hat, werden übersprungen.
 */
import Swal from 'sweetalert2';
import { isPersonPosition, lineTotal } from '../libs/pricing.js';

const configElement = document.getElementById('event-form-config');

if (configElement) {
    initEventForm(JSON.parse(configElement.textContent));
}

function field(id) {
    return document.getElementById(id);
}

function setValue(id, value) {
    const element = field(id);
    if (element) {
        element.value = value;
    }
}

function csrfToken() {
    return document.querySelector('input[name="_token"]')?.value ?? '';
}

function postJson(url, body) {
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        body: JSON.stringify(body),
    });
}

function formatDate(iso) {
    return new Date(iso + 'T00:00:00Z').toLocaleDateString('de-CH', { timeZone: 'UTC' });
}

/**
 * @param {{
 *   positions: Array<{input: string, bexio_code: number, price: number}>,
 *   minNights: number,
 *   checkinCheckoutPrice?: number,
 *   calendarUrl: string,
 *   parseUrl: string,
 *   importStatus?: number,
 *   importPersonsInput?: string,
 *   internStatus?: number,
 *   createCodeUrl?: string,
 *   eventId?: number,
 *   cleaningMailAddress?: string,
 *   cleaningMailSent?: boolean,
 *   cancelledContractState?: number,
 * }} config
 */
function initEventForm(config) {
    let calendarRequest = null;
    const actions = {
        import: importBooking,
        intern: fillIntern,
        'cleaning-mail': prepareCleaningMail,
        'reminder-text': () => setValue('additional_text', 'Wir haben bisher noch keine Bestätigung für obenstehendes Angebot von Dir erhalten. Sollten wir innerhalb der nächsten zwei Wochen keine Bestätigung erhalten, müssen wir die Buchung leider wieder freigeben.'),
        'create-code': createDoorCode,
    };
    document.querySelectorAll('[data-action]').forEach((button) => {
        button.addEventListener('click', () => actions[button.dataset.action]?.());
    });

    ['start_date', 'end_date'].forEach((id) => field(id)?.addEventListener('change', () => {
        updateNights();
        updateTotal();
    }));
    ['total_days', 'discount', ...config.positions.map((position) => position.input)].forEach((id) => {
        field(id)?.addEventListener('input', updateTotal);
    });
    ['start_date', 'end_date', 'early_checkin', 'late_checkout'].forEach((id) => {
        field(id)?.addEventListener('change', updateCalendar);
    });
    ['early_checkin', 'late_checkout'].forEach((id) => field(id)?.addEventListener('change', updateTotal));

    field('contract_status_id')?.addEventListener('change', (event) => {
        if (Number(event.target.value) === config.cancelledContractState && config.cleaningMailSent) {
            void Swal.fire({
                title: 'Buchung stornieren',
                text: 'Die Buchung wurde bereits der Reinigungsfirma gemeldet. Bitte die Stornierung weiterleiten.',
                icon: 'warning',
            });
        }
    });

    document.querySelectorAll('.confirm-delete').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            void Swal.fire({
                title: 'Buchung löschen?',
                text: "Lösche Buchungen nur im Notfall. Zur Nachvollziehbarkeit der Buchungen, bitte den Status auf 'Storniert' setzen und nicht löschen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: 'blue',
                cancelButtonColor: 'red',
            }).then((result) => {
                if (result.isConfirmed) {
                    button.form.submit();
                }
            });
        });
    });

    updateTotal();
    // Nach einem Validierungsfehler sind die Daten schon ausgefüllt
    if (!field('booking-calendar')?.querySelector('[data-date]')) {
        updateCalendar();
    }

    /** Nächte aus Start und Ende; das Feld "Tage" bleibt danach von Hand änderbar. */
    function updateNights() {
        const start = field('start_date')?.value;
        const end = field('end_date')?.value;
        if (!start || !end || end < start) {
            return;
        }
        const nights = Math.round((new Date(end) - new Date(start)) / 86400000);
        setValue('total_days', Math.max(nights, config.minNights));
    }

    function updateTotal() {
        const nights = Number(field('total_days')?.value) || 0;
        const discount = field('discount')?.value;
        let total = 0;
        let people = 0;
        config.positions.forEach((position) => {
            const amount = field(position.input)?.value;
            total += lineTotal(position, amount, nights, discount);
            if (isPersonPosition(position)) {
                people += Number(amount) || 0;
            }
        });
        // Frühbezug/Spätabgabe setzt der Server beim Speichern aus den Checkboxen
        const checkinCheckout = ['early_checkin', 'late_checkout'].filter((id) => field(id)?.checked).length;
        total += checkinCheckout * (config.checkinCheckoutPrice ?? 0);
        field('total').textContent = new Intl.NumberFormat('de-CH', { maximumFractionDigits: 2 }).format(total);
        setValue('total_people', people);
    }

    /** Kalender mit den Daten aus dem Formular laden (nur Anzeige, gespeichert wird erst beim Absenden). */
    function updateCalendar() {
        const start = field('start_date')?.value;
        const end = field('end_date')?.value;
        if (!start || !end || end < start) {
            return;
        }
        const params = new URLSearchParams({ start_date: start, end_date: end });
        ['early_checkin', 'late_checkout'].forEach((id) => {
            if (field(id)) {
                params.set(id, field(id).checked ? 1 : 0);
            }
        });
        calendarRequest?.abort();
        calendarRequest = new AbortController();
        fetch(config.calendarUrl + '?' + params, { headers: { 'Accept': 'application/json' }, signal: calendarRequest.signal })
            .then((response) => (response.ok ? response.text() : null))
            .then((html) => {
                if (html) {
                    field('booking-calendar').innerHTML = html;
                }
            })
            .catch(() => {});
    }

    function afterFill() {
        updateNights();
        updateTotal();
        updateCalendar();
    }

    /** Anfrage-Mail bzw. Buchungs-PDF einer externen Plattform auswerten und Felder befüllen. */
    async function importBooking() {
        try {
            const response = await postJson(config.parseUrl, { text: field('import-text').value });
            if (!response.ok) {
                throw new Error(response.statusText);
            }
            const data = await response.json();
            if (data.buchungsnummer) {
                setValue('foreign_key', data.buchungsnummer);
            }
            setValue('name', data.name ?? '');
            setValue('firstname', data.vorname ?? '');
            setValue('group_name', data.gruppe ?? '');
            setValue('street', data.strasse ?? 'Itelfingen');
            setValue('house_number', data.hausnummer ?? '3');
            setValue('plz', data.plz ?? '6344');
            setValue('city', data.stadt ?? 'Meierskappel');
            setValue('email', data.email ?? 'verwalter@itelfingen.ch');
            setValue('telephone', data.telefon ?? data.mobil ?? '');
            setValue('start_date', data.mietdauer_von ?? '');
            setValue('end_date', data.mietdauer_bis ?? '');
            if (config.importPersonsInput) {
                setValue(config.importPersonsInput, data.anzahl_personen ?? '');
            }
            if (config.importStatus) {
                setValue('event_status_id', config.importStatus);
            }
            // Kommentar der Gruppe anhängen statt überschreiben, damit ein zweiter Import (Buchungs-PDF) nichts verliert
            const comment = field('comment');
            if (data.kommentar && comment && !comment.value.includes(data.kommentar)) {
                comment.value = comment.value ? comment.value + '\n\n' + data.kommentar : data.kommentar;
            }
            afterFill();
        } catch (error) {
            void Swal.fire({ title: 'Import fehlgeschlagen', text: String(error), icon: 'error' });
        }
    }

    function fillIntern() {
        setValue('name', 'Interne Buchung');
        setValue('street', 'Itelfingen');
        setValue('house_number', '3');
        setValue('plz', '6344');
        setValue('city', 'Meierskappel');
        setValue('email', 'verwalter@itelfingen.ch');
        setValue('code', '290393');
        setValue('event_status_id', config.internStatus);
        setValue('total_days', 0);
        updateTotal();
    }

    function prepareCleaningMail() {
        field('cleaning_mail').hidden = false;
        const nights = Number(field('total_days').value) || 0;
        const people = config.positions
            .filter(isPersonPosition)
            .reduce((sum, position) => sum + (Number(field(position.input)?.value) || 0), 0);
        setValue('cleaning_mail_address', config.cleaningMailAddress ?? '');
        setValue('cleaning_mail_text', 'Sehr geehrte Damen und Herren,\n'
            + 'Wir haben eine neue Buchung für unser Ferienhaus vom ' + formatDate(field('start_date').value)
            + ' bis ' + formatDate(field('end_date').value) + ' (' + nights + ' ' + (nights === 1 ? 'Nacht' : 'Nächte') + ') für '
            + people + ' Personen. Für einige nachfolgende Reinigung wären wir sehr dankbar.\n\n'
            + 'Vielen Dank und freundliche Grüsse,\nVerwaltung Ferienhaus Itelfingen');
    }

    function createDoorCode() {
        const code = field('code').value;
        if (!code) {
            void Swal.fire({ title: 'Bitte zuerst einen Code eingeben.', icon: 'info' });
            return;
        }
        void Swal.fire({
            title: 'Türcode erstellen?',
            text: `Wirklich den Türcode "${code}" für diese Buchung erstellen?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ja',
            cancelButtonText: 'Abbrechen',
            confirmButtonColor: 'blue',
            cancelButtonColor: 'red',
            showLoaderOnConfirm: true,
            theme: 'auto',
            preConfirm: async () => {
                try {
                    const response = await postJson(config.createCodeUrl, { event_id: config.eventId, code });
                    const data = await response.json();
                    if (!response.ok) {
                        return Swal.showValidationMessage(JSON.stringify(data));
                    }
                    return data;
                } catch (error) {
                    return Swal.showValidationMessage(`Request failed: ${error}`);
                }
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.isConfirmed) {
                void Swal.fire({ titleText: String(result.value.success) });
            }
        });
    }
}
