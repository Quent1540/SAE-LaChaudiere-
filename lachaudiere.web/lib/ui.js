//Affichage de la catégorie de l'image
import {url} from "./config.js";

export const displayCategory = (category) => {
    //Charger et compiler le template
    const templateSource = document.querySelector('#categoryTemplate').innerHTML;
    const template = Handlebars.compile(templateSource);
    const categoryContainer = document.querySelector('#la_categorie');
    categoryContainer.innerHTML = template({nom: category.categorie.nom, description: category.categorie.descr});
};

//Affichage des événements courants
export async function displayEventsMoisCourant() {
    const response = await fetch(`${url}/api/evenements`);
    const data = await response.json();

    //Extraction du tableau d'événements
    const evenements = data.evenements.map(e => e.evenement);

    //Récup mois et année courants
    const ajd = new Date();
    const anneeCourante = ajd.getFullYear();
    const moisCourant = String(ajd.getMonth() + 1).padStart(2, '0');
    const currentPrefix = `${anneeCourante}-${moisCourant}`;

    //Filtre des événements pour le mois courant
    const filtered = evenements.filter(ev => ev.date_debut.startsWith(currentPrefix));

    const source = document.getElementById('event-list-template').innerHTML;
    const template = Handlebars.compile(source);
    document.getElementById('event-list').innerHTML = template({events: filtered});
}