import { afterEach, describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import BookingWizard from '../../resources/js/components/BookingWizard.vue';

const positions = [
    { bexio_code: 20, name: 'Reinigungs- und Buchungspauschale', price: 290 },
    { bexio_code: 50, name: 'Tagespauschale', price: 300 },
    { bexio_code: 120, name: 'Erwachsene, Normaltarif', price: 60 },
    { bexio_code: 140, name: 'Kinder', price: 30 },
];

let wrapper;

/** Im Formular einhängen wie auf der Startseite (der Assistent hört auf dessen submit). */
function mountWizard(props = {}) {
    const form = document.createElement('form');
    form.addEventListener('submit', (event) => event.preventDefault());
    document.body.append(form);
    wrapper = mount(BookingWizard, {
        props: { positions, old: { start_date: '2030-11-11', end_date: '2030-11-14' }, showForm: true, ...props },
        attachTo: form,
    });
    return { wrapper, form };
}

const hidden = (name) => wrapper.find(`input[type="hidden"][name="${name}"]`).element.value;

afterEach(() => {
    wrapper?.unmount();
    document.body.innerHTML = '';
});

describe('BookingWizard', () => {
    it('behält Eingaben, wenn die Komponente neu zeichnet', async () => {
        mountWizard();
        await wrapper.find('#name').setValue('Muster');
        await wrapper.find('#email').setValue('max@example.com');

        // Personen ändern zeichnet die Komponente neu
        await wrapper.find('#position_120').setValue('4');

        expect(wrapper.find('#name').element.value).toBe('Muster');
        expect(wrapper.find('#email').element.value).toBe('max@example.com');
    });

    it('behält Eingaben beim Absenden', async () => {
        const { form } = mountWizard();
        await wrapper.find('#name').setValue('Muster');

        form.dispatchEvent(new Event('submit', { cancelable: true }));
        await wrapper.vm.$nextTick();

        expect(wrapper.find('#name').element.value).toBe('Muster');
        const submit = wrapper.find('button[type="submit"]');
        expect(submit.attributes('disabled')).toBeDefined();
        expect(submit.text()).toBe('Wird gesendet...');
    });

    it('rechnet Total, Personen und Nächte für die versteckten Felder', async () => {
        mountWizard();
        await wrapper.find('#position_120').setValue('4');
        await wrapper.find('#position_140').setValue('2');

        // 290 + 4 × 60 × 3 + 2 × 30 × 3, kein Rabatt (Anreise weit in der Zukunft)
        expect(hidden('total_amount')).toBe('1190');
        expect(hidden('total_people')).toBe('6');
        expect(hidden('total_days')).toBe('3');
        expect(hidden('start_date')).toBe('2030-11-11');
    });

    it('zeigt keine Tagespauschale mehr', () => {
        mountWizard();
        expect(wrapper.text()).not.toContain('Tagespauschale');
    });

    it('stellt nach einem Validierungsfehler Eingaben und Formular wieder her', () => {
        mountWizard({ old: { start_date: '2030-11-11', end_date: '2030-11-14', name: 'Muster', positions: { 120: 3 } } });

        expect(wrapper.find('.hk-dateselect').isVisible()).toBe(true);
        expect(wrapper.find('#name').element.value).toBe('Muster');
        expect(wrapper.find('#position_120').element.value).toBe('3');
    });

    it('verlangt eine Übernachtung, bevor es weitergeht', async () => {
        mountWizard({ old: {}, showForm: false });

        await wrapper.findAll('button').find((button) => button.text() === 'Weiter').trigger('click');

        expect(wrapper.text()).toContain('Es muss mindestens eine Übernachtung gewählt werden.');
        expect(wrapper.find('.hk-dateselect').isVisible()).toBe(false);
    });
});
