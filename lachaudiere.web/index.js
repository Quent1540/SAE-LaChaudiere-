import { url } from './lib/config.js';

import {activerFiltres, activerTri, displayEvents, displayEventsMoisCourant} from "./lib/ui.js";
import { afficherCategories } from "./lib/ui.js";

window.addEventListener('DOMContentLoaded', () => {
    afficherCategories();
    displayEvents("actuels", "date_asc");
    activerFiltres();
    activerTri();
    Handlebars.registerHelper('isFavori', function(id) {
        const favoris = JSON.parse(localStorage.getItem('favoris') || '[]');
        return favoris.includes(id.toString());
    });
    document.getElementById('afficher-favoris').onclick = () => {
        import('./lib/ui.js').then(module => {
            module.displayFavoris();
        });
    };
});

