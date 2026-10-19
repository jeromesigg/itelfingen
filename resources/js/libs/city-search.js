/**
 * Vorschläge für PLZ und Ortschaft (Route searchajaxcity) über native <datalist>.
 *
 * Aktiv in Formularen mit data-city-search="<URL>" und den Feldern zipcode, city und optional city_id.
 * Passt die Eingabe genau auf einen Vorschlag, werden die übrigen Felder ausgefüllt.
 */
const DEBOUNCE_MS = 250;
const MIN_LENGTH = 2;

export function initCitySearch(form) {
    const url = form.dataset.citySearch;
    const fields = {
        plz: form.querySelector('[name="zipcode"]'),
        name: form.querySelector('[name="city"]'),
    };
    const cityId = form.querySelector('[name="city_id"]');
    let results = [];

    Object.entries(fields).forEach(([key, input]) => {
        if (!input) {
            return;
        }
        const list = document.createElement('datalist');
        list.id = `${input.id || input.name}-suggestions`;
        input.after(list);
        input.setAttribute('list', list.id);
        input.setAttribute('autocomplete', 'off');

        let timer = null;
        input.addEventListener('input', () => {
            if (fillFromMatch(key, input.value)) {
                return;
            }
            clearTimeout(timer);
            const term = input.value.trim();
            if (term.length < MIN_LENGTH) {
                return;
            }
            timer = setTimeout(async () => {
                try {
                    const response = await fetch(url + '?' + new URLSearchParams({ term }), { headers: { 'Accept': 'application/json' } });
                    const data = await response.json();
                    // Ohne Treffer liefert der Server ein einzelnes leeres Objekt statt einer Liste
                    results = Array.isArray(data) ? data : [];
                    list.replaceChildren(...results.map((city) => new Option(`${city.plz} ${city.name}`, city[key])));
                    fillFromMatch(key, input.value);
                } catch {
                    results = [];
                }
            }, DEBOUNCE_MS);
        });
    });

    function fillFromMatch(key, value) {
        const matches = results.filter((city) => String(city[key]) === value);
        if (matches.length !== 1) {
            return false;
        }
        const [city] = matches;
        if (fields.plz && key !== 'plz') {
            fields.plz.value = city.plz;
        }
        if (fields.name && key !== 'name') {
            fields.name.value = city.name;
        }
        if (cityId) {
            cityId.value = city.id;
        }
        return true;
    }
}
