-- Utilisateurs (1 admin, 1 utilisateur normal)
INSERT INTO utilisateurs (email, mot_de_passe_hash, role) VALUES
                                                              ('admin@chaudiere.org', SHA2('admin123', 256), 1),
                                                              ('user@chaudiere.org', SHA2('user123', 256), 0);

-- Catégories
INSERT INTO categories (libelle, description) VALUES
                                                  ('Concert', 'Musique live avec des artistes locaux ou internationaux'),
                                                  ('Exposition', 'Expositions artistiques ou thématiques'),
                                                  ('Conférence', 'Interventions et discussions sur divers sujets');

-- Événements
INSERT INTO evenements (titre, description, tarif, date_debut, date_fin, id_categorie, est_publie, id_utilisateur_creation) VALUES
                                                                                                                                ('Jazz & Vin', 'Soirée jazz accompagnée d’une dégustation de vins locaux.', '12€', '2025-06-15 20:00:00', NULL, 1, 1, 1),
                                                                                                                                ('Expo Photo : Regards Urbains', 'Une exposition des meilleurs clichés urbains du collectif local.', 'Entrée libre', '2025-06-01 10:00:00', '2025-06-30 18:00:00', 2, 1, 2),
                                                                                                                                ('Conférence : IA & Culture', 'Débat sur l’impact de l’intelligence artificielle dans le secteur culturel.', '5€', '2025-06-20 18:30:00', NULL, 3, 1, 1),
                                                                                                                                ('Concert Rock - Les Inflammables', 'Groupe de rock local en tournée nationale.', '10€', '2025-07-05 21:00:00', NULL, 1, 0, 2); -- non publié

-- Images associées aux événements
INSERT INTO images_evenements (id_evenement, url_image, legende, ordre_affichage) VALUES
                                                                                      (1, 'https://example.com/images/jazzvin.jpg', 'Ambiance jazz & vin', 1),
                                                                                      (2, 'https://example.com/images/expo_regards1.jpg', 'Affiche de l’expo', 1),
                                                                                      (2, 'https://example.com/images/expo_regards2.jpg', 'Vue intérieure', 2),
                                                                                      (3, 'https://example.com/images/ia_culture.jpg', 'Visuel conférence IA', 1),
                                                                                      (4, 'https://example.com/images/rock_inflammables.jpg', 'Les Inflammables sur scène', 1);