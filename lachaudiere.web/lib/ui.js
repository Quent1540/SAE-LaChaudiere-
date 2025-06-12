import {url} from "./config.js";
let currentFilter= "actuels";
let currentSort = "date_asc";
document.getElementById('filtre-selectionne').innerHTML = 'Filtre sélectionné : ' + currentFilter;

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
    activerFavoris();
    document.querySelectorAll('.event-detail-btn').forEach(btn => {
        btn.onclick = () => {
            const id = btn.getAttribute('data-id');
            afficherDetailEvenement(id);
        };
    });
}

//affichage des événements passés
export async function displayEventsPasses() {
    const eventList = document.getElementById('event-list');
    eventList.innerHTML = 'Chargement...';

    const response = await fetch(`${url}/api/evenements`);
    const data = await response.json();

    const evenements = data.evenements.map(e => e.evenement);

    const ajd = new Date();

    //On garde seulement les événements dont la date est antérieure à aujourd'hui
    const filtered = evenements.filter(ev => new Date(ev.date_debut) < ajd);

    const source = document.getElementById('event-list-template').innerHTML;
    const template = Handlebars.compile(source);
    eventList.innerHTML = template({events: filtered});
    activerFavoris();
    document.querySelectorAll('.event-detail-btn').forEach(btn => {
        btn.onclick = () => {
            const id = btn.getAttribute('data-id');
            afficherDetailEvenement(id);
        };
    });
}

//affichage des événements futurs
export async function displayEventsFuturs() {
    const eventList = document.getElementById('event-list');
    eventList.innerHTML = 'Chargement...';

    const response = await fetch(`${url}/api/evenements`);
    const data = await response.json();

    const evenements = data.evenements.map(e => e.evenement);

    const ajd = new Date();

    // On garde seulement les événements dont la date est antérieure à aujourd'hui
    const filtered = evenements.filter(ev => new Date(ev.date_debut) > ajd);

    const source = document.getElementById('event-list-template').innerHTML;
    const template = Handlebars.compile(source);
    eventList.innerHTML = template({events: filtered});
    activerFavoris();
    document.querySelectorAll('.event-detail-btn').forEach(btn => {
        btn.onclick = () => {
            const id = btn.getAttribute('data-id');
            afficherDetailEvenement(id);
        };
    });
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

        const catRes = await fetch(`${url}/api/categories`);
        const catData = await catRes.json();
        const catObj = catData.categories.find(c => c.categorie.id_categorie == id);
        const categorie = catObj ? catObj.categorie : { libelle: "Catégorie inconnue" };

        //filtrage selon le filtre courant
        const ajd = new Date();
        let filtered = data.evenements
            .map(e => e.evenement)
            .filter(ev => {
                const date = new Date(ev.date_debut);
                switch (currentFilter) {
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

        filtered.sort((a, b) => {
            switch (currentSort) {
                case "date_asc":
                    return new Date(a.date_debut) - new Date(b.date_debut);
                case "date_desc":
                    return new Date(b.date_debut) - new Date(a.date_debut);
                case "titre":
                    return a.titre.localeCompare(b.titre);
                default:
                    return 0;
            }
        });

        //Affichage du nom de la catégorie + lien de réinitialisation
        const catSource = document.getElementById('categorie-selectionnee-template').innerHTML;
        const catTemplate = Handlebars.compile(catSource);
        catContainer.innerHTML = `
            <a href="#" id="reset-filtre">Tout réafficher</a>
            ${catTemplate(categorie)}
        `;
        document.getElementById('reset-filtre').onclick = async (e) => {
            e.preventDefault();
            selectedCategoryId = null;
            document.getElementById('categorie-selectionnee').innerHTML = '';
            if (currentFilter === "actuels") {
                await displayEventsMoisCourant();
            } else if (currentFilter === "futurs") {
                await displayEventsFuturs();
            } else if (currentFilter === "passes") {
                await displayEventsPasses();
            } else {
                await displayEvents("tous");
            }
        };

        //Affichage des événements filtrés
        if (filtered.length > 0) {
            const source = document.getElementById('event-list-template').innerHTML;
            const template = Handlebars.compile(source);
            eventList.innerHTML = template({ events: filtered });
            activerFavoris();
            document.querySelectorAll('.event-detail-btn').forEach(btn => {
                btn.onclick = () => {
                    const id = btn.getAttribute('data-id');
                    afficherDetailEvenement(id);
                };
            });
        } else {
            eventList.innerHTML = "Aucun événement pour ce filtre dans cette catégorie.";
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

export async function displayEvents(filtre = "actuels", tri = "date_asc") {
    const eventList = document.getElementById('event-list');
    eventList.innerHTML = 'Chargement...';

    try {
        if (allEvenements.length === 0) {
            const response = await fetch(`${url}/api/evenements`);
            const data = await response.json();
            allEvenements = data.evenements.map(e => e.evenement);
        }

        const ajd = new Date();

        let filtered = allEvenements.filter(ev => {
            const date = new Date(ev.date_debut);
            switch (filtre) {
                case "passes": return date < ajd;
                case "futurs": return date > ajd;
                case "actuels": return date.getMonth() === ajd.getMonth() && date.getFullYear() === ajd.getFullYear();
                case "tous":
                default: return true;
            }
        });

        //Tri selon le critère
        filtered.sort((a, b) => {
            switch (tri) {
                case "date_asc":
                    return new Date(a.date_debut) - new Date(b.date_debut);
                case "date_desc":
                    return new Date(b.date_debut) - new Date(a.date_debut);
                case "titre":
                    return a.titre.localeCompare(b.titre);
                default:
                    return 0;
            }
        });


        const source = document.getElementById('event-list-template').innerHTML;
        const template = Handlebars.compile(source);
        eventList.innerHTML = template({ events: filtered });
        activerFavoris();
        document.querySelectorAll('.event-detail-btn').forEach(btn => {
            btn.onclick = () => {
                const id = btn.getAttribute('data-id');
                afficherDetailEvenement(id);
            };
        });

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
            currentFilter = filtre;
            document.getElementById('filtre-selectionne').innerHTML = 'Filtre sélectionné : ' + currentFilter;
            if (selectedCategoryId) {
                afficherEvenementsParCategorie(selectedCategoryId);
            } else {
                displayEvents(currentFilter, currentSort);
            }
        };
    });
}

export function activerTri() {
    const boutons = document.querySelectorAll('#event-sort button');
    boutons.forEach(b => {
        b.onclick = () => {
            currentSort = b.getAttribute('data-tri');
            document.getElementById('tri-selectionne').innerHTML = 'Tri sélectionné : ' + currentSort;
            if (selectedCategoryId) {
                afficherEvenementsParCategorie(selectedCategoryId);
            } else {
                displayEvents(currentFilter, currentSort);
            }
        };
    });
}

function activerFavoris() {
    document.querySelectorAll('.favori-star').forEach(star => {
        star.onclick = function() {
            const id = this.getAttribute('data-id');
            let favoris = JSON.parse(localStorage.getItem('favoris') || '[]');
            if (favoris.includes(id)) {
                favoris = favoris.filter(f => f !== id);
            } else {
                favoris.push(id);
            }
            localStorage.setItem('favoris', JSON.stringify(favoris));
            if (selectedCategoryId) {
                afficherEvenementsParCategorie(selectedCategoryId);
            } else {
                displayEvents(currentFilter, currentSort);
            }
        };
    });
}

export async function displayFavoris() {
    const eventList = document.getElementById('event-list');
    eventList.innerHTML = 'Chargement...';

    const favoris = JSON.parse(localStorage.getItem('favoris') || '[]');

    if (allEvenements.length === 0) {
        const response = await fetch(`${url}/api/evenements`);
        const data = await response.json();
        allEvenements = data.evenements.map(e => e.evenement);
    }

    const filtered = allEvenements.filter(ev => favoris.includes(ev.id.toString()));

    const source = document.getElementById('event-list-template').innerHTML;
    const template = Handlebars.compile(source);
    eventList.innerHTML = template({ events: filtered });
    activerFavoris();
    document.querySelectorAll('.event-detail-btn').forEach(btn => {
        btn.onclick = () => {
            const id = btn.getAttribute('data-id');
            afficherDetailEvenement(id);
        };
    });
}

//Affichage du détail d'un événement
export async function afficherDetailEvenement(id) {
    const detailDiv = document.getElementById('event-detail');
    detailDiv.innerHTML = 'Chargement...';
    const response = await fetch(`${url}/api/evenements/${id}`);
    const data = await response.json();
    const evenement = data.evenement;

    if (evenement.images && evenement.images.length > 0) {
        evenement.images.forEach(image => {
            image.url = `${url}${image.url}`; 
        });
    }
    evenement.descriptionHtml = marked.parse(evenement.description || "");
    const source = document.getElementById('event-detail-template').innerHTML;
    const template = Handlebars.compile(source);
    detailDiv.innerHTML = template(evenement);
    document.getElementById('close-detail').onclick = () => detailDiv.innerHTML = '';
}