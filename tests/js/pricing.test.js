import { describe, expect, it } from 'vitest';
import { discountApplies, isFixedPosition, isPersonPosition, lineTotal, offerAmount } from '../../resources/js/libs/pricing.js';

// Gleiche Fälle wie tests/Unit/OfferAmountTest.php: Formulare und Bexio-Offerte müssen gleich rechnen
const offerAmounts = {
    'Hausmiete fix': [20, 1, 4, 1],
    'Personen pro Nacht': [30, 6, 4, 24],
    'Nutzung an der Obergrenze pro Nacht': [200, 2, 3, 6],
    'Personen ohne Eintrag': [30, 0, 4, 0],
    'Parkplätze: 3 inbegriffen': [210, 3, 4, 0],
    'Parkplätze: weniger als inbegriffen': [210, 1, 4, 0],
    'Parkplätze: 2 zusätzliche pro Nacht': [210, 5, 4, 8],
    'Zusatzkosten fix (unter Parkplätzen)': [201, 2, 4, 2],
    'Zusatzkosten fix (über Parkplätzen)': [220, 1, 4, 1],
    'Genossenschaftsschein fix': [300, 1, 7, 1],
    'eine Nacht': [30, 10, 1, 10],
};

describe('offerAmount', () => {
    it.each(Object.entries(offerAmounts))('%s', (_, [code, amount, nights, expected]) => {
        expect(offerAmount(code, amount, nights)).toBe(expected);
    });

    it('behandelt leere Felder als 0', () => {
        expect(offerAmount(120, '', 3)).toBe(0);
        expect(offerAmount(120, undefined, 3)).toBe(0);
    });
});

describe('lineTotal', () => {
    it('zieht den Rabatt ab Code 100 ab', () => {
        expect(lineTotal({ bexio_code: 120, price: 60 }, 4, 3, 50)).toBe(360);
    });

    it('gibt auf Pauschalen unter 100 keinen Rabatt', () => {
        expect(lineTotal({ bexio_code: 20, price: 290 }, 1, 3, 50)).toBe(290);
    });

    it('gibt auf Frühbezug/Spätabgabe keinen Rabatt', () => {
        expect(lineTotal({ bexio_code: 220, price: 50 }, 2, 3, 50)).toBe(100);
        expect(discountApplies(220)).toBe(false);
    });

    it('gibt auf übrige Zusatzkosten Rabatt', () => {
        expect(lineTotal({ bexio_code: 250, price: 20 }, 1, 3, 10)).toBe(18);
    });
});

describe('Positionsarten', () => {
    it('unterscheidet Pauschalen und Personen', () => {
        expect(isFixedPosition({ bexio_code: 20 })).toBe(true);
        expect(isFixedPosition({ bexio_code: 120 })).toBe(false);
        expect(isPersonPosition({ bexio_code: 150 })).toBe(true);
        expect(isPersonPosition({ bexio_code: 210 })).toBe(false);
    });
});
