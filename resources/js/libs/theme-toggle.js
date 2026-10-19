/**
 * Umschalter Hell/Dunkel im Admin (components/toggle-switch).
 *
 * Das Thema beim Laden setzt das Inline-Skript im <head> von layouts/admin (sonst blitzt die Seite hell auf);
 * hier werden nur Symbol und Zustand angezeigt und die Wahl gespeichert.
 */
const STORAGE_KEY = 'color-theme';

export function initThemeToggle(button) {
    const darkIcon = document.getElementById('theme-toggle-dark-icon');
    const lightIcon = document.getElementById('theme-toggle-light-icon');

    function update() {
        const dark = document.documentElement.classList.contains('dark');
        // Das Symbol zeigt, wohin der Klick wechselt
        lightIcon?.classList.toggle('hidden', !dark);
        darkIcon?.classList.toggle('hidden', dark);
        button.setAttribute('aria-pressed', String(dark));
    }

    button.addEventListener('click', () => {
        const dark = document.documentElement.classList.toggle('dark');
        try {
            localStorage.setItem(STORAGE_KEY, dark ? 'dark' : 'light');
        } catch {
            // Ohne Speicher (privates Fenster) gilt die Wahl nur bis zum nächsten Laden
        }
        update();
    });
    update();
}
