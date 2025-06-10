import { url } from './lib/config.js';

import { displayEventsMoisCourant } from "./lib/ui.js";
import { afficherCategories } from "./lib/ui.js";


window.addEventListener('DOMContentLoaded', afficherCategories);
window.addEventListener('DOMContentLoaded', displayEventsMoisCourant());
