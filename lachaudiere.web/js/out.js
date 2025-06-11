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
  function displayEventsMoisCourant() {
    return __async(this, null, function* () {
      const eventList = document.getElementById("event-list");
      eventList.innerHTML = "Chargement...";
      const response = yield fetch(`${url}/api/evenements`);
      const data = yield response.json();
      const evenements = data.evenements.map((e) => e.evenement);
      const ajd = /* @__PURE__ */ new Date();
      const anneeCourante = ajd.getFullYear();
      const moisCourant = String(ajd.getMonth() + 1).padStart(2, "0");
      const currentPrefix = `${anneeCourante}-${moisCourant}`;
      const filtered = evenements.filter((ev) => ev.date_debut.startsWith(currentPrefix));
      const source = document.getElementById("event-list-template").innerHTML;
      const template = Handlebars.compile(source);
      eventList.innerHTML = template({ events: filtered });
    });
  }
  function afficherEvenementsParCategorie(id) {
    return __async(this, null, function* () {
      const eventList = document.getElementById("event-list");
      const catContainer = document.getElementById("categorie-selectionnee");
      eventList.innerHTML = "Chargement...";
      catContainer.innerHTML = "";
      try {
        const response = yield fetch(`${url}/api/categories/${id}/evenements`);
        const data = yield response.json();
        const catRes = yield fetch(`${url}/api/categories`);
        const catData = yield catRes.json();
        const catObj = catData.categories.find((c) => c.categorie.id_categorie == id);
        const categorie = catObj ? catObj.categorie : { libelle: "Cat\xE9gorie inconnue" };
        const ajd = /* @__PURE__ */ new Date();
        const anneeCourante = ajd.getFullYear();
        const moisCourant = String(ajd.getMonth() + 1).padStart(2, "0");
        const currentPrefix = `${anneeCourante}-${moisCourant}`;
        const filtered = data.evenements.map((e) => e.evenement).filter((ev) => ev.date_debut.startsWith(currentPrefix));
        const catSource = document.getElementById("categorie-selectionnee-template").innerHTML;
        const catTemplate = Handlebars.compile(catSource);
        catContainer.innerHTML = catTemplate(categorie);
        catContainer.innerHTML = `
        <a href="#" id="reset-filtre">Tout r\xE9afficher</a>
        ${catTemplate(categorie)}`;
        document.getElementById("reset-filtre").onclick = (e) => __async(this, null, function* () {
          e.preventDefault();
          selectedCategoryId = null;
          document.getElementById("categorie-selectionnee").innerHTML = "";
          yield displayEventsMoisCourant();
        });
        if (filtered.length > 0) {
          const source = document.getElementById("event-list-template").innerHTML;
          const template = Handlebars.compile(source);
          eventList.innerHTML = `
        ${template({ events: filtered })}
    `;
        } else {
          eventList.innerHTML = "Aucun \xE9v\xE9nement pour ce mois dans cette cat\xE9gorie.";
        }
      } catch (err) {
        eventList.textContent = "Erreur lors du chargement des \xE9v\xE9nements";
        catContainer.innerHTML = "";
      }
    });
  }
  var selectedCategoryId = null;
  function afficherCategories() {
    return __async(this, null, function* () {
      const container = document.getElementById("categories-list");
      container.innerHTML = "Chargement...";
      try {
        const res = yield fetch(`${url}/api/categories`);
        const data = yield res.json();
        const source = document.getElementById("categories-list-template").innerHTML;
        const template = Handlebars.compile(source);
        container.innerHTML = template({ categories: data.categories });
        container.querySelectorAll("a[data-id]").forEach((lien) => {
          const id = lien.getAttribute("data-id");
          lien.onclick = (e) => __async(this, null, function* () {
            e.preventDefault();
            if (selectedCategoryId !== id) {
              selectedCategoryId = id;
              yield afficherEvenementsParCategorie(id);
            }
          });
        });
      } catch (err) {
        container.textContent = "Erreur lors du chargement des cat\xE9gories";
      }
    });
  }

  // index.js
  window.addEventListener("DOMContentLoaded", afficherCategories);
  window.addEventListener("DOMContentLoaded", displayEventsMoisCourant());
})();
//# sourceMappingURL=out.js.map
