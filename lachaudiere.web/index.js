import { url } from './lib/config.js';

import {activerFiltres, displayEvents, displayEventsMoisCourant} from "./lib/ui.js";
import { afficherCategories } from "./lib/ui.js";


window.addEventListener('DOMContentLoaded', afficherCategories);
window.addEventListener('DOMContentLoaded', () => {
    afficherCategories();
    displayEvents("actuels");
    activerFiltres();
});
