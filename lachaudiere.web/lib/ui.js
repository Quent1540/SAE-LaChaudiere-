import Handlebars from "handlebars";

//Affichage de la catégorie de l'image
export const displayCategory = (category) => {
    //Charger et compiler le template
    const templateSource = document.querySelector('#categoryTemplate').innerHTML;
    const template = Handlebars.compile(templateSource);
    const categoryContainer = document.querySelector('#la_categorie');
    categoryContainer.innerHTML = template({nom: category.categorie.nom, description: category.categorie.descr});
};