/**
 * Preise der Buchungspositionen, gleich gerechnet wie die Bexio-Offerte
 * (App\Listeners\EventOfferCreateListener::offerAmount, getestet in tests/Unit/OfferAmountTest.php).
 *
 * Positionen: { bexio_code, price }. Die definitiven Preise legt der Mietvertrag fest.
 */

export const PARKING_CODE = 210;
export const PARKING_INCLUDED = 3;
/** Frühbezug/Spätabgabe: je Early Check-In und Late Check-Out eine Pauschale, ohne Rabatt (PricelistPosition::CHECKIN_CHECKOUT_CODE). */
export const CHECKIN_CHECKOUT_CODE = 220;

/**
 * Menge in der Offerte: Hausmiete (bis 20) und Zusatzkosten (über 200) fix,
 * Personen/Nutzung (21–200) pro Nacht, Parkplätze pro Nacht, wobei drei inbegriffen sind.
 */
export function offerAmount(bexioCode, amount, nights) {
    const count = Number(amount) || 0;
    if (bexioCode === PARKING_CODE) {
        return Math.max(count - PARKING_INCLUDED, 0) * nights;
    }
    if (bexioCode > 20 && bexioCode <= 200) {
        return count * nights;
    }
    return count;
}

/** Rabatt wie in der Offerte (EventOfferCreateListener::discountApplies). */
export function discountApplies(bexioCode) {
    return bexioCode >= 100 && bexioCode !== CHECKIN_CHECKOUT_CODE;
}

/** Betrag einer Position inklusive Rabatt. */
export function lineTotal(position, amount, nights, discount) {
    const factor = discountApplies(position.bexio_code) ? (100 - (Number(discount) || 0)) / 100 : 1;
    return offerAmount(position.bexio_code, amount, nights) * position.price * factor;
}

/** Pauschalen (Hausmiete, Reinigung) ohne Eingabefeld, immer Menge 1. */
export function isFixedPosition(position) {
    return position.bexio_code < 50;
}

/** Erwachsene und Kinder; ihre Anzahlen ergeben total_people. */
export function isPersonPosition(position) {
    return position.bexio_code >= 100 && position.bexio_code <= 200;
}
