import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import FilterButtons from '../../resources/js/components/FilterButtons.vue';

describe('FilterButtons', () => {
    it('zeigt den aktiven Filter und meldet die Auswahl', async () => {
        const wrapper = mount(FilterButtons, {
            props: { modelValue: 'Alle', options: ['Alle', 'Offen', 'Bearbeitet'], label: 'Status' },
        });
        const pressed = () => wrapper.findAll('button').map((button) => button.attributes('aria-pressed'));

        expect(pressed()).toEqual(['true', 'false', 'false']);

        await wrapper.findAll('button')[1].trigger('click');
        expect(wrapper.emitted('update:modelValue')).toEqual([['Offen']]);

        await wrapper.setProps({ modelValue: 'Offen' });
        expect(pressed()).toEqual(['false', 'true', 'false']);
        expect(wrapper.find('[role="group"]').attributes('aria-label')).toBe('Status');
    });
});
