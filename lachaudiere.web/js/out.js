(() => {
  var __async = (__this, __arguments, generator) => {
    return new Promise((resolve, reject) => {
      var fulfilled = (value) => {
        try {
          step(generator.next(value));
        } catch (e) {
          reject(e);
        }
      };
      var rejected = (value) => {
        try {
          step(generator.throw(value));
        } catch (e) {
          reject(e);
        }
      };
      var step = (x) => x.done ? resolve(x.value) : Promise.resolve(x.value).then(fulfilled, rejected);
      step((generator = generator.apply(__this, __arguments)).next());
    });
  };

  // lib/config.js
  var url = "http://localhost:8000";

  // lib/ui.js
  function afficherCategories() {
    return __async(this, null, function* () {
      const container = document.getElementById("categories-list");
      container.innerHTML = "Chargement...";
      fetch(`${url}/api/categories`).then((res) => res.json()).then((data) => {
        container.innerHTML = "";
        data.categories.forEach((cat) => {
          const c = cat.categorie;
          const lien = document.createElement("a");
          lien.href = "#";
          lien.textContent = c.libelle;
          lien.style.display = "block";
          lien.onclick = (e) => {
            e.preventDefault();
            afficherEvenementsParCategorie(c.id_categorie);
          };
          container.appendChild(lien);
        });
      }).catch((err) => {
        document.getElementById("categories-list").textContent = "Erreur lors du chargement des cat\xE9gories";
      });
    });
  }

  // index.js
  window.addEventListener("DOMContentLoaded", afficherCategories);
})();
//# sourceMappingURL=out.js.map
