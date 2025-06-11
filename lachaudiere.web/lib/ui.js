//Affichage de la catégorie de l'image
import {url} from "./config.js";
//Affichage des événements courants
export async function displayEventsMoisCourant() {
    const eventList = document.getElementById('event-list');
    eventList.innerHTML = 'Chargement...';

    const response = await fetch(`${url}/api/evenements`);
    const data = await response.json();

    const evenements = data.evenements.map(e => e.evenement);

    const ajd = new Date();
    const anneeCourante = ajd.getFullYear();
    const moisCourant = String(ajd.getMonth() + 1).padStart(2, '0');
    const currentPrefix = `${anneeCourante}-${moisCourant}`;

    const filtered = evenements.filter(ev => ev.date_debut.startsWith(currentPrefix));

    const source = document.getElementById('event-list-template').innerHTML;
    const template = Handlebars.compile(source);
    eventList.innerHTML = template({events: filtered});
}

//click sur une categorie pour afficher les événements
async function afficherEvenementsParCategorie(id) {
    const eventList = document.getElementById('event-list');
    const catContainer = document.getElementById('categorie-selectionnee');
    eventList.innerHTML = 'Chargement...';
    catContainer.innerHTML = '';

    try {
        const response = await fetch(`${url}/api/categories/${id}/evenements`);
        const data = await response.json();

        //On recupere le nom de la catégorie
        const catRes = await fetch(`${url}/api/categories`);
        const catData = await catRes.json();

        const catObj = catData.categories.find(c => c.categorie.id_categorie == id);
        const categorie = catObj ? catObj.categorie : { libelle: "Catégorie inconnue" };

        const ajd = new Date();
        const anneeCourante = ajd.getFullYear();
        const moisCourant = String(ajd.getMonth() + 1).padStart(2, '0');
        const currentPrefix = `${anneeCourante}-${moisCourant}`;
        const filtered = data.evenements
            .map(e => e.evenement)
            .filter(ev => ev.date_debut.startsWith(currentPrefix));

        const catSource = document.getElementById('categorie-selectionnee-template').innerHTML;
        const catTemplate = Handlebars.compile(catSource);
        catContainer.innerHTML = catTemplate(categorie);
        catContainer.innerHTML = `
        <a href="#" id="reset-filtre">Tout réafficher</a>
        ${catTemplate(categorie)}`;
        //On ajoute un listener pour le lien de réinitialisation du filtre
        document.getElementById('reset-filtre').onclick = async (e) => {
            e.preventDefault();
            selectedCategoryId = null;
            document.getElementById('categorie-selectionnee').innerHTML = '';
            await displayEventsMoisCourant();
        };

        if (filtered.length > 0) {
            const source = document.getElementById('event-list-template').innerHTML;
            const template = Handlebars.compile(source);
            eventList.innerHTML = `
        ${template({ events: filtered })}
    `;
        } else {
            eventList.innerHTML = "Aucun événement pour ce mois dans cette catégorie.";
        }
    } catch (err) {
        eventList.textContent = "Erreur lors du chargement des événements";
        catContainer.innerHTML = '';
    }
}

//Affichage de la liste des catégories
let selectedCategoryId = null;

export async function afficherCategories() {
    const container = document.getElementById('categories-list');
    container.innerHTML = 'Chargement...';

    try {
        const res = await fetch(`${url}/api/categories`);
        const data = await res.json();

        const source = document.getElementById('categories-list-template').innerHTML;
        const template = Handlebars.compile(source);
        container.innerHTML = template({ categories: data.categories });

        container.querySelectorAll('a[data-id]').forEach(lien => {
            const id = lien.getAttribute('data-id');
            lien.onclick = async (e) => {
                e.preventDefault();
                if (selectedCategoryId !== id) {
                    selectedCategoryId = id;
                    await afficherEvenementsParCategorie(id);
                }
            };
        });
    } catch (err) {
        container.textContent = "Erreur lors du chargement des catégories";
    }
}

let allEvenements = [];

export async function displayEvents(filtre = "actuels") {
    const eventList = document.getElementById('event-list');
    eventList.innerHTML = 'Chargement...';

    try {
        if (allEvenements.length === 0) {
            const response = await fetch(`${url}/api/evenements`);
            const data = await response.json();
            allEvenements = data.evenements.map(e => e.evenement);
        }

        const ajd = new Date();

        const filtered = allEvenements.filter(ev => {
            const date = new Date(ev.date_debut);
            switch (filtre) {
                case "passes":
                    return date < ajd;
                case "futurs":
                    return date > ajd;
                case "actuels":
                    return date.getMonth() === ajd.getMonth() && date.getFullYear() === ajd.getFullYear();
                case "tous":
                default:
                    return true;
            }
        });

        const source = document.getElementById('event-list-template').innerHTML;
        const template = Handlebars.compile(source);
        eventList.innerHTML = template({ events: filtered });

    } catch (err) {
        eventList.textContent = "Erreur lors du chargement des événements.";
        console.error(err);
    }
}

export function activerFiltres() {
    const boutons = document.querySelectorAll('#event-filters button');
    boutons.forEach(b => {
        b.onclick = () => {
            const filtre = b.getAttribute('data-filtre');
            displayEvents(filtre);
        };
    });
}

