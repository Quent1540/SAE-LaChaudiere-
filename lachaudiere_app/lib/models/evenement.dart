import 'package:lachaudiere_app/models/categorie.dart';
import 'package:lachaudiere_app/models/image.dart';

class Evenement {
  final int id;
  final String titre;
  String? description;
  final DateTime dateDebut;
  final DateTime? dateFin;
  final Categorie categorie;
  List<ImageEvenement> images;

  Evenement({
    required this.id,
    required this.titre,
    this.description,
    required this.dateDebut,
    this.dateFin,
    required this.categorie,
    this.images = const [],
  });

  factory Evenement.fromJsonList(Map<String, dynamic> json) {
    final evenementData = json['evenement'];
    final linksData = json['links'];

    final categorie = Categorie(
      id: evenementData['id_categorie'] as int? ?? 0,
      libelle: evenementData['categorie_libelle'] as String? ?? 'Non classé',
    );

    final String selfHref = linksData['self']['href'];
    final int id = int.tryParse(selfHref.split('/').last) ?? -1;

    return Evenement(
      id: id,
      titre: evenementData['titre'] ?? 'Titre inconnu',
      dateDebut: DateTime.parse(evenementData['date_debut']),
      categorie: categorie,
    );
  }

  void updateWithDetails(Map<String, dynamic> json) {
    final evenementData = json['evenement'];
    
    description = evenementData['description'];
    
    if (evenementData['images'] != null) {
      final List<dynamic> imagesJson = evenementData['images'];
      images = imagesJson.map((imgJson) => ImageEvenement.fromJson(imgJson)).toList();
    }
  }
}