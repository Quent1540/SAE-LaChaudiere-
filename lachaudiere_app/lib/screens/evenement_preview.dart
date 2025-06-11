import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:lachaudiere_app/models/evenement.dart';

class EvenementPreview extends StatelessWidget {
  final Evenement evenement;
  final VoidCallback? onTap;

  const EvenementPreview({
    super.key,
    required this.evenement,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 8.0, vertical: 4.0),
      child: ListTile(
        title: Text(evenement.titre),
        subtitle: Text(
          '${evenement.categorie.libelle} • Début : ${DateFormat.yMd('fr_FR').format(evenement.dateDebut)}',
        ),
        trailing: const Icon(Icons.arrow_forward_ios, size: 16),
        onTap: onTap,
      ),
    );
  }
}