import Handlebars from "handlebars";

//Affichage d'une image
export const displayPicture = (picture) => {
    //Charger et compiler le template
    const templateSource = document.querySelector('#photoTemplate').innerHTML;
    const template = Handlebars.compile(templateSource);
    const photoContainer = document.querySelector('#la_photo');
    photoContainer.innerHTML = template({url: 'https://webetu.iutnc.univ-lorraine.fr/'+picture.url.href, title: picture.titre, description: picture.descr, type: picture.type, file: picture.file});
};

//Affichage de la catégorie de l'image
export const displayCategory = (category) => {
    //Charger et compiler le template
    const templateSource = document.querySelector('#categoryTemplate').innerHTML;
    const template = Handlebars.compile(templateSource);
    const categoryContainer = document.querySelector('#la_categorie');
    categoryContainer.innerHTML = template({nom: category.categorie.nom, description: category.categorie.descr});
};

//Affichage des commentaires
export const displayComments = (comments) => {
    const templateSource = document.querySelector('#commentTemplate').innerHTML;
    const template = Handlebars.compile(templateSource);
    const commentsContainer = document.querySelector('#les_commentaires');
    console.log(comments);
    commentsContainer.innerHTML = template({commentaires: comments.comments});
}