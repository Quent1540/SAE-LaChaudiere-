import 'package:lachaudiere_app/models/categorie.dart';

class Evenement {
  final int id;
  final String titre;
  final String? description;
  final DateTime dateDebut;
  final DateTime? dateFin;
  final Categorie categorie;

  Evenement({
    required this.id,
    required this.titre,
    this.description,
    required this.dateDebut,
    this.dateFin,
    required this.categorie,
  });

  factory Evenement.fromJson(Map<String, dynamic> evenementJson, Map<String, dynamic> linksJson) {
    
    final categorie = Categorie(
      id: evenementJson['id_categorie'] as int? ?? 0,
      libelle: evenementJson['categorie_libelle'] as String? ?? 'Non classé',
    );

    final String selfHref = linksJson['self']['href'];
    final int id = int.tryParse(selfHref.split('/').last) ?? -1;

    return Evenement(
      id: id,
      titre: evenementJson['titre'] as String? ?? 'Titre inconnu',
      
      description: evenementJson['description'] as String?, 
      
      dateDebut: DateTime.parse(evenementJson['date_debut']),
      
      dateFin: evenementJson['date_fin'] != null ? DateTime.parse(evenementJson['date_fin']) : null,
      
      categorie: categorie,
    );
  }
}