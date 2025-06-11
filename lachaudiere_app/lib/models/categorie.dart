class Categorie {
  final int id;
  final String libelle;
  final String? description;

  Categorie({required this.id, required this.libelle, this.description});

  factory Categorie.fromJson(Map<String, dynamic> json) {
    return Categorie(
      id: json['id_categorie'] as int? ?? 0,
      libelle: json['libelle'] as String? ?? 'Non classé',
      description: json['description'] as String?,
    );
  }
}