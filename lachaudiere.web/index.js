import { url } from './lib/config.js';

import {activerFiltres, activerTri, displayEvents, displayEventsMoisCourant} from "./lib/ui.js";
import { afficherCategories } from "./lib/ui.js";

//Quand le DOM est entièrement chargé
window.addEventListener('DOMContentLoaded', () => {
    //Afficher la liste des catégories
    afficherCategories();

    //Afficher les événements du mois courant, triés par date croissante
    displayEvents("actuels", "date_asc");

    //Activer les filtres (passés, futurs, actuels, tous)
    activerFiltres();

    //Activer le tri (date, titre, etc)
    activerTri();

    //Enregistre un helper Handlebars pour vérifier si un événement est favori
    Handlebars.registerHelper('isFavori', function(id) {
        const favoris = JSON.parse(localStorage.getItem('favoris') || '[]');
        return favoris.includes(id.toString());
    });

    //Gestion du clic sur le bouton favoris
    document.getElementById('afficher-favoris').onclick = () => {
        import('./lib/ui.js').then(module => {
            module.displayFavoris();
        });
    };
});

