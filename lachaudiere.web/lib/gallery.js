// lib/gallery.js
import { loadResource } from "./photoloader.js";

let galerie = [];

//Chargement de la galerie d'images depuis l'API
export const load = (page = 1) => {
    return new Promise((resolve, reject) => {
        loadResource(`https://webetu.iutnc.univ-lorraine.fr/www/canals5/phox/api/photos?page=${page}`)
            .then((data) => {
                galerie = data;
                resolve(galerie);
            })
            .catch((error) => {
                reject(error);
            });
    });
};

//Page suivante de la galerie
export const next = () => {
    return new Promise((resolve, reject) => {
        const nextHref = galerie.links.next?.href;
        const params = new URLSearchParams(nextHref.split('?')[1]);
        const nextPage = params.get('page');
        load(Number(nextPage))
            .then(resolve)
            .catch(reject);
    });
}

//Page précédente de la galerie
export const prev = () => {
    return new Promise((resolve, reject) => {
        const prevHref = galerie.links.prev?.href;
        const params = new URLSearchParams(prevHref.split('?')[1]);
        const prevPage = params.get('page');
        load(Number(prevPage))
            .then(resolve)
            .catch(reject);
    });
}

//Première page de la galerie
export const first = () => {
    return new Promise((resolve, reject) => {
        const firstHref = galerie.links.first?.href;
        const params = new URLSearchParams(firstHref.split('?')[1]);
        const firstPage = params.get('page');
        load(Number(firstPage))
            .then(resolve)
            .catch(reject);
    });
}

//Dernière page de la galerie
export const last = () => {
    return new Promise((resolve, reject) => {
        const lastHref = galerie.links.last?.href;
        const params = new URLSearchParams(lastHref.split('?')[1]);
        const lastPage = params.get('page');
        load(Number(lastPage))
            .then(resolve)
            .catch(reject);
    });
}

export const getGalerie = () => galerie;