import { url } from './lib/config.js';

//Affichage de la liste des catégories
function afficherCategories() {
    fetch(`${url}/api/categories`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('categories-list');
            container.innerHTML = '';
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
// Initialisation
afficherCategories();