import { loadPicture } from './lib/photoloader.js';
import {displayCategory, displayComments, displayPicture} from "./lib/ui.js";
import {loadResource} from "./lib/photoloader.js";
import {first, last, load, next, prev} from "./lib/gallery";
import { getGalerie} from "./lib/gallery.js";
import {display_galerie} from "./lib/gallery_ui";

//getPicture(window.location.hash ? window.location.hash.substr(1): 105);

loadPicture(11).then((result) => {
    console.log(result.photo.descr);
}).catch((error) => {
    console.error(error);
});

//Bouton pour charger la galerie d'images
document.querySelector('#loadGalleryBtn').addEventListener('click', () => {
    load().then(() => {
        display_galerie(getGalerie());
    }).catch((error) => {
        console.error('Erreur lors du chargement de la galerie :', error);
    });
});

//Bouton pour aller à la page suivante de la galerie
document.querySelector('#next').addEventListener('click', () => {
    next().then(() => {
        display_galerie(getGalerie());
    }).catch((error) => {
        console.error('Erreur lors du chargement de la page :', error);
    });
});

//Bouton pour aller à la page précédente de la galerie
document.querySelector('#prev').addEventListener('click', () => {
    prev().then(() => {
        display_galerie(getGalerie());
    }).catch((error) => {
        console.error('Erreur lors du chargement de la page :', error);
    });
});

//Bouton pour aller à la première page de la galerie
document.querySelector('#first').addEventListener('click', () => {
    first().then(() => {
        display_galerie(getGalerie());
    }).catch((error) => {
        console.error('Erreur lors du chargement de la page :', error);
    });
});

//Bouton pour aller à la dernière page de la galerie
document.querySelector('#last').addEventListener('click', () => {
    last().then(() => {
        display_galerie(getGalerie());
    }).catch((error) => {
        console.error('Erreur lors du chargement de la page :', error);
    });
});

//Récupère une image par son ID
const getPicture = (id) => {
    loadPicture(id)
        .then((data) => {
            console.log(`Titre: ${data.photo.titre}`);
            console.log(`Type: ${data.photo.type}`);
            console.log(`URL: ${data.photo.file}`);
            displayPicture(data.photo);
            getCategory(data).then ((categoryData) => {
             displayCategory(categoryData);
            });
            getComments(data).then ((commentsData) => {
                displayComments(commentsData)
            });
        })
        .catch((error) => {
            console.error('Erreur lors de la récupération des données:', error);
        });
};

//Récupère la catégorie d'une image
const getCategory = (imageData) => {
    const categoryUri = 'https://webetu.iutnc.univ-lorraine.fr'+imageData.links.categorie.href;
    return loadResource(categoryUri)
        .then((categoryData) => {
            console.log('Données de la catégorie récupérées :', categoryData);
            return categoryData; //Retourne les données de la catégorie
        })
        .catch((error) => {
            console.error('Erreur lors de la récupération de la catégorie :', error);
            throw error;
        });
};

//Récupère les commentaires d'une image
const getComments = (imageData) => {
    const commentsUri = 'https://webetu.iutnc.univ-lorraine.fr'+imageData.links.comments.href;
    return loadResource(commentsUri)
        .then((commentsData) => {
            console.log('Données des commentaires récupérées :', commentsData);
            return commentsData; //Retourne les données des commentaires
        })
        .catch((error) => {
            console.error('Erreur lors de la récupération des commentaires :', error);
            throw error;
        });
}

const photoId = window.location.hash ? window.location.hash.substring(1) : 105;
getPicture(photoId);