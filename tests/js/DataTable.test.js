import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import { createColumnHelper } from '@tanstack/vue-table';
import DataTable from '../../resources/js/components/DataTable.vue';

const columnHelper = createColumnHelper();
const columns = [
    columnHelper.accessor('created_at', { header: 'Erstellt am', enableSorting: true }),
    columnHelper.accessor('name', { header: 'Name', enableSorting: true }),
    columnHelper.accessor('done', { header: 'Bearbeitet', enableSorting: false }),
    columnHelper.accessor('actions', { meta: { actions: true }, header: 'Aktionen', enableSorting: false }),
];

const rows = [
    { id: 1, created_at: { display: '11.12.2030', sort: '2030-12-11' }, name: 'Zora', done: 'Nein', actions: [{ label: 'Bearbeitet', url: '/contacts/1/done' }] },
    { id: 2, created_at: { display: '01.02.2031', sort: '2031-02-01' }, name: 'Anna', done: 'Nein', actions: [{ label: 'Bearbeitet', url: '/contacts/2/done' }] },
    { id: 3, created_at: { display: '05.11.2030', sort: '2030-11-05' }, name: 'Max', done: 'Ja', actions: [] },
];

function jsonResponse(body, ok = true) {
    return Promise.resolve({ ok, status: ok ? 200 : 500, json: () => Promise.resolve(body) });
}

let wrapper;
let fetchMock;

beforeEach(() => {
    fetchMock = vi.fn((url) => jsonResponse({ data: rows }));
    vi.stubGlobal('fetch', fetchMock);
});

afterEach(() => {
    wrapper?.unmount();
    vi.unstubAllGlobals();
});

async function mountTable(extraProps = {}) {
    wrapper = mount(DataTable, { props: { columns, apiEndpoint: '/contacts/datatable', ...extraProps } });
    await flushPromises();
    return wrapper;
}

const firstColumn = () => wrapper.findAll('tbody tr').map((row) => row.find('td').text());
const header = (text) => wrapper.findAll('th').find((th) => th.text().startsWith(text));

describe('DataTable', () => {
    it('zeigt die geladenen Zeilen', async () => {
        await mountTable();
        expect(firstColumn()).toEqual(['11.12.2030', '01.02.2031', '05.11.2030']);
    });

    it('sortiert beim Klick sofort, ohne neue Anfrage', async () => {
        await mountTable();
        const requests = fetchMock.mock.calls.length;

        await header('Erstellt am').trigger('click');
        expect(firstColumn()).toEqual(['05.11.2030', '11.12.2030', '01.02.2031']);

        await header('Erstellt am').trigger('click');
        expect(firstColumn()).toEqual(['01.02.2031', '11.12.2030', '05.11.2030']);
        expect(header('Erstellt am').attributes('aria-sort')).toBe('descending');

        expect(fetchMock.mock.calls.length).toBe(requests);
    });

    it('ersetzt nach einer Aktion nur die betroffene Zeile', async () => {
        await mountTable();
        fetchMock.mockImplementationOnce(() => jsonResponse({
            message: 'Anfrage als bearbeitet markiert.',
            row: { ...rows[0], done: 'Ja', actions: [] },
        }));

        const button = wrapper.findAll('tbody tr')[0].find('button');
        expect(button.text()).toBe('Bearbeitet');
        await button.trigger('click');
        await flushPromises();

        const [url, options] = fetchMock.mock.calls.at(-1);
        expect(url).toBe('/contacts/1/done');
        expect(options.method).toBe('POST');
        expect(options.headers.Accept).toBe('application/json');

        const firstRow = wrapper.findAll('tbody tr')[0];
        expect(firstRow.text()).toContain('Ja');
        expect(firstRow.find('button').exists()).toBe(false);
        // Die anderen Zeilen bleiben, wie sie sind
        expect(wrapper.findAll('tbody tr')[1].find('button').exists()).toBe(true);
        expect(wrapper.text()).toContain('Anfrage als bearbeitet markiert.');
    });

    it('meldet eine fehlgeschlagene Aktion', async () => {
        await mountTable();
        vi.spyOn(console, 'error').mockImplementation(() => {});
        fetchMock.mockImplementationOnce(() => jsonResponse({}, false));

        await wrapper.findAll('tbody tr')[0].find('button').trigger('click');
        await flushPromises();

        expect(wrapper.text()).toContain('Die Aktion ist fehlgeschlagen.');
        expect(wrapper.findAll('tbody tr')[0].find('button').exists()).toBe(true);
    });

    it('filtert über die Suche', async () => {
        vi.useFakeTimers();
        await mountTable();

        await wrapper.find('input[type="text"]').setValue('anna');
        vi.advanceTimersByTime(300);
        await flushPromises();

        expect(firstColumn()).toEqual(['01.02.2031']);
        vi.useRealTimers();
    });

    it('zeigt leere und fehlerhafte Antworten an', async () => {
        fetchMock.mockImplementationOnce(() => jsonResponse({ data: [] }));
        await mountTable();
        expect(wrapper.text()).toContain('Keine Einträge gefunden.');
        wrapper.unmount();

        vi.spyOn(console, 'error').mockImplementation(() => {});
        fetchMock.mockImplementationOnce(() => jsonResponse({}, false));
        await mountTable();
        expect(wrapper.text()).toContain('Die Daten konnten nicht geladen werden.');
    });
});

describe('DataTable auf dem Handy (Karten)', () => {
    const mobile = { kicker: 'created_at', badge: 'state', badgeClasses: { Neu: 'bg-orange-100' }, title: 'name', lines: [], checks: 'checks', details: ['done'] };
    const cardRows = [
        { ...rows[0], state: 'Neu', checks: [{ label: 'Angebot', done: true }, { label: 'Code', done: false }] },
        rows[1],
    ];
    const cards = () => wrapper.findAll('[data-testid="cards"] article');
    const part = (card, name) => card.find(`[data-card="${name}"]`);

    it('zeigt pro Zeile eine Karte mit Datum, Titel, Badge und Prüfpunkten', async () => {
        fetchMock.mockImplementationOnce(() => jsonResponse({ data: cardRows }));
        await mountTable({ mobile });

        expect(cards()).toHaveLength(2);
        const [first, second] = cards();
        expect(part(first, 'kicker').text()).toBe('11.12.2030');
        expect(part(first, 'title').text()).toBe('Zora');
        expect(part(first, 'badge').text()).toBe('Neu');
        expect(part(first, 'badge').classes()).toContain('bg-orange-100');
        expect(part(first, 'checks').findAll('li').map((li) => li.text())).toEqual(['✓erledigt: Angebot', '✗offen: Code']);
        expect(first.find('details').text()).toContain('Bearbeitet');
        // Ohne Badge und Prüfpunkte in den Daten bleiben die Teile weg
        expect(part(second, 'badge').exists()).toBe(false);
        expect(part(second, 'checks').exists()).toBe(false);
    });

    it('nimmt ohne Angabe die erste Spalte als Titel und die nächsten zwei als Zeilen', async () => {
        await mountTable();

        const [first] = cards();
        expect(part(first, 'title').text()).toBe('11.12.2030');
        expect(first.findAll('[data-card="line"]').map((line) => line.text())).toEqual(['Zora', 'Nein']);
        expect(first.find('details').exists()).toBe(false);
    });

    it('führt die Aktion auf der Karte aus und ersetzt die Zeile', async () => {
        await mountTable({ mobile });
        fetchMock.mockImplementationOnce(() => jsonResponse({
            message: 'Anfrage als bearbeitet markiert.',
            row: { ...rows[0], done: 'Ja', actions: [] },
        }));

        await cards()[0].find('button').trigger('click');
        await flushPromises();

        expect(fetchMock.mock.calls.at(-1)[0]).toBe('/contacts/1/done');
        expect(cards()[0].find('button').exists()).toBe(false);
        expect(cards()[1].find('button').exists()).toBe(true);
    });

    it('sortiert über das Auswahlfeld', async () => {
        await mountTable({ mobile });
        // Das erste Auswahlfeld ist die Sortierung, das zweite die Anzahl Zeilen pro Seite
        const sortSelect = wrapper.findAll('select')[0];

        await sortSelect.setValue('created_at:desc');
        expect(cards().map((card) => part(card, 'kicker').text())).toEqual(['01.02.2031', '11.12.2030', '05.11.2030']);
        expect(header('Erstellt am').attributes('aria-sort')).toBe('descending');

        await sortSelect.setValue('');
        expect(cards().map((card) => part(card, 'kicker').text())).toEqual(['11.12.2030', '01.02.2031', '05.11.2030']);
    });
});
