import {loadPicture} from "./photoloader";

//Affichage de la galerie d'images
export const display_galerie = (galerie) => {
    const photos = galerie.photos.map(item => ({
        url: 'https://webetu.iutnc.univ-lorraine.fr' + item.photo.original.href,
        title: item.photo.titre,
        id: item.photo.id
    }));

    const templateSource = document.querySelector('#galleryTemplate').innerHTML;
    const template = Handlebars.compile(templateSource);
    const galerieContainer = document.querySelector('#galerie');
    galerieContainer.innerHTML = template({photos});

    galerieContainer.addEventListener('click', (e) => {
        const img = e.target.closest('img[data-photoid]');
        if (img) {
            const photoId = img.getAttribute('data-photoid');
            loadPicture(photoId).then(data => {
                // Affiche la photo dans #la_photo
                const laPhoto = document.querySelector('#la_photo');
                laPhoto.innerHTML = `<img src="https://webetu.iutnc.univ-lorraine.fr${data.photo.url.href}" alt="${data.photo.titre}"><p>${data.photo.titre}</p>`;
            });
        }
    });
}