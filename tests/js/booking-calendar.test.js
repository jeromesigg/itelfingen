import { afterEach, describe, expect, it, vi } from 'vitest';
import {
    addDays,
    baseState,
    clampEnd,
    isStartable,
    monthWeeks,
    nightsBetween,
    selectionStates,
    shortNoticeDiscount,
    todayISO,
} from '../../resources/js/libs/booking-calendar.js';

// Belegung wie von App\Services\BookingDays: Anreise Mo 11.11. nachmittags, Abreise Mi 13.11. vormittags
const days = {
    '2030-11-11': { am: null, pm: 'B' },
    '2030-11-12': { am: 'B', pm: 'B' },
    '2030-11-13': { am: 'BD', pm: null },
};

describe('Datumshilfen', () => {
    it('zählt Nächte auch über die Zeitumstellung', () => {
        expect(nightsBetween('2030-03-29', '2030-04-01')).toBe(3);
        expect(nightsBetween('2030-10-26', '2030-10-28')).toBe(2);
    });

    it('rechnet Tage über den Monatswechsel', () => {
        expect(addDays('2030-01-31', 1)).toBe('2030-02-01');
        expect(addDays('2030-03-01', -1)).toBe('2030-02-28');
    });

    it('beginnt Wochen am Montag', () => {
        // 1.11.2030 ist ein Freitag
        expect(monthWeeks(2030, 10)[0]).toEqual([null, null, null, null, '2030-11-01', '2030-11-02', '2030-11-03']);
        expect(monthWeeks(2030, 10).flat().filter(Boolean)).toHaveLength(30);
    });

    describe('todayISO', () => {
        afterEach(() => vi.useRealTimers());

        it('nimmt das lokale Datum, nicht UTC', () => {
            vi.useFakeTimers();
            vi.setSystemTime(new Date(2030, 0, 1, 0, 30));
            expect(todayISO()).toBe('2030-01-01');
        });
    });
});

describe('Belegung', () => {
    it('setzt den Zustand aus Vor- und Nachmittag zusammen', () => {
        expect(baseState(days, '2030-11-10')).toBe('FF');
        expect(baseState(days, '2030-11-11')).toBe('FB');
        expect(baseState(days, '2030-11-13')).toBe('BDF');
    });

    it('erlaubt die Anreise nur bei freiem Nachmittag', () => {
        expect(isStartable(days, '2030-11-11')).toBe(false);
        expect(isStartable(days, '2030-11-13')).toBe(true);
    });
});

describe('clampEnd', () => {
    it('endet am Wechseltag, wenn die nächste Buchung am Nachmittag anreist', () => {
        expect(clampEnd(days, '2030-11-08', '2030-11-20')).toBe('2030-11-11');
        expect(clampEnd(days, '2030-11-08', '2030-11-11')).toBe('2030-11-11');
    });

    it('endet am Tag davor, wenn schon der Vormittag belegt ist', () => {
        const earlyCheckin = { '2030-11-11': { am: 'B', pm: 'B' } };
        expect(clampEnd(earlyCheckin, '2030-11-08', '2030-11-20')).toBe('2030-11-10');
    });

    it('lässt freie Zeiträume unverändert', () => {
        expect(clampEnd(days, '2030-11-13', '2030-11-20')).toBe('2030-11-20');
    });
});

describe('selectionStates', () => {
    it('markiert An- und Abreise als halbe Tage', () => {
        expect(selectionStates(days, '2030-11-08', '2030-11-11')).toEqual({
            '2030-11-08': 'FS',
            '2030-11-09': 'SS',
            '2030-11-10': 'SS',
            '2030-11-11': 'SB',
        });
    });

    it('beginnt am Abreisetag der vorherigen Buchung', () => {
        expect(selectionStates(days, '2030-11-13', '2030-11-15')).toEqual({
            '2030-11-13': 'BS',
            '2030-11-14': 'SS',
            '2030-11-15': 'SF',
        });
    });

    it('zeigt nichts, wenn der Anreisetag belegt ist', () => {
        expect(selectionStates(days, '2030-11-11', null)).toEqual({});
    });
});

describe('shortNoticeDiscount', () => {
    it('gibt 50 % bei Anreise in weniger als 8 Tagen', () => {
        expect(shortNoticeDiscount('2030-01-08', '2030-01-01', true)).toBe(50);
        expect(shortNoticeDiscount('2030-01-09', '2030-01-01', true)).toBe(0);
    });

    it('gibt keinen Rabatt, wenn er ausgeschaltet ist', () => {
        expect(shortNoticeDiscount('2030-01-02', '2030-01-01', false)).toBe(0);
    });
});
