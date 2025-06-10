import { url } from './config.js';

//Chargement d'une immage depuis l'API avec son id
export const loadPicture = (idPicture) => {
    return new Promise((resolve, reject) => {
        fetch(`${url}/photos/` + idPicture)
            .then((response) => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Network response was not ok');
                }
            })
            .then((data) => {
                resolve(data);
            })
            .catch((error) => {
                reject(error);
            });
    });
};

//Chargement d'une ressource générique depuis l'API
export const loadResource = (uri) => {
    return new Promise((resolve, reject) => {
        fetch(uri)
            .then((response) => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Network response was not ok');
                }
            })
            .then((data) => {
                resolve(data);
            })
            .catch((error) => {
                reject(error);
            });
    })
}