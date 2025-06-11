class Categorie {
  final int id;
  final String libelle;
  final String? description;

  Categorie({required this.id, required this.libelle, this.description});

  factory Categorie.fromJson(Map<String, dynamic> categorieJson) {
    return Categorie(
      id: categorieJson['id_categorie'] as int? ?? 0,
      libelle: categorieJson['libelle'] as String? ?? 'Non classé',
      description: categorieJson['description'] as String?,
    );
  }
}