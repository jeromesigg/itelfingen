import { afterEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import { createColumnHelper } from '@tanstack/vue-table';
import DataTable from '../../resources/js/components/DataTable.vue';

const columnHelper = createColumnHelper();
const columns = [
    columnHelper.accessor('start_date', { header: 'Datum', enableSorting: true }),
    columnHelper.accessor('number', { meta: { html: true }, header: 'Nr.', enableSorting: true }),
    columnHelper.accessor('name', { meta: { html: true }, header: 'Name', enableSorting: true }),
    columnHelper.accessor('status', { meta: { html: true }, header: 'Status', enableSorting: true }),
];

// Ungefähr so viele Buchungen wie in Produktion
const rows = Array.from({ length: 700 }, (_, i) => {
    const date = new Date(Date.UTC(2020, 0, 1 + ((i * 37) % 2500))).toISOString().slice(0, 10);
    return {
        id: i + 1,
        start_date: { display: date.split('-').reverse().join('.'), sort: date },
        number: `<a href="/admin/events/${i + 1}/edit">${String(i + 1).padStart(5, '0')}</a><br>`,
        name: `Name ${(i * 7) % 700}`,
        status: ['Offen', 'Angebot erstellt', 'Bestätigt'][i % 3],
    };
});

let wrapper;

afterEach(() => {
    wrapper?.unmount();
    vi.unstubAllGlobals();
});

describe('DataTable bei vielen Sortierwechseln', () => {
    it('bleibt korrekt, schnell und ohne Vue-Warnungen', async () => {
        const fetchMock = vi.fn(() => Promise.resolve({ ok: true, json: () => Promise.resolve({ data: rows }) }));
        vi.stubGlobal('fetch', fetchMock);
        const warn = vi.spyOn(console, 'warn');
        const error = vi.spyOn(console, 'error');

        wrapper = mount(DataTable, { props: { columns, apiEndpoint: '/events/datatable' } });
        await flushPromises();
        const header = (index) => wrapper.findAll('th')[index];
        const firstCell = () => wrapper.find('tbody tr td').text();
        const nextPage = () => wrapper.findAll('button').find((button) => button.text().includes('Nächste'));

        // Jeden Klick einzeln messen: Mit dem alten Muster ("get data()"/"get state()") dauerte jeder Klick ein
        // Vielfaches des vorherigen und blockierte JavaScript; ein Gesamt-Timeout käme nie zum Zug.
        const clickWithin = async (element, label) => {
            const started = performance.now();
            await element.trigger('click');
            expect(performance.now() - started, `${label} dauert zu lange`).toBeLessThan(500);
        };
        const started = performance.now();
        for (let round = 0; round < 20; round++) {
            for (const index of [0, 1, 2, 3, 0, 0]) {
                await clickWithin(header(index), `Runde ${round + 1}, Spalte ${index}`);
            }
            await clickWithin(nextPage(), `Runde ${round + 1}, nächste Seite`);
        }
        const duration = performance.now() - started;

        // Nach [0, 1, 2, 3, 0, 0] ist Datum absteigend sortiert; zwei weitere Klicks: ohne Sortierung, dann aufsteigend
        expect(header(0).attributes('aria-sort')).toBe('descending');
        await header(0).trigger('click');
        await header(0).trigger('click');
        expect(header(0).attributes('aria-sort')).toBe('ascending');
        // Sortieren springt auf Seite 1 zurück
        expect(firstCell()).toBe('01.01.2020');
        expect(wrapper.findAll('tbody tr')).toHaveLength(10);

        // Sortieren und Blättern laufen im Browser, ohne neue Anfrage an den Server
        expect(fetchMock).toHaveBeenCalledTimes(1);
        expect(warn).not.toHaveBeenCalled();
        expect(error).not.toHaveBeenCalled();
        // 140 Sortierungen und 20 Seitenwechsel über 700 Zeilen; ein Hänger läge weit darüber
        expect(duration).toBeLessThan(5000);
    });
});
