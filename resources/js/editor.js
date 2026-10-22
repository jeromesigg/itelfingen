import 'jodit/es2021/jodit.min.css';
// Grundversion mit bold (fett/kursiv/unterstrichen) und ordered-list; die Editoren im Admin nutzen nur diese Buttons
import { Jodit } from 'jodit/esm/index.js';
import 'jodit/esm/plugins/resize-handler/resize-handler.js';
// Eingefügte Inhalte (z. B. aus Word) bereinigen
import 'jodit/esm/plugins/paste/paste.js';
import 'jodit/esm/plugins/clean-html/clean-html.js';
import de from 'jodit/esm/langs/de.js';

Jodit.lang.de = de;

window.Jodit = Jodit;
