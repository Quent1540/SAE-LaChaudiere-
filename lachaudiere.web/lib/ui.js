import {url} from "./config.js";
let currentFilter= "actuels";
let currentSort = "date_asc";
document.getElementById('filtre-selectionne').innerHTML = 'Filtre sélectionné : ' + currentFilter;

//Affichage des événements courants
export async function displayEventsMoisCourant() {
    const eventList = document.getElementById('event-list');
    eventList.innerHTML = 'Chargement...';

    //On récup les événements depuis l'API
    const response = await fetch(`${url}/api/evenements`);
    const data = await response.json();

    //On extrait les événements de la réponse
    const evenements = data.evenements.map(e => e.evenement);

    //Mois et année courants
    const ajd = new Date();
    const anneeCourante = ajd.getFullYear();
    const moisCourant = String(ajd.getMonth() + 1).padStart(2, '0');
    const currentPrefix = `${anneeCourante}-${moisCourant}`;

    //On garde que les événements du mois courant
    const filtered = evenements.filter(ev => ev.date_debut.startsWith(currentPrefix));

    //Rendu Handlebars
    const source = document.getElementById('event-list-template').innerHTML;
    const template = Handlebars.compile(source);
    eventList.innerHTML = template({events: filtered});

    //Activer les favoris
    activerFavoris();

    //Activer les boutons de détails
    document.querySelectorAll('.event-detail-btn').forEach(btn => {
        btn.onclick = () => {
            const id = btn.getAttribute('data-id');
            afficherDetailEvenement(id);
        };
    });
}

//Affichage des événements passés
export async function displayEventsPasses() {
    const eventList = document.getElementById('event-list');
    eventList.innerHTML = 'Chargement...';

    //On récup les événements depuis l'API
    const response = await fetch(`${url}/api/evenements`);
    const data = await response.json();

    //On extrait les événements de la réponse
    const evenements = data.evenements.map(e => e.evenement);

    //Date d'aujourd'hui
    const ajd = new Date();

    //On garde seulement les événements dont la date est antérieure à aujourd'hui
    const filtered = evenements.filter(ev => new Date(ev.date_debut) < ajd);

    //Rendu Handlebars
    const source = document.getElementById('event-list-template').innerHTML;
    const template = Handlebars.compile(source);
    eventList.innerHTML = template({events: filtered});

    //Activer les favoris
    activerFavoris();

    //Activer les boutons de détails
    document.querySelectorAll('.event-detail-btn').forEach(btn => {
        btn.onclick = () => {
            const id = btn.getAttribute('data-id');
            afficherDetailEvenement(id);
        };
    });
}

//Affichage des événements futurs
export async function displayEventsFuturs() {
    const eventList = document.getElementById('event-list');
    eventList.innerHTML = 'Chargement...';

    //On récup les événements depuis l'API
    const response = await fetch(`${url}/api/evenements`);
    const data = await response.json();

    //On extrait les événements de la réponse
    const evenements = data.evenements.map(e => e.evenement);

    //Date d'aujourd'hui
    const ajd = new Date();

    //On garde seulement les événements dont la date est supérieure à aujourd'hui
    const filtered = evenements.filter(ev => new Date(ev.date_debut) > ajd);

    //Rendu Handlebars
    const source = document.getElementById('event-list-template').innerHTML;
    const template = Handlebars.compile(source);
    eventList.innerHTML = template({events: filtered});

    //Activer les favoris
    activerFavoris();

    //Activer les boutons de détails
    document.querySelectorAll('.event-detail-btn').forEach(btn => {
        btn.onclick = () => {
            const id = btn.getAttribute('data-id');
            afficherDetailEvenement(id);
        };
    });
}

//Clic sur une categorie pour afficher les événements
async function afficherEvenementsParCategorie(id) {
    const eventList = document.getElementById('event-list');
    const catContainer = document.getElementById('categorie-selectionnee');
    eventList.innerHTML = 'Chargement...';
    catContainer.innerHTML = '';

    try {
        //On récup les événements de la catégorie depuis l'API
        const response = await fetch(`${url}/api/categories/${id}/evenements`);
        const data = await response.json();

        //Récup de la catégorie
        const catRes = await fetch(`${url}/api/categories`);
        const catData = await catRes.json();
        const catObj = catData.categories.find(c => c.categorie.id_categorie == id);
        const categorie = catObj ? catObj.categorie : { libelle: "Catégorie inconnue" };

        //Filtrage selon le filtre courant
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

        //Tri selon le critère courant
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
            //Réaffichage selon le filtre courant
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
        console.error(err);
    }
}

//Pour stocker la catégorie sélectionnée
let selectedCategoryId = null;

//Affichage de la liste des catégories
export async function afficherCategories() {
    const container = document.getElementById('categories-list');
    container.innerHTML = 'Chargement...';

    try {
        //On récup les catégories depuis l'API
        const res = await fetch(`${url}/api/categories`);
        const data = await res.json();

        //Rendu Handlebars
        const source = document.getElementById('categories-list-template').innerHTML;
        const template = Handlebars.compile(source);
        container.innerHTML = template({ categories: data.categories });

        //Gestion des clics sur les liens de catégorie
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

//Init de la liste des événements
let allEvenements = [];

//Affichage de tous les événements selon le filtre et le tri
export async function displayEvents(filtre = "actuels", tri = "date_asc") {
    const eventList = document.getElementById('event-list');
    eventList.innerHTML = 'Chargement...';

    try {
        //Si on n'a pas encore chargé les événements, on les récupère
        if (allEvenements.length === 0) {
            const response = await fetch(`${url}/api/evenements`);
            const data = await response.json();
            allEvenements = data.evenements.map(e => e.evenement);
        }

        const ajd = new Date();

        //Filtrage selon le filtre sélectionné
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

        //Rendu Handlebars
        const source = document.getElementById('event-list-template').innerHTML;
        const template = Handlebars.compile(source);
        eventList.innerHTML = template({ events: filtered });

        //Activer les favoris
        activerFavoris();

        //Activer les boutons de détails
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

//Activation des filtres
export function activerFiltres() {
    const boutons = document.querySelectorAll('#event-filters button');
    boutons.forEach(b => {
        b.onclick = () => {
            //Récup du filtre sélectionné
            const filtre = b.getAttribute('data-filtre');
            currentFilter = filtre;
            document.getElementById('filtre-selectionne').innerHTML = 'Filtre sélectionné : ' + currentFilter;
            //Affichage de la catégorie sélectionnée ou non
            if (selectedCategoryId) {
                afficherEvenementsParCategorie(selectedCategoryId);
            } else {
                displayEvents(currentFilter, currentSort);
            }
        };
    });
}

//Activation des boutons de tri
export function activerTri() {
    const boutons = document.querySelectorAll('#event-sort button');
    boutons.forEach(b => {
        b.onclick = () => {
            //Récup du tri sélectionné
            currentSort = b.getAttribute('data-tri');
            document.getElementById('tri-selectionne').innerHTML = 'Tri sélectionné : ' + currentSort;
            //Affichage de la catégorie sélectionnée ou non
            if (selectedCategoryId) {
                afficherEvenementsParCategorie(selectedCategoryId);
            } else {
                displayEvents(currentFilter, currentSort);
            }
        };
    });
}

//Activation des favoris
function activerFavoris() {
    document.querySelectorAll('.favori-star').forEach(star => {
        star.onclick = function() {
            //Récup de l'id de l'événement
            const id = this.getAttribute('data-id');
            let favoris = JSON.parse(localStorage.getItem('favoris') || '[]');
            //Ajout/suppression de l'événement dans les favoris
            if (favoris.includes(id)) {
                favoris = favoris.filter(f => f !== id);
            } else {
                favoris.push(id);
            }
            //Maj du localStorage
            localStorage.setItem('favoris', JSON.stringify(favoris));
            //Maj de l'affichage
            if (selectedCategoryId) {
                afficherEvenementsParCategorie(selectedCategoryId);
            } else {
                displayEvents(currentFilter, currentSort);
            }
        };
    });
}

//Affichage des événements favoris
export async function displayFavoris() {
    const eventList = document.getElementById('event-list');
    eventList.innerHTML = 'Chargement...';

    //On récup les favoris depuis le localStorage
    const favoris = JSON.parse(localStorage.getItem('favoris') || '[]');

    //Si on n'a pas encore chargé les événements, on les récup
    if (allEvenements.length === 0) {
        const response = await fetch(`${url}/api/evenements`);
        const data = await response.json();
        allEvenements = data.evenements.map(e => e.evenement);
    }

    //Filtrage des événements favoris
    const filtered = allEvenements.filter(ev => favoris.includes(ev.id.toString()));

    //Rendu Handlebars
    const source = document.getElementById('event-list-template').innerHTML;
    const template = Handlebars.compile(source);
    eventList.innerHTML = template({ events: filtered });

    //Activer les favoris
    activerFavoris();

    //Activer les boutons de détails
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

    //On récup l'événement depuis l'API
    const response = await fetch(`${url}/api/evenements/${id}`);
    const data = await response.json();
    const evenement = data.evenement;

    if (evenement.images && evenement.images.length > 0) {
        evenement.images.forEach(image => {
            if (image.url && !image.url.startsWith('http')) {
                image.url = `${url}${image.url}`; 
            }
        });
    }
    evenement.descriptionHtml = marked.parse(evenement.description || "");

    //Rendu Handlebars
    const source = document.getElementById('event-detail-template').innerHTML;
    const template = Handlebars.compile(source);
    detailDiv.innerHTML = template(evenement);

    //Fermeture du détail
    document.getElementById('close-detail').onclick = () => detailDiv.innerHTML = '';
}