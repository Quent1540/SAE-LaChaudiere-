//Affichage de la catégorie de l'image
import {url} from "./config.js";
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

//Affichage de la liste des catégories
export async function afficherCategories() {
    const container = document.getElementById('categories-list');
    fetch(`${url}/api/categories`)
        .then(res => res.json())
        .then(data => {
            container.innerHTML='';
            data.categories.forEach(cat => {
                const c = cat.categorie;
                const lien = document.createElement('a');
                lien.href = "#";
                lien.textContent = c.libelle;
                lien.style.display = "block";
                lien.onclick = (e) => {
                    e.preventDefault();
                    afficherEvenementsParCategorie(c.id_categorie);
                };
                container.appendChild(lien);
            });
        })
        .catch(err => {
            document.getElementById('categories-list').textContent = "Erreur lors du chargement des catégories";
        });
}
