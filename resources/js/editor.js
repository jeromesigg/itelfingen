import 'jodit/es2021/jodit.min.css';
import { Jodit } from 'jodit/esm/index.js';
import 'jodit/esm/plugins/all.js';
import de from 'jodit/esm/langs/de.js';

Jodit.lang.de = de;

window.Jodit = Jodit;
