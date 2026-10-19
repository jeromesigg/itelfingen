/**
 * Logik der Buchungskalender (ohne Darstellung).
 *
 * Daten sind ISO-Strings (YYYY-MM-DD). Belegte Tage kommen vom Server (App\Services\BookingDays):
 * { '2026-11-11': { am: 'BD', pm: 'P', id: 12 } } – am/pm = Vor-/Nachmittag, null = frei.
 * Der Zustand eines Tages ist die CSS-Klasse hk-agenda__day--<Zustand> aus resources/css/libs/events.css.
 */

export const MONTH_NAMES = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];

/** Frühestens so viele Tage im Voraus kann ein Gast buchen. */
export const MIN_LEAD_DAYS = 4;

/** Kurzfristige Buchungen (Anreise in weniger als so vielen Tagen) erhalten Rabatt, siehe EventController. */
export const SHORT_NOTICE_DAYS = 8;
export const SHORT_NOTICE_DISCOUNT = 50;

export function toISO(date) {
    return date.toISOString().slice(0, 10);
}

export function fromISO(iso) {
    return new Date(iso + 'T00:00:00Z');
}

export function addDays(iso, days) {
    const date = fromISO(iso);
    date.setUTCDate(date.getUTCDate() + days);
    return toISO(date);
}

export function todayISO() {
    const now = new Date();
    return toISO(new Date(Date.UTC(now.getFullYear(), now.getMonth(), now.getDate())));
}

export function nightsBetween(start, end) {
    return Math.round((fromISO(end) - fromISO(start)) / 86400000);
}

/**
 * Wochen eines Monats, beginnend am Montag. Tage ausserhalb des Monats sind null.
 *
 * @return {Array<Array<string|null>>}
 */
export function monthWeeks(year, month) {
    const first = new Date(Date.UTC(year, month, 1));
    const daysInMonth = new Date(Date.UTC(year, month + 1, 0)).getUTCDate();
    const offset = (first.getUTCDay() + 6) % 7;
    const cells = Array(offset).fill(null);
    for (let day = 1; day <= daysInMonth; day++) {
        cells.push(toISO(new Date(Date.UTC(year, month, day))));
    }
    while (cells.length % 7) {
        cells.push(null);
    }
    const weeks = [];
    for (let i = 0; i < cells.length; i += 7) {
        weeks.push(cells.slice(i, i + 7));
    }
    return weeks;
}

function half(days, iso) {
    return days[iso] ?? { am: null, pm: null };
}

/** Zustand ohne Auswahl, z. B. 'FF', 'BDF' (Abreise am Vormittag) oder 'FP' (Anreise am Nachmittag). */
export function baseState(days, iso) {
    const { am, pm } = half(days, iso);
    return (am ?? 'F') + (pm ?? 'F');
}

/** Als Anreisetag möglich, wenn der Nachmittag frei ist. */
export function isStartable(days, iso) {
    return !half(days, iso).pm;
}

/**
 * Kürzt einen Zeitraum bis vor die nächste Buchung: Ist der Nachmittag eines Tages belegt,
 * ist er der letzte mögliche Abreisetag (Abreise am Vormittag); ist schon der Vormittag belegt, der Tag davor.
 */
export function clampEnd(days, start, end) {
    for (let day = addDays(start, 1); day <= end; day = addDays(day, 1)) {
        const { am, pm } = half(days, day);
        if (am) {
            return addDays(day, -1);
        }
        if (pm && day < end) {
            return day;
        }
    }
    return end;
}

/**
 * Zustände der gewählten Tage (Überlagerung der Auswahl auf die Belegung).
 *
 * @return {Object<string, string>}
 */
export function selectionStates(days, start, end) {
    const states = {};
    if (!start || !isStartable(days, start)) {
        return states;
    }
    const startState = baseState(days, start);
    if (!end || end === start) {
        states[start] = startState.charAt(0) === 'F' ? 'SS' : startState.charAt(0) + 'S';
        return states;
    }
    states[start] = startState.charAt(0) + 'S';
    for (let day = addDays(start, 1); day < end; day = addDays(day, 1)) {
        states[day] = 'SS';
    }
    if (!half(days, end).am) {
        states[end] = 'S' + baseState(days, end).charAt(1);
    }
    return states;
}

export function shortNoticeDiscount(start, today, enabled) {
    return enabled && start < addDays(today, SHORT_NOTICE_DAYS) ? SHORT_NOTICE_DISCOUNT : 0;
}

/**
 * Preis je Position (Logik wie bisher im Buchungsformular, die definitiven Preise rechnet Bexio):
 * bexio_code < 50 einmalig (z. B. Hausmiete, Reinigung), > 100 Anzahl × Preis × Nächte abzüglich Rabatt.
 * Tagesaufenthalte (50–100) gibt es nicht mehr, sie fehlen im Formular.
 */
export function positionTotal(position, amount, nights, discount) {
    if (position.bexio_code < 50) {
        return position.price;
    }
    if (position.bexio_code > 100) {
        return (Number(amount) || 0) * position.price * nights * ((100 - discount) / 100);
    }
    return 0;
}

/** Positionen mit Eingabefeld (Personen, Parkplätze …); ihre Anzahlen zählen als Personen. */
export function hasAmountInput(position) {
    return position.bexio_code >= 100;
}

export function isBookable(position) {
    return position.bexio_code < 50 || position.bexio_code >= 100;
}
