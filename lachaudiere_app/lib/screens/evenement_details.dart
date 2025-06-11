import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:lachaudiere_app/models/evenement.dart';

class EvenementDetails extends StatelessWidget {
  final Evenement evenement;

  const EvenementDetails({super.key, required this.evenement});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(evenement.titre)),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(evenement.titre, style: Theme.of(context).textTheme.headlineMedium),
            const SizedBox(height: 8),
            Chip(
              label: Text(evenement.categorie.libelle),
              backgroundColor: Theme.of(context).colorScheme.primaryContainer,
            ),
            const SizedBox(height: 16),
            _buildDateInfo(context),
            const Divider(height: 32),
            Text('Description', style: Theme.of(context).textTheme.headlineSmall),
            const SizedBox(height: 8),
            Text(
              evenement.description?.isNotEmpty == true ? evenement.description! : "Aucune description.",
              style: Theme.of(context).textTheme.bodyLarge,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDateInfo(BuildContext context) {
    final format = DateFormat.yMMMMEEEEd('fr_FR').add_Hm();
    String dateText = 'Le ${format.format(evenement.dateDebut)}';
    if (evenement.dateFin != null) {
      dateText = 'Du ${format.format(evenement.dateDebut)}\nau ${format.format(evenement.dateFin!)}';
    }
    return Text(dateText, style: Theme.of(context).textTheme.titleMedium);
  }
}