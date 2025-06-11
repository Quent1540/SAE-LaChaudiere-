import { url } from './lib/config.js';

import {activerFiltres, activerTri, displayEvents, displayEventsMoisCourant} from "./lib/ui.js";
import { afficherCategories } from "./lib/ui.js";

window.addEventListener('DOMContentLoaded', () => {
    afficherCategories();
    displayEvents("actuels", "date_asc");
    activerFiltres();
    activerTri();
});

